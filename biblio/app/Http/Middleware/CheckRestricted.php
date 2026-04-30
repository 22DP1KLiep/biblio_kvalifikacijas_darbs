<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRestricted
{
    public function handle(Request $request, Closure $next)
{
    $user = Auth::user();

    if ($user && $user->isRestricted()) {

        
        if (
            $request->is('/') ||
            $request->is('notifications*') ||
            $request->is('users*') ||
            $request->is('api/notifications*')
        ) {
            return $next($request);
        }

        
        return redirect('/')
            ->with('error', 'Tavs konts ir ierobežots.');
    }

    return $next($request);
}
}
