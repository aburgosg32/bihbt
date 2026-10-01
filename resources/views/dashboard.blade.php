@extends('adminlte::page')

@section('title', 'Bienvenida | Hospital Belén de Trujillo')
@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
@stop

@section('content_header')
<h1>Bienvenida</h1>
@stop

@section('content')
<div class="container-fluid pt-3">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="mr-3">
                            <i class="fas fa-hospital fa-3x text-primary"></i>
                        </div>

                        <div>
                            <div class="text-primary font-weight-bold mb-1">
                                HOSPITAL BELÉN DE TRUJILLO
                            </div>

                            <h2 class="mb-0 font-weight-bold">
                                Bienvenido, {{ auth()->user()->name }}
                            </h2>
                        </div>
                    </div>

                    <p class="lead mb-4">
                        Bienvenido al Sistema Trazabilidad Hospitalario del Hospital Belén
                        de Trujillo.
                    </p>

                    <div class="callout callout-info mb-4">
                        <h5 class="font-weight-bold mb-2">
                            Oficina de Estadística e Informática
                        </h5>

                        <p class="mb-0">
                            Área de Informática HBT
                        </p>
                    </div>

                    <p class="text-muted mb-0">
                        Para comenzar, selecciona un módulo en el menú ubicado
                        a la izquierda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop