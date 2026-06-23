<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipType;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Translation\Message;

class MemberController extends Controller
{
    /**
     * Display a listing of members for the authenticated gym.
     */
    public function index(Request $request): JsonResponse
    {
        $gymId = $request->user()->id;

        $query = Member::where('gym_id', $gymId)
            ->with('membershipType:id,name,duration_days');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('unique_code', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('created_at', 'desc')->get();

        return response()->json($members);
    }

    /**
     * Get members expiring soon (within 7 days).
     */
    public function expiring(Request $request): JsonResponse
    {
        $gymId = $request->user()->id;
        $daysAhead = intval($request->input('days', 7));

        $members = Member::where('gym_id', $gymId)
            ->where('status', 'active')
            ->whereBetween('expiry_date', [
                now()->toDateString(),
                now()->addDays($daysAhead)->toDateString(),
            ])
            ->with('membershipType:id,name,duration_days')
            ->orderBy('expiry_date', 'asc')
            ->get();

        return response()->json($members);
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'membership_type_id' => 'required|exists:membership_types,id',
            'start_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $gymId = $request->user()->id;

        // Verify membership type belongs to this gym
        $membershipType = MembershipType::where('id', $validated['membership_type_id'])
            ->where('gym_id', $gymId)
            ->firstOrFail();

        // Calculate expiry date - ensure duration_days is an integer
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $durationDays = intval($membershipType->duration_days);
        $expiryDate = $startDate->copy()->addDays($durationDays);

        // Handle photo upload — name the file after the member for easy identification
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = Str::slug($validated['full_name']) . '-' . Str::random(6)
                . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('members', $filename, 'public');
        }

        // Generate unique code (5 characters)
        $uniqueCode = User::generateUniqueCode($gymId);

        // Generate slug
        $slug = Str::slug($validated['full_name']) . '-' . Str::random(6). '-' . Str::random(4);

        $member = Member::create([
            'gym_id' => $gymId,
            'membership_type_id' => $validated['membership_type_id'],
            'slug' => $slug,
            'unique_code' => $uniqueCode,
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'photo_path' => $photoPath,
            'gender' => $validated['gender'] ?? null,
            'start_date' => $validated['start_date'],
            'expiry_date' => $expiryDate->toDateString(),
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        $member->load('membershipType:id,name,duration_days');

        return response()->json([
            'message' => Message::get('member_created'),
            'data' => $member,
        ], 201);
    }

    /**
     * Display the specified member.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $member = Member::where('gym_id', $request->user()->id)
            ->with('membershipType:id,name,duration_days')
            ->findOrFail($id);

        return response()->json($member);
    }

    /**
     * Update the specified member.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $member = Member::where('gym_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'membership_type_id' => 'sometimes|required|exists:membership_types,id',
            'start_date' => 'sometimes|required|date',
            'gender' => 'nullable|in:male,female,other',
            'status' => 'sometimes|in:active,expired,suspended',
            'notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $gymId = $request->user()->id;

        // If membership type changed, verify it belongs to this gym and recalculate expiry
        if (isset($validated['membership_type_id'])) {
            $membershipType = MembershipType::where('id', $validated['membership_type_id'])
                ->where('gym_id', $gymId)
                ->firstOrFail();

            $startDate = isset($validated['start_date'])
                ? \Carbon\Carbon::parse($validated['start_date'])
                : \Carbon\Carbon::parse($member->start_date);

            $durationDays = intval($membershipType->duration_days);
            $validated['expiry_date'] = $startDate->copy()
                ->addDays($durationDays)
                ->toDateString();
        } elseif (isset($validated['start_date'])) {
            // If only start date changed, recalculate expiry with existing membership type
            $member->load('membershipType');
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $durationDays = intval($member->membershipType->duration_days);
            $validated['expiry_date'] = $startDate->copy()
                ->addDays($durationDays)
                ->toDateString();
        }

        // Handle photo upload — name the file after the member for easy identification
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($member->photo_path) {
                \Storage::disk('public')->delete($member->photo_path);
            }
            $photo = $request->file('photo');
            $name = $validated['full_name'] ?? $member->full_name;
            $filename = Str::slug($name) . '-' . Str::random(6)
                . '.' . $photo->getClientOriginalExtension();
            $validated['photo_path'] = $photo->storeAs('members', $filename, 'public');
        }

        $member->update($validated);
        $member->load('membershipType:id,name,duration_days');

        return response()->json([
            'message' => Message::get('member_updated'),
            'data' => $member,
        ]);
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $member = Member::where('gym_id', $request->user()->id)
            ->findOrFail($id);

        // Delete photo if exists
        if ($member->photo_path) {
            \Storage::disk('public')->delete($member->photo_path);
        }

        $member->delete();

        return response()->json([
            'message' => Message::get('member_deleted'),
        ]);
    }

    /**
     * Get card data for a specific member.
     */
    public function cardData(Request $request, int $gym, int $member): JsonResponse
    {
        $memberModel = Member::where('id', $member)
            ->where('gym_id', $gym)
            ->firstOrFail();

        if ($memberModel->gym_id !== $request->user()->id) {
            abort(403);
        }

        $memberModel->load('gym', 'membershipType');

        return response()->json([
            'member' => [
                'name'            => $memberModel->full_name,
                'photo_url'       => $memberModel->photo_path
                    ? asset('storage/' . $memberModel->photo_path)
                    : null,
                'code'            => $memberModel->unique_code,
                'slug'            => $memberModel->slug,
                'phone'           => $memberModel->phone,
                'membership_type' => $memberModel->membershipType->name,
                'start_date'      => $memberModel->start_date->format('M d, Y'),
                'expiry_date'     => $memberModel->expiry_date->format('M d, Y'),
            ],
            'gym' => [
                'name'     => $memberModel->gym->name,
                'logo_url' => $memberModel->gym->logo_path
                    ? asset('storage/' . $memberModel->gym->logo_path)
                    : null,
            ],
        ]);
    }

    /**
     * Generate a unique 5-character code.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(5));
        } while (Member::where('unique_code', $code)->exists());

        return $code;
    }

    /**
     * Generate a PDF for a single member's card.
     */
    public function cardPdf(Request $request, int $gym, int $member): HttpResponse
    {
        $memberModel = Member::where('id', $member)
            ->where('gym_id', $gym)
            ->firstOrFail();

        if ($memberModel->gym_id !== $request->user()->id) {
            abort(403);
        }

        $memberModel->load('gym', 'membershipType');

        $data = $this->resolveCardImages($memberModel);

        $pdf = Pdf::loadView('cards.member-card', array_merge($data, ['member' => $memberModel]));
        $pdf->setPaper([0, 0, 153.09, 242.55], 'portrait');

        $filename = Str::slug($memberModel->full_name) . '-membership-card.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate a batch PDF for multiple members' cards.
     */
    public function batchCardsPdf(Request $request, int $gym): HttpResponse
    {
        $validated = $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'integer',
        ]);

        $members = Member::whereIn('id', $validated['member_ids'])
            ->where('gym_id', $gym)
            ->with(['gym', 'membershipType'])
            ->get();

        if ($members->isEmpty()) {
            abort(422, 'No valid members found.');
        }

        $membersData = $members->map(function (Member $m) {
            return array_merge($this->resolveCardImages($m), ['member' => $m]);
        });

        $pdf = Pdf::loadView('cards.batch-cards', ['members' => $membersData]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('membership-cards.pdf');
    }

    /**
     * Fetch images (logo, photo, QR) as base64 data URIs.
     */
    private function resolveCardImages(Member $member): array
    {
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($member->slug) . '&size=300x300';
        $qrResponse = Http::timeout(10)->get($qrUrl);
        $qrDataUri = $qrResponse->successful()
            ? 'data:image/png;base64,' . base64_encode($qrResponse->body())
            : null;

        $logoDataUri = null;
        if ($member->gym->logo_path) {
            $path = storage_path('app/public/' . $member->gym->logo_path);
            if (file_exists($path)) {
                $mime = mime_content_type($path);
                $logoDataUri = "data:{$mime};base64," . base64_encode(file_get_contents($path));
            }
        }

        $photoDataUri = null;
        if ($member->photo_path) {
            $path = storage_path('app/public/' . $member->photo_path);
            if (file_exists($path)) {
                $mime = mime_content_type($path);
                $photoDataUri = "data:{$mime};base64," . base64_encode(file_get_contents($path));
            }
        }

        return compact('qrDataUri', 'logoDataUri', 'photoDataUri');
    }

    /**
     * Get member statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $gymId = $request->user()->id;

        $stats = [
            'total' => Member::where('gym_id', $gymId)->count(),
            'active' => Member::where('gym_id', $gymId)->where('status', 'active')->count(),
            'expired' => Member::where('gym_id', $gymId)->where('status', 'expired')->count(),
            'suspended' => Member::where('gym_id', $gymId)->where('status', 'suspended')->count(),
            'expiring_soon' => Member::where('gym_id', $gymId)
                ->where('status', 'active')
                ->whereBetween('expiry_date', [
                    now()->toDateString(),
                    now()->addDays(7)->toDateString(),
                ])
                ->count(),
        ];

        return response()->json($stats);
    }
}
