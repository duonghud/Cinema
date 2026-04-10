<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('admin_auth')) {
            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
