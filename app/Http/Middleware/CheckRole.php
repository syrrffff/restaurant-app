<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Admin memiliki akses ke SEMUA halaman
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 3. Cek apakah role user ada di dalam daftar role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Jika tidak punya akses, lempar ke halaman yang sesuai role-nya
        // atau tampilkan pesan error
        return abort(404, 'Page Not Found.');
    }
}
