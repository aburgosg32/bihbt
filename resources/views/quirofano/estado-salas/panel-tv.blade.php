<!DOCTYPE html>

<html lang="es">



<head>

    <meta charset="UTF-8">



    <meta

        name="viewport"

        content="width=device-width, initial-scale=1.0">



    <meta http-equiv="refresh" content="30">



    <title>Estado de Salas de Operaciones</title>



    <link

        rel="stylesheet"

        href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">



    <style>
        * {

            box-sizing: border-box;

        }



        html,

        body {

            width: 100%;

            height: 100%;

            margin: 0;

            padding: 0;

            overflow: hidden;

        }



        body {

            display: flex;

            flex-direction: column;

            padding: 7px;

            background:
                radial-gradient(circle at top left, rgba(21, 151, 212, .16), transparent 32%),
                linear-gradient(135deg, #05263a 0%, #073b5c 100%);

            color: #102a43;

            font-family: Arial, Helvetica, sans-serif;

        }



        .tv-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            flex: 0 0 88px;

            padding: 4px 8px 10px;

            border-bottom: 2px solid rgba(255, 255, 255, .22);

            color: #ffffff;

        }



        .tv-header-left {

            display: flex;

            align-items: center;

            min-width: 0;

        }



        .institutional-logos {

            display: flex;

            align-items: center;

            justify-content: center;

            flex: 0 0 auto;

            margin-right: 14px;

            padding: 6px 10px;

            border: 1px solid rgba(255, 255, 255, .55);

            border-radius: 9px;

            background: #ffffff;

            box-shadow: 0 3px 10px rgba(0, 0, 0, .18);

        }



        .institutional-logo {

            display: block;

            width: auto;

            max-width: 150px;

            height: 48px;

            object-fit: contain;

        }



        .institutional-logo.gerencia {

            max-width: 125px;

        }



        .logo-divider {

            width: 1px;

            height: 38px;

            margin: 0 9px;

            background: #bfd4e3;

        }



        .tv-title {

            margin: 0;

            font-size: 28px;

            font-weight: 700;

            line-height: 1.05;

        }



        .tv-subtitle {

            margin-top: 6px;

            font-size: 13px;

        }



        .tv-clock {

            flex: 0 0 auto;

            padding-left: 20px;

            text-align: right;

        }



        .tv-clock-time {

            font-size: 33px;

            font-weight: 700;

            line-height: 1;

        }



        .tv-clock-date {

            margin-top: 6px;

            font-size: 13px;

        }



        .rooms-grid {

            display: grid;

            grid-template-columns: repeat(3, minmax(0, 1fr));

            grid-template-rows: repeat(2, minmax(0, 1fr));

            flex: 1 1 auto;

            gap: 12px;

            min-height: 0;

            padding-top: 12px;

        }



        .room-card {

            display: flex;

            flex-direction: column;

            min-width: 0;

            min-height: 0;

            overflow: hidden;

            border-top: 7px solid #18a84b;

            border-radius: 10px;

            border: 1px solid rgba(113, 169, 202, .55);

            background: linear-gradient(145deg, #f1f9fd 0%, #e3f2fa 100%);

            box-shadow: 0 5px 14px rgba(0, 18, 35, .22);

        }



        .room-card.en-uso {

            border-top-color: #e6354b;

        }



        .room-card.preparacion {

            border-top-color: #f39c12;

        }



        .room-card.limpieza {

            border-top-color: #17a2b8;

        }



        .room-card.inoperativa {

            border-top-color: #6c757d;

        }



        .room-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            flex: 0 0 auto;

            min-height: 60px;

            padding: 10px 13px 8px;

            border-bottom: 1px solid #d7e0e7;

            background: rgba(213, 237, 249, .75);

        }



        .room-header-info {

            min-width: 0;

            padding-right: 10px;

        }



        .room-name {

            margin: 0;

            overflow: hidden;

            color: #075a94;

            font-size: 18px;

            font-weight: 700;

            line-height: 1.15;

            text-overflow: ellipsis;

            white-space: nowrap;

        }



        .room-code {

            margin-top: 5px;

            color: #607d8b;

            font-size: 10px;

            text-transform: uppercase;

        }



        .status-badge {

            flex: 0 0 auto;

            padding: 8px 12px;

            border-radius: 18px;

            background: #18a84b;

            color: #ffffff;

            font-size: 12px;

            font-weight: 700;

            white-space: nowrap;

        }



        .status-badge.en-uso {

            background: #e6354b;

        }



        .status-badge.preparacion {

            background: #f39c12;

        }



        .status-badge.limpieza {

            background: #17a2b8;

        }



        .status-badge.inoperativa {

            background: #6c757d;

        }



        .room-body {

            display: flex;

            flex: 1 1 auto;

            flex-direction: column;

            min-height: 0;

            padding: 10px 13px 8px;

        }



        .patient-name {

            margin-bottom: 3px;

            overflow: hidden;

            color: #102a43;

            font-size: 17px;

            font-weight: 700;

            line-height: 1.15;

            text-overflow: ellipsis;

            white-space: nowrap;

        }



        .patient-account {

            margin-bottom: 10px;

            color: #607d8b;

            font-size: 10px;

        }



        .detail-label {

            margin-bottom: 2px;

            color: #607d8b;

            font-size: 10px;

            text-transform: uppercase;

        }



        .detail-value {

            margin-bottom: 8px;

            overflow: hidden;

            color: #17324d;

            font-size: 12px;

            line-height: 1.2;

            text-overflow: ellipsis;

            white-space: nowrap;

        }



        .time-boxes {

            display: grid;

            grid-template-columns: repeat(3, minmax(0, 1fr));

            gap: 6px;

            margin-top: auto;

        }



        .time-box {

            min-width: 0;

            padding: 7px 5px;

            border-radius: 7px;

            border: 1px solid #c7dfed;

            background: rgba(255, 255, 255, .72);

            text-align: center;

        }



        .time-label {

            overflow: hidden;

            color: #607d8b;

            font-size: 9px;

            text-overflow: ellipsis;

            text-transform: uppercase;

            white-space: nowrap;

        }



        .time-value {

            margin-top: 3px;

            color: #102a43;

            font-size: 14px;

            font-weight: 700;

        }



        .empty-room {

            display: flex;

            align-items: center;

            justify-content: center;

            flex: 1 1 auto;

            flex-direction: column;

            min-height: 0;

            text-align: center;

        }



        .empty-room-icon {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 45px;

            height: 45px;

            margin-bottom: 12px;

            border-radius: 50%;

            background: #18a84b;

            color: #ffffff;

            font-size: 28px;

        }



        .empty-room-icon.inoperativa {

            background: #6c757d;

        }



        .empty-room-title {

            color: #0b9f3e;

            font-size: 18px;

            font-weight: 700;

        }



        .empty-room-title.inoperativa {

            color: #6c757d;

        }



        .empty-room-text {

            margin-top: 5px;

            color: #607d8b;

            font-size: 12px;

        }



        .cleaning-content {

            display: flex;

            align-items: center;

            justify-content: center;

            flex: 1 1 auto;

            flex-direction: column;

            text-align: center;

        }



        .cleaning-icon {

            margin-bottom: 12px;

            color: #17a2b8;

            font-size: 45px;

        }



        .cleaning-title {

            color: #138496;

            font-size: 20px;

            font-weight: 700;

        }



        .cleaning-text {

            margin-top: 6px;

            color: #607d8b;

            font-size: 12px;

        }



        .next-operation {

            margin-top: 12px;

            padding: 8px 10px;

            border-radius: 7px;

            background: #edf5fa;

            color: #456477;

            font-size: 11px;

            text-align: center;

        }



        .next-operation strong {

            color: #17324d;

        }



        .tv-footer {

            flex: 0 0 18px;

            padding-top: 5px;

            color: #8ab4cf;

            font-size: 9px;

            text-align: right;

        }



        @media (max-width: 1200px) {

            .institutional-logo {

                max-width: 115px;

                height: 40px;

            }



            .institutional-logo.gerencia {

                max-width: 100px;

            }



            .logo-divider {

                height: 32px;

                margin: 0 6px;

            }

            .tv-title {

                font-size: 23px;

            }



            .room-name {

                font-size: 16px;

            }



            .patient-name {

                font-size: 15px;

            }



            .detail-value {

                font-size: 11px;

            }

        }
    </style>

