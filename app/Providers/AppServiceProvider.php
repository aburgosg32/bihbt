<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Paginator::useBootstrapFour();
        // Dentro de boot():
        Gate::define(
            'administrar-seguridad',
            fn(User $user): bool => $user->activo && $user->esAdministrador()
        );
        Gate::define(
            'ver-consulta',
            fn(User $user): bool => $user->puedeVerModulo('consulta')
        );

        Gate::define(
            'gestionar-consulta',
            fn(User $user): bool => $user->puedeGestionarModulo('consulta')
        );
        foreach (
            [
                'emergencia',
                'hospitalizacion',
                'radiologia',
                'camas',
                'procedimientos',
                'quirofano',
                'seguros',
            ] as $modulo
        ) {
            Gate::define(
                "ver-$modulo",
                fn(User $user): bool => $user->puedeVerModulo($modulo)
            );

            Gate::define(
                "gestionar-$modulo",
                fn(User $user): bool => $user->puedeGestionarModulo($modulo)
            );
        }
    }
}
