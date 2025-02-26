<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $requiredType)
    {
        // Obtém o usuário autenticado
        $user = $request->user();

        // Verifica se o tipo de usuário corresponde ao necessário
        if ($user && $user->tipo_usuario_id == $requiredType) {
            return $next($request);
        }

        // Retorna erro 403 (Acesso negado) se o tipo não corresponder
        return response()->json(['error' => 'Acesso negado'], 403);
    }
}
