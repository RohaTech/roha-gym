<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Helper\Response\Response;
use Translation\Message;

class GymProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return Response::_200($request->user());
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone'   => 'sometimes|required|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'sometimes|required|string|max:500',
            'logo'    => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($user->logo_path) {
                Storage::disk('public')->delete($user->logo_path);
            }
            $logo     = $request->file('logo');
            $gymName  = $validated['name'] ?? $user->name;
            $filename = Str::slug($gymName) . '-logo-' . Str::random(6) . '.' . $logo->getClientOriginalExtension();
            $validated['logo_path'] = $logo->storeAs('logos', $filename, 'public');
        }

        unset($validated['logo']);

        $user->update($validated);

        return Response::_200([
            'message' => Message::get('gym_profile_updated'),
            'data'    => $user->fresh(),
        ]);
    }
}
