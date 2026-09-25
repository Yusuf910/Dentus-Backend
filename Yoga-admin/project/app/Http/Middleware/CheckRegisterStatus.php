<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRegisterStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if(Auth::check() && (Auth::user()->status==0)){
            $token = $request->user()->token();
            $token->revoke();
            return response()->json(['status' => false,"activestatus"=>"0", 'msg' => 'Your account is inactive. Please contact our customer support for more information.'], 422);
        }

        if(Auth::check() && (Auth::user()->status==0)){
            $token = $request->user()->token();
            $token->revoke();
            return response()->json(['status' => false,"activestatus"=>"0", 'msg' => 'Your account not verified. Please verify your account or contact with admin.'], 422);
        }

        return $response;
    }
}
