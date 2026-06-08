<?php
// app/Http/Middleware/CheckRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Kiểm tra role
     * @param Request $request
     * @param Closure $next
     * @param string $role
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $admin = $request->session()->get('admin_auth');

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