</head>



<body>

    @php

    /*

    * Ejemplos:

    *

    * INGAR SANCHEZ GLADIS ESTHER

    * INGAR S. G. E.

    *

    * ARRIAGA VERASTEGUI CARLOS ALFONSO

    * ARRIAGA V. C. A.

    */

    $abreviarNombre = function ($nombreCompleto) {

    $nombreCompleto = trim(

    preg_replace('/\s+/', ' ', (string) $nombreCompleto)

    );



    if ($nombreCompleto === '') {

    return 'NO REGISTRADO';

    }



    $partes = preg_split('/\s+/', $nombreCompleto);



    $apellidoPaterno = mb_strtoupper(

    array_shift($partes),

    'UTF-8'

    );



    $iniciales = [];



    foreach ($partes as $parte) {

    if (trim($parte) !== '') {

    $iniciales[] = mb_strtoupper(

    mb_substr($parte, 0, 1, 'UTF-8'),

    'UTF-8'

    ) . '.';

    }

    }



    return trim(

    $apellidoPaterno . ' ' . implode(' ', $iniciales)

    );

    };

    @endphp



    <header class="tv-header">

        <div class="tv-header-left">

            <div class="institutional-logos">

                <img
                    src="{{ asset('img/logo-gobierno.png') }}"
                    alt="Gerencia Regional de Salud La Libertad"
                    class="institutional-logo gerencia">

                <span class="logo-divider"></span>

                <img
                    src="{{ asset('img/logo-hbt.png') }}"
                    alt="Hospital Belén de Trujillo"
                    class="institutional-logo">

            </div>



            <div>

                <h1 class="tv-title">

                    Estado de Salas de Operaciones

                </h1>



                <div class="tv-subtitle">

                    Hospital Belén de Trujillo

                </div>

            </div>

        </div>



        <div class="tv-clock">

            <div class="tv-clock-time" id="reloj">

                {{ now()->format('H:i:s') }}

            </div>



            <div class="tv-clock-date">

                {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}

            </div>

        </div>

    </header>



    <main class="rooms-grid">

        @foreach ($salas as $sala)

        @php

        /*

        * Estas son las propiedades correctas que asigna

        * el método panelTv() del controlador.

        */

        $programacionActiva = $sala->programacion_activa;

        $programacionLimpieza = $sala->programacion_limpieza;

        $proximaProgramacion = $sala->proxima_programacion;



        $condicionSala = mb_strtoupper(

        trim((string) $sala->condicion),

        'UTF-8'

        );



        $esInoperativa = $condicionSala === 'INOPERATIVA';



        /*

        * Si hay una programación activa, su estado puede ser:

        * EN_PREPARACION o EN_CURSO.

        */

        $estadoActivo = $programacionActiva

        ? mb_strtoupper(

        trim((string) $programacionActiva->estado),

        'UTF-8'

        )

        : null;



        $estaEnPreparacion =

        $programacionActiva &&

        $estadoActivo === 'EN_PREPARACION';



        $estaEnCurso =

        $programacionActiva &&

        $estadoActivo === 'EN_CURSO';



        $estaEnLimpieza = !is_null($programacionLimpieza);



        /*

        * Determinar apariencia y estado de la sala.

        */

        $claseTarjeta = '';

        $claseBadge = '';

        $textoEstado = 'DISPONIBLE';



        if ($esInoperativa) {

        $claseTarjeta = 'inoperativa';

        $claseBadge = 'inoperativa';

        $textoEstado = 'INOPERATIVA';

        } elseif ($estaEnLimpieza) {

        $claseTarjeta = 'limpieza';

        $claseBadge = 'limpieza';

        $textoEstado = 'EN LIMPIEZA';

        } elseif ($estaEnCurso) {

        $claseTarjeta = 'en-uso';

        $claseBadge = 'en-uso';

        $textoEstado = 'EN USO';

        } elseif ($estaEnPreparacion) {

        $claseTarjeta = 'preparacion';

        $claseBadge = 'preparacion';

        $textoEstado = 'EN PREPARACIÓN';

        }



        /*

        * Programación que se mostrará.

        */

        $programacionMostrar = $programacionActiva;



        /*

        * Si está en limpieza, se conserva la información

        * de la operación que acaba de finalizar.

        */

        if (!$programacionMostrar && $programacionLimpieza) {

        $programacionMostrar = $programacionLimpieza;

        }



        $solicitud = $programacionMostrar

        ? $programacionMostrar->solicitud

        : null;



        /*

        * Hora de inicio:

        * - Si ya empezó: fecha_inicio_real.

        * - Si está en preparación: fecha_hora_inicio.

        */

        $fechaHoraInicio = null;



        if ($programacionMostrar) {

        $fechaHoraInicio =

        $programacionMostrar->fecha_inicio_real

        ?: $programacionMostrar->fecha_hora_inicio;

        }



        $inicioCarbon = $fechaHoraInicio

        ? \Carbon\Carbon::parse($fechaHoraInicio)

        : null;



        /*

        * Fin probable:

        * hora mostrada de inicio + duración de la operación.

        */

        $duracionMinutos = $solicitud

        ? (int) $solicitud->duracion_operacion_minutos

        : 0;



        $finProbableCarbon =

        $inicioCarbon && $duracionMinutos > 0

        ? $inicioCarbon

        ->copy()

        ->addMinutes($duracionMinutos)

        : null;



        /*

        * Datos para próxima operación cuando la sala

        * se encuentre disponible.

        */

        $proximaSolicitud = $proximaProgramacion

        ? $proximaProgramacion->solicitud

        : null;

        @endphp



        <section class="room-card {{ $claseTarjeta }}">

            <div class="room-header">

                <div class="room-header-info">

                    <h2 class="room-name">

                        {{ $sala->nombre }}

                    </h2>



                    <div class="room-code">

                        {{ $sala->codigo }}

                        ·

                        {{ $sala->tipo }}

                    </div>

                </div>



                <span class="status-badge {{ $claseBadge }}">

                    {{ $textoEstado }}

                </span>

            </div>



            <div class="room-body">

                @if ($esInoperativa)

                <div class="empty-room">

                    <div class="empty-room-icon inoperativa">

                        <i class="fas fa-ban"></i>

                    </div>



                    <div class="empty-room-title inoperativa">

                        Sala inoperativa

                    </div>



                    <div class="empty-room-text">

                        No disponible para programación

                    </div>

                </div>



                @elseif ($estaEnLimpieza)

                <div class="cleaning-content">

                    <div class="cleaning-icon">

                        <i class="fas fa-broom"></i>

                    </div>



                    <div class="cleaning-title">

                        Sala en limpieza

                    </div>



                    <div class="cleaning-text">

                        Preparando la sala para la siguiente operación

                    </div>



                    @if ($proximaProgramacion)

                    <div class="next-operation">

                        Próxima operación:

                        <strong>

                            {{ \Carbon\Carbon::parse(

                                            $proximaProgramacion->fecha_hora_inicio

                                        )->format('H:i') }}

                        </strong>

                    </div>

                    @endif

                </div>



                @elseif ($programacionActiva && $solicitud)

                <div class="patient-name">

                    {{ $abreviarNombre($solicitud->paciente) }}

                </div>



                <div class="patient-account">

                    Cuenta: {{ $solicitud->numero_cuenta }}

                    ·

                    HC: {{ $solicitud->historia_clinica }}

                </div>



                <div class="detail-label">

                    Procedimiento

                </div>



                <div class="detail-value">

                    {{ mb_strtoupper(

                                $solicitud->procedimiento_nombre

                                    ?: 'NO REGISTRADO',

                                'UTF-8'

                            ) }}

                </div>



                <div class="detail-label">

                    Médico

                </div>



                <div class="detail-value">

                    {{ $abreviarNombre($solicitud->medico_nombre) }}

                </div>



                <div class="time-boxes">

                    <div class="time-box">

                        <div class="time-label">

                            Hora de inicio

                        </div>



                        <div class="time-value">

                            {{ $inicioCarbon

                                        ? $inicioCarbon->format('H:i')

                                        : '--:--' }}

                        </div>

                    </div>



                    <div class="time-box">

                        <div class="time-label">

                            Fin probable

                        </div>



                        <div class="time-value">

                            {{ $finProbableCarbon

                                        ? $finProbableCarbon->format('H:i')

                                        : '--:--' }}

                        </div>

                    </div>



                    <div class="time-box">

                        <div class="time-label">

                            Transcurrido

                        </div>



                        <div

                            class="time-value tiempo-transcurrido"

                            data-inicio="{{ $programacionActiva->fecha_inicio_real

                                        ? \Carbon\Carbon::parse(

                                            $programacionActiva->fecha_inicio_real

                                        )->toIso8601String()

                                        : '' }}">

                            {{ $programacionActiva->fecha_inicio_real

                                        ? '00:00'

                                        : '--:--' }}

                        </div>

                    </div>

                </div>



                @else

                <div class="empty-room">

                    <div class="empty-room-icon">

                        <i class="fas fa-check"></i>

                    </div>



                    <div class="empty-room-title">

                        Sala disponible

                    </div>



                    @if ($proximaProgramacion && $proximaSolicitud)

                    <div class="empty-room-text">

                        Próxima operación:

                        {{ \Carbon\Carbon::parse(

                                        $proximaProgramacion->fecha_hora_inicio

                                    )->format('H:i') }}

                    </div>

                    @else

                    <div class="empty-room-text">

                        Sin operación próxima

                    </div>

                    @endif

                </div>

                @endif

            </div>

        </section>

        @endforeach

    </main>



    <footer class="tv-footer">

        Actualización automática cada 30 segundos

    </footer>



    <script>
        function actualizarReloj() {

            const ahora = new Date();



            document.getElementById('reloj').textContent =

                ahora.toLocaleTimeString('es-PE', {

                    hour: '2-digit',

                    minute: '2-digit',

                    second: '2-digit',

                    hour12: false

                });

        }



        function actualizarTiemposTranscurridos() {

            const ahora = new Date();



            document

                .querySelectorAll('.tiempo-transcurrido')

                .forEach(function(elemento) {

                    const valorInicio = elemento.dataset.inicio;



                    if (!valorInicio) {

                        elemento.textContent = '--:--';

                        return;

                    }



                    const inicio = new Date(valorInicio);



                    let segundos = Math.floor(

                        (ahora.getTime() - inicio.getTime()) / 1000

                    );



                    if (segundos < 0) {

                        segundos = 0;

                    }



                    const horas = Math.floor(segundos / 3600);



                    const minutos = Math.floor(

                        (segundos % 3600) / 60

                    );



                    elemento.textContent =

                        String(horas).padStart(2, '0') +

                        ':' +

                        String(minutos).padStart(2, '0');

                });

        }



        actualizarReloj();

        actualizarTiemposTranscurridos();



        setInterval(function() {

            actualizarReloj();

            actualizarTiemposTranscurridos();

        }, 1000);
    </script>

</body>
</html>