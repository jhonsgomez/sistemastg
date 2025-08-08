<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Verifica si el usuario autenticado tiene el permiso
        if (!Auth::user() || !Auth::user()->can($permission)) {
            // Si no tiene el permiso, redirige a la raíz
            return redirect('/');
        }

        // Si tiene el permiso, permite la ejecución de la solicitud
        return $next($request);
    }
}
