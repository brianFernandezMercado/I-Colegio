<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Devuelve un error JSON 401 si no está autenticado y no es petición JSON
            abort(response()->json(['message' => 'No autenticado.'], 401));
        }
    }
}
