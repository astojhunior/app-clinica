<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->empleado || !$user->empleado->cargo) {
            abort(403);
        }

        $descripcion = strtolower($user->empleado->cargo->DescripcionCargo);
        $roles       = array_map('strtolower', $roles);

        if (! in_array($descripcion, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
