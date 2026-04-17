<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomerLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('customer')) {
            return redirect()
                // Chuyển đúng về form login cútomer neu chưa có sessopn đăng nhập
                ->route('auth.customerLogin')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }

        return $next($request);
    }
}
