<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class GymScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $gymRoute = $request->route('gym');
        $gym = $gymRoute instanceof User ? $gymRoute : User::findOrFail($gymRoute);

        if ($gym->owner_id !== auth()->id()) {
            abort(403);
        }

        $request->gym = $gym;

        return $next($request);
    }
}
