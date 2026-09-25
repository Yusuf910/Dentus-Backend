<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // foreach ($roles as $role) {
        //     if(auth()->user()->hasRole($role)) {
        //         return $next($request);
        //     }
        // }
        
        // abort(404);
        if (auth()->user()->IsSuper()) {
            return $next($request);
        }
        else {
            foreach ($roles as $role) {
                if(auth()->user()->hasPermission($role)) {
                    return $next($request);
                }
            }
            abort(401);
        }
    }
}
