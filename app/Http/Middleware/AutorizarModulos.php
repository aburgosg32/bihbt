<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutorizarModulos
{
    private const MODULOS = [
        'consulta' => 'consulta',
        'emergencia' => 'emergencia',
        'hospitalizacion' => 'hospitalizacion',
        'radiologia' => 'radiologia',
        'camas' => 'camas',
        'procedimientos' => 'procedimientos',
        'quirofano' => 'quirofano',
        'seguros' => 'seguros',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ruta = $request->route();
        $nombre = $ruta?->getName();

        $usuario = $request->user();

        // Una cuenta desactivada pierde el acceso aunque tenga una sesión abierta.
        if ($usuario && ! $usuario->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'dni' => 'Tu cuenta se encuentra inactiva.',
                ]);
        }

        if (! $nombre) {
            return $next($request);
        }

        $prefijo = explode('.', $nombre, 2)[0];
        $modulo = self::MODULOS[$prefijo] ?? null;

        // Inicio, login, perfil y Seguridad conservan sus reglas propias.
        if ($modulo === null) {
            return $next($request);
        }

        // Protege incluso módulos que aún no tienen auth en web.php.
        if (! $usuario) {
            return redirect()->guest(route('login'));
        }

        abort_unless($usuario->puedeVerModulo($modulo), 403);

        // GET y HEAD consultan información; los otros métodos modifican datos.
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            abort_unless($usuario->puedeGestionarModulo($modulo), 403);
        }

        return $next($request);
    }
}
