@extends('adminlte::page')

@section('title', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<h1>Seguridad / {{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
@stop

@section('content')
<div class="container-fluid">
    @if ($errors->any())
    <div class="alert alert-danger">
        Revisa los campos señalados antes de guardar.
    </div>
    @endif

    <form method="POST"
        action="{{ $usuario->exists
                  ? route('seguridad.usuarios.update', $usuario)
                  : route('seguridad.usuarios.store') }}">
        @csrf

        @if ($usuario->exists)
        @method('PUT')
        @endif

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Datos de acceso</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="dni">DNI</label>
                        <input id="dni" name="dni" maxlength="8" required
                            inputmode="numeric"
                            class="form-control @error('dni') is-invalid @enderror"
                            value="{{ old('dni', $usuario->dni) }}">
                        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8 form-group">
                        <label for="name">Nombre completo</label>
                        <input id="name" name="name" required
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $usuario->name) }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="email">Correo electrónico (opcional)</label>
                        <input id="email" name="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $usuario->email) }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="rol">Rol</label>
                        <select id="rol" name="rol" class="form-control">
                            <option value="visualizador"
                                @selected(old('rol', $usuario->rol ?: 'visualizador') === 'visualizador')>
                                Usuario con permisos asignados
                            </option>
                            <option value="administrador"
                                @selected(old('rol', $usuario->rol) === 'administrador')>
                                Administrador
                            </option>
                        </select>
                        @error('rol') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="password">
                            {{ $usuario->exists ? 'Nueva contraseña (opcional)' : 'Contraseña' }}
                        </label>
                        <input id="password" name="password" type="password"
                            autocomplete="new-password"
                            class="form-control @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input id="password_confirmation" name="password_confirmation"
                            type="password" autocomplete="new-password"
                            class="form-control">
                    </div>
                </div>

                <div class="form-check mb-2">
                    <input id="activo" name="activo" type="checkbox" value="1"
                        class="form-check-input"
                        @checked((bool) old('activo', $usuario->exists ? $usuario->activo : true))>
                    <label for="activo" class="form-check-label">Cuenta activa</label>
                </div>

                @if ($usuario->exists)
                <div class="form-check">
                    <input id="cambiar_password" name="cambiar_password"
                        type="checkbox" value="1" class="form-check-input"
                        @checked((bool) old('cambiar_password', $usuario->cambiar_password))>
                    <label for="cambiar_password" class="form-check-label">
                        Solicitar cambio de contraseña
                    </label>
                </div>
                @endif
            </div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Permisos por módulo</h3>
            </div>

            <div class="card-body">
                <p class="text-muted">
                    El administrador tiene acceso general. Para otros usuarios,
                    marca «Ver» o «Gestionar» en cada módulo.
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Módulo</th>
                                <th class="text-center">Ver</th>
                                <th class="text-center">Gestionar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modulos as $clave => $nombre)
                            <tr>
                                <td>{{ $nombre }}</td>
                                <td class="text-center">
                                    <input type="checkbox"
                                        name="permisos[{{ $clave }}][ver]"
                                        value="1"
                                        @checked((bool) old( "permisos.$clave.ver" ,
                                        $permisosActuales[$clave]['ver'] ?? false
                                        ))>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox"
                                        name="permisos[{{ $clave }}][gestionar]"
                                        value="1"
                                        @checked((bool) old( "permisos.$clave.gestionar" ,
                                        $permisosActuales[$clave]['gestionar'] ?? false
                                        ))>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer text-right">
                <a href="{{ route('seguridad.usuarios.index') }}"
                    class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Guardar usuario
                </button>
            </div>
        </div>
    </form>
</div>
@stop