<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    private const MODULOS = [
        'consulta' => 'Consulta Externa',
        'emergencia' => 'Emergencia',
        'hospitalizacion' => 'Hospitalización',
        'radiologia' => 'Radiología',
        'camas' => 'Gestión de Camas',
        'procedimientos' => 'Procedimientos',
        'quirofano' => 'Sala de Operaciones',
        'seguros' => 'Seguros',
    ];

    public function index(): View
    {
        return view('seguridad.usuarios.index', [
            'usuarios' => User::with('permisosModulos')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('seguridad.usuarios.form', [
            'usuario' => new User(),
            'modulos' => self::MODULOS,
            'permisosActuales' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos) {
            $usuario = User::create([
                'dni' => $datos['dni'],
                'name' => $datos['name'],
                'email' => $datos['email'] ?? null,
                'password' => $datos['password'],
                'rol' => $datos['rol'],
                'activo' => (bool) ($datos['activo'] ?? false),
                'cambiar_password' => true,
            ]);

            $this->guardarPermisos($usuario, $datos['permisos'] ?? []);
        });

        return redirect()
            ->route('seguridad.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        return view('seguridad.usuarios.form', [
            'usuario' => $usuario,
            'modulos' => self::MODULOS,
            'permisosActuales' => $usuario->permisosModulos
                ->keyBy('modulo')
                ->toArray(),
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $this->validar($request, $usuario);

        if (
            $usuario->is($request->user())
            && ($datos['rol'] !== 'administrador' || ! ($datos['activo'] ?? false))
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'rol' => 'No puedes quitarte el rol de administrador ni desactivar tu propia cuenta.',
                ]);
        }

        DB::transaction(function () use ($usuario, $datos) {
            $usuario->fill([
                'dni' => $datos['dni'],
                'name' => $datos['name'],
                'email' => $datos['email'] ?? null,
                'rol' => $datos['rol'],
                'activo' => (bool) ($datos['activo'] ?? false),
                'cambiar_password' => (bool) ($datos['cambiar_password'] ?? false),
            ]);

            if (! empty($datos['password'])) {
                $usuario->password = $datos['password'];
            }

            $usuario->save();

            $this->guardarPermisos($usuario, $datos['permisos'] ?? []);
        });

        return redirect()
            ->route('seguridad.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    private function validar(Request $request, ?User $usuario = null): array
    {
        $modulos = array_keys(self::MODULOS);

        return $request->validate([
            'dni' => [
                'required',
                'digits:8',
                Rule::unique('users', 'dni')->ignore($usuario?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario?->id),
            ],
            'password' => [
                $usuario ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'rol' => ['required', Rule::in(['administrador', 'visualizador'])],
            'activo' => ['nullable', 'boolean'],
            'cambiar_password' => ['nullable', 'boolean'],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['array'],
            'permisos.*.ver' => ['nullable', 'boolean'],
            'permisos.*.gestionar' => ['nullable', 'boolean'],
        ] + collect($modulos)->mapWithKeys(
            fn($modulo) => ["permisos.$modulo" => ['nullable', 'array']]
        )->all());
    }

    private function guardarPermisos(User $usuario, array $permisos): void
    {
        $usuario->permisosModulos()->delete();

        if ($usuario->esAdministrador()) {
            return;
        }

        foreach (self::MODULOS as $clave => $nombre) {
            $ver = (bool) ($permisos[$clave]['ver'] ?? false);
            $gestionar = (bool) ($permisos[$clave]['gestionar'] ?? false);

            if (! $ver && ! $gestionar) {
                continue;
            }

            $usuario->permisosModulos()->create([
                'modulo' => $clave,
                'ver' => $ver || $gestionar,
                'gestionar' => $gestionar,
            ]);
        }
    }
}
