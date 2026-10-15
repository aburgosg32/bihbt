@extends('adminlte::page')

@section('title', 'Seguridad | Usuarios')

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
@stop

@section('content_header')
<h1>Seguridad / Usuarios</h1>
@stop

@section('content')
<div class="container-fluid">
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Usuarios del sistema</h3>

            <a href="{{ route('seguridad.usuarios.create') }}"
                class="btn btn-primary ml-auto">
                <i class="fas fa-user-plus mr-1"></i> Nuevo usuario
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Accesos</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->dni }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ ucfirst($usuario->rol) }}</td>
                        <td>
                            <span class="badge badge-{{ $usuario->activo ? 'success' : 'secondary' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            {{ $usuario->esAdministrador()
                                        ? 'Todos los módulos'
                                        : $usuario->permisosModulos->count() . ' módulo(s)' }}
                        </td>
                        <td class="text-right">
                            <a href="{{ route('seguridad.usuarios.edit', $usuario) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@stop