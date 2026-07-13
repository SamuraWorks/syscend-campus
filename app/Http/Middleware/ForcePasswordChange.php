<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user && $user->force_password_change && !$request->is('profile/password', 'profile/password/*')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You must change your password.'], 403);
            }
            return redirect()->route('password.change')->with('warning', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}
