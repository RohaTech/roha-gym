<?php

namespace App\Http\Controllers;

use App\Models\MembershipType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Translation\Message;

class MembershipTypeController extends Controller
{
    /**
     * Display a listing of membership types for the authenticated gym.
     */
    public function index(Request $request): JsonResponse
    {
        $gymId = $request->user()->id;

        $membershipTypes = MembershipType::where('gym_id', $gymId)
            ->withCount('members')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($membershipTypes);
    }

    /**
     * Store a newly created membership type.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'allowed_checkins_per_day' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        $membershipType = MembershipType::create([
            'gym_id' => $request->user()->id,
            'name' => $validated['name'],
            'duration_days' => $validated['duration_days'],
            'allowed_checkins_per_day' => $validated['allowed_checkins_per_day'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'message' => Message::get('membership_type_created'),
            'data' => $membershipType,
        ], 201);
    }

    /**
     * Display the specified membership type.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $membershipType = MembershipType::where('gym_id', $request->user()->id)
            ->withCount('members')
            ->findOrFail($id);

        return response()->json($membershipType);
    }

    /**
     * Update the specified membership type.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $membershipType = MembershipType::where('gym_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'duration_days' => 'sometimes|required|integer|min:1',
            'allowed_checkins_per_day' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        $membershipType->update($validated);

        return response()->json([
            'message' => Message::get('membership_type_updated'),
            'data' => $membershipType,
        ]);
    }

    /**
     * Remove the specified membership type.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $membershipType = MembershipType::where('gym_id', $request->user()->id)
            ->findOrFail($id);

        // Check if there are active members using this membership type
        $activeMembersCount = $membershipType->members()
            ->where('status', 'active')
            ->count();

        if ($activeMembersCount > 0) {
            return response()->json([
                'message' => Message::get('membership_type_has_active_members'),
            ], 422);
        }

        $membershipType->delete();

        return response()->json([
            'message' => Message::get('membership_type_deleted'),
        ]);
    }
}
