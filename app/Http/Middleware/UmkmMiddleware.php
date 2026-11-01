<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UmkmMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isUmkm() && !Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk Mitra UMKM.');
        }

        return $next($request);
    }
}

