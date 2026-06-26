<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Translation\Message;

class GymController extends Controller
{
    /**
     * List all gyms (gym-owner users) with member counts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'user')
            ->withCount([
                'members',
                'members as active_members_count' => fn ($q) => $q->where('status', 'active'),
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }

        $gyms = $query->orderBy('created_at', 'desc')
            ->paginate((int) $request->input('per_page', 15));

        $gyms->getCollection()->transform(fn ($gym) => $this->summarize($gym));

        return response()->json($gyms);
    }

    /**
     * Show a single gym with detailed stats.
     */
    public function show(int $id): JsonResponse
    {
        $gym = User::where('role', 'user')->findOrFail($id);

        return response()->json([
            'gym'   => $this->summarize($gym),
            'stats' => [
                'total_members'    => Member::where('gym_id', $gym->id)->count(),
                'active_members'   => Member::where('gym_id', $gym->id)->where('status', 'active')->count(),
                'expired_members'  => Member::where('gym_id', $gym->id)->where('status', 'expired')->count(),
                'membership_types' => $gym->membershipTypes()->count(),
                'checkins_today'   => Attendance::where('gym_id', $gym->id)
                                        ->whereDate('checked_in_at', today())->count(),
            ],
            'recent_members' => Member::where('gym_id', $gym->id)
                ->latest()
                ->take(10)
                ->get()
                ->map(fn ($m) => [
                    'id'          => $m->id,
                    'full_name'   => $m->full_name,
                    'phone'       => $m->phone,
                    'status'      => $m->status,
                    'photo'       => $m->photo_path ? asset('storage/' . $m->photo_path) : null,
                    'expiry_date' => $m->expiry_date?->format('M d, Y'),
                ]),
        ]);
    }

    /**
     * Create a new gym (gym-owner user).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8',
            'address'  => 'required|string|max:500',
            'logo'     => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $gym = User::create([
            'name'      => $validated['name'],
            'phone'     => $validated['phone'],
            'password'  => Hash::make($validated['password']),
            'address'   => $validated['address'],
            'status'    => USER_STATUS_PENDING, // Awaits admin approval before going active
            'logo_path' => $logoPath,
            'role'      => 'user',
        ]);

        return response()->json([
            'message' => Message::get('gym_created'),
            'data'    => $this->summarize($gym),
        ], 201);
    }

    /**
     * Update a gym's profile details.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $gym = User::where('role', 'user')->findOrFail($id);

        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'phone'    => "sometimes|required|string|max:20|unique:users,phone,{$gym->id}",
            'address'  => 'sometimes|required|string|max:500',
            'password' => 'sometimes|nullable|string|min:8',
            'logo'     => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($gym->logo_path) {
                Storage::disk('public')->delete($gym->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['logo']);
        $gym->update($validated);

        return response()->json([
            'message' => Message::get('gym_updated'),
            'data'    => $this->summarize($gym->fresh()),
        ]);
    }

    /**
     * Suspend or activate a gym.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $gym = User::where('role', 'user')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:' . USER_STATUS_ACTIVE . ',' . USER_STATUS_INACTIVE,
        ]);

        $gym->update(['status' => (int) $validated['status']]);

        return response()->json([
            'message' => Message::get('gym_updated'),
            'data'    => $this->summarize($gym->fresh()),
        ]);
    }

    /**
     * Delete a gym (cascades to members, types, attendance).
     */
    public function destroy(int $id): JsonResponse
    {
        $gym = User::where('role', 'user')->findOrFail($id);

        if ($gym->logo_path) {
            Storage::disk('public')->delete($gym->logo_path);
        }

        $gym->delete();

        return response()->json([
            'message' => Message::get('gym_deleted'),
        ]);
    }

    /**
     * Shape a gym user into a consistent summary payload.
     */
    private function summarize(User $gym): array
    {
        return [
            'id'             => $gym->id,
            'name'           => $gym->name,
            'phone'          => $gym->phone,
            'address'        => $gym->address,
            'status'         => (int) $gym->status,
            'logo'           => $gym->logo_path ? asset('storage/' . $gym->logo_path) : null,
            'members_count'  => $gym->members_count ?? $gym->members()->count(),
            'active_members_count' => $gym->active_members_count
                ?? $gym->members()->where('status', 'active')->count(),
            'created_at'     => $gym->created_at?->format('M d, Y'),
        ];
    }
}
