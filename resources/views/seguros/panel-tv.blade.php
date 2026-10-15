@extends('adminlte::page')

@section('title', 'Panel TV de consultorios | Seguros')

@section('content_header')
<h1>
    Seguros
    <small class="text-muted">/ Panel TV de consultorios</small>
</h1>
@stop

@section('content')
<div id="panelTv" class="hbt-panel-tv">
    <header class="tv-encabezado">
        <div>
            <div class="tv-institucion">HOSPITAL BELÉN DE TRUJILLO</div>
            <h2>Disponibilidad de consultorios Consulta Externa</h2>
        </div>

        <div class="tv-controles">
            <form method="GET" action="{{ route('seguros.panel-tv.index') }}">
                <label for="fecha" class="sr-only">Fecha</label>

                <input
                    type="date"
                    name="fecha"
                    id="fecha"
                    value="{{ $fecha }}"
                    class="form-control"
                    required>

                <button class="btn btn-primary" type="submit">
                    Ver
                </button>
            </form>

            <button id="tvCompleta" class="btn btn-outline-primary" type="button">
                <i class="fas fa-expand mr-1"></i>
                Pantalla completa
            </button>
        </div>
    </header>

    <div class="tv-franja">
        <span id="tvFecha"></span>
        <strong id="tvReloj"></strong>
        <span id="tvPagina">Página 1 de 1</span>
    </div>

    <div id="tvEstado" class="tv-estado" role="status">
        Cargando programaciones…
    </div>

    <main id="tvTarjetas" class="tv-cuadricula"></main>

    <footer class="tv-pie">
        <span id="tvActualizado">Esperando actualización</span>
        <span>Cambio de página cada 10 segundos</span>
    </footer>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/hbt.css') }}">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo_hbt2.jpg') }}?v=2">
<style>
    .hbt-panel-tv {
        --tv-azul: #16476b;
        background: #edf3f9;
        color: var(--tv-azul);
        padding: 20px;
        border-radius: 16px;
    }

    .hbt-panel-tv .tv-encabezado,
    .hbt-panel-tv .tv-franja,
    .hbt-panel-tv .tv-pie {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .hbt-panel-tv .tv-institucion {
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 13px;
    }

    .hbt-panel-tv h2 {
        margin: 4px 0;
        font-size: clamp(22px, 2vw, 36px);
        font-weight: 800;
    }

    .hbt-panel-tv .tv-subtitulo {
        color: #5b7289;
    }

    .hbt-panel-tv .tv-controles,
    .hbt-panel-tv .tv-controles form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hbt-panel-tv .tv-franja {
        background: #16476b;
        color: #fff;
        margin: 16px 0;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: clamp(16px, 1.2vw, 22px);
    }

    .hbt-panel-tv .tv-cuadricula {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        grid-template-rows: repeat(3, minmax(190px, auto));
        gap: 14px;
    }

    .hbt-panel-tv .tv-tarjeta {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
        padding: 18px;
        background: linear-gradient(135deg, #fff, #e2ecf8);
        border: 1px solid #d5e2ee;
        border-top: 5px solid #16b8af;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(22, 71, 107, .08);
    }

    .hbt-panel-tv .tv-tarjeta.sin-cupos {
        border-top-color: #dc3545;
        background: linear-gradient(135deg, #fff, #fcecef);
    }

    .hbt-panel-tv .tv-tarjeta.sin-calculo {
        border-top-color: #d59c22;
    }

    .hbt-panel-tv .tv-titulo-tarjeta {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .hbt-panel-tv .tv-icono {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #d1f3f0;
        color: #087b78;
        font-size: 22px;
    }

    .hbt-panel-tv .tv-consultorio {
        margin: 0;
        font-size: clamp(17px, 1.35vw, 26px);
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .hbt-panel-tv .tv-medico {
        margin: 12px 0 6px;
        font-size: clamp(15px, 1.1vw, 21px);
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .hbt-panel-tv .tv-horario {
        color: #61758a;
        font-size: clamp(14px, 1vw, 18px);
        margin-bottom: 12px;
    }

    .hbt-panel-tv .tv-cupos {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .hbt-panel-tv .tv-contador {
        padding: 8px 4px;
        border-radius: 9px;
        color: #fff;
        text-align: center;
        line-height: 1.1;
    }

    .hbt-panel-tv .tv-contador strong {
        display: block;
        font-size: clamp(26px, 2.2vw, 42px);
    }

    .hbt-panel-tv .tv-contador span {
        font-size: clamp(13px, .95vw, 18px);
    }

    .hbt-panel-tv .tv-libres {
        background: #168347;
    }

    .hbt-panel-tv .tv-ocupados {
        background: #cf3549;
    }

    .hbt-panel-tv .tv-estado {
        margin-bottom: 12px;
        font-size: 17px;
    }

    .hbt-panel-tv .tv-estado:empty {
        display: none;
    }

    .hbt-panel-tv .tv-pie {
        margin-top: 14px;
        color: #536c82;
        font-size: 14px;
    }

    /* Se pone en pantalla completa solo el panel. */
    .hbt-panel-tv:fullscreen {
        height: 100vh;
        width: 100vw;
        border-radius: 0;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }

    .hbt-panel-tv:fullscreen .tv-controles {
        display: none;
    }

    .hbt-panel-tv:fullscreen .tv-cuadricula {
        flex: 1;
        min-height: 0;
        grid-template-rows: repeat(3, minmax(0, 1fr));
    }

    .hbt-panel-tv .tv-ubicacion {
        align-self: center;
        text-align: center;
        margin: 10px 0;
        padding: 8px 22px;
        min-width: 120px;
        max-width: 100%;
        background: #dceaf7;
        border: 1px solid #bfd5e9;
        border-radius: 12px;
        color: #16476b;
    }

    .hbt-panel-tv .tv-ubicacion-etiqueta {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .hbt-panel-tv .tv-ubicacion-numero {
        display: block;
        font-size: clamp(25px, 2.2vw, 42px);
        line-height: 1.1;
        overflow-wrap: anywhere;
    }

    .hbt-panel-tv .tv-ubicacion.pendiente {
        background: #f0f3f6;
        border-color: #d8e0e7;
        color: #738495;
    }

    .hbt-panel-tv .tv-ubicacion.pendiente .tv-ubicacion-numero {
        font-size: 18px;
    }

    .hbt-panel-tv .tv-horario {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .hbt-panel-tv .tv-turno {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.2;
        background: #e8edf2;
        color: #526577;
    }

    .hbt-panel-tv .tv-turno-manana {
        background: #dceafa;
        color: #174f87;
        border: 1px solid #aac9ea;
    }

    .hbt-panel-tv .tv-turno-tarde {
        background: #fff0d5;
        color: #854b00;
        border: 1px solid #efc476;
    }

    .hbt-panel-tv .tv-tarjeta {
        padding: 12px;
    }

    .hbt-panel-tv .tv-medico {
        margin: 8px 0 4px;
    }

    .hbt-panel-tv .tv-horario {
        margin-bottom: 4px;
    }

    .hbt-panel-tv .tv-ubicacion {
        margin: 4px 0;
        padding: 4px 16px;
    }

    .hbt-panel-tv .tv-ubicacion-numero {
        font-size: 30px;
    }

    .hbt-panel-tv .tv-contador {
        padding: 5px 4px;
    }

    .hbt-panel-tv .tv-contador strong {
        font-size: 30px;
    }

    /* Verde suave para cupos libres */
    .hbt-panel-tv .tv-libres {
        background: #d9eee2;
        color: #246343;
        border: 1px solid #bdddc9;
    }

    /* Rojo suave para cupos ocupados */
    .hbt-panel-tv .tv-ocupados {
        background: #f5dfe3;
        color: #943e4b;
        border: 1px solid #e8bdc5;
    }

    @media (max-width: 991px) {
        .hbt-panel-tv .tv-cuadricula {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-template-rows: auto;
        }

        .hbt-panel-tv .tv-encabezado {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 575px) {
        .hbt-panel-tv .tv-cuadricula {
            grid-template-columns: 1fr;
        }

        .hbt-panel-tv .tv-controles,
        .hbt-panel-tv .tv-franja,
        .hbt-panel-tv .tv-pie {
            flex-wrap: wrap;
        }
    }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fecha = @json($fecha);
        const endpoint = @json(route('seguros.panel-tv.datos'));
        const porPagina = 12;

        const panel = document.getElementById('panelTv');
        const cuadricula = document.getElementById('tvTarjetas');
        const estado = document.getElementById('tvEstado');
        const paginaTexto = document.getElementById('tvPagina');
        const actualizado = document.getElementById('tvActualizado');

        let tarjetas = [];
        let pagina = 0;
        let cargando = false;
        let hayDatos = false;
        let aviso = '';

        const [anio, mes, dia] = fecha.split('-');

        document.getElementById('tvFecha').textContent =
            'Programación: ' + dia + '/' + mes + '/' + anio;

        function crear(tag, clase, texto) {
            const elemento = document.createElement(tag);
            elemento.className = clase;

            if (texto !== undefined) {
                elemento.textContent = texto;
            }

            return elemento;
        }

        function contador(numero, etiqueta, clase) {
            const bloque = crear('div', 'tv-contador ' + clase);
            bloque.append(
                crear('strong', '', numero),
                crear('span', '', etiqueta)
            );

            return bloque;
        }

        function renderizar() {
            const paginas = Math.max(1, Math.ceil(tarjetas.length / porPagina));
            pagina = pagina % paginas;

            paginaTexto.textContent = `Página ${pagina + 1} de ${paginas}`;

            const fragmento = document.createDocumentFragment();
            const visibles = tarjetas.slice(
                pagina * porPagina,
                (pagina + 1) * porPagina
            );

            visibles.forEach(function(fila) {
                let clase = 'tv-tarjeta';

                if (!fila.valido) {
                    clase += ' sin-calculo';
                } else if (fila.libres === 0) {
                    clase += ' sin-cupos';
                }

                const tarjeta = crear('article', clase);
                const informacion = crear('div', '');
                const titulo = crear('div', 'tv-titulo-tarjeta');
                const icono = crear('span', 'tv-icono');

                icono.setAttribute('aria-hidden', 'true');
                icono.appendChild(crear('i', 'fas fa-stethoscope'));

                titulo.append(
                    icono,
                    crear('h3', 'tv-consultorio', fila.consultorio)
                );

                informacion.append(
                    titulo,
                    crear('div', 'tv-medico', fila.medico),
                    crear('div', 'tv-horario', fila.inicio + ' – ' + fila.fin)
                );

                const turno = fila.turno || 'SIN TURNO';

                const etiquetaTurno = crear(
                    'span',
                    'tv-turno',
                    turno
                );

                if (turno === 'TARDE') {
                    etiquetaTurno.classList.add('tv-turno-tarde');
                } else if (turno === 'MAÑANA') {
                    etiquetaTurno.classList.add('tv-turno-manana');
                }

                informacion.querySelector('.tv-horario').appendChild(etiquetaTurno);

                tarjeta.appendChild(informacion);

                const ubicacion = crear('div', 'tv-ubicacion');
                const numero = fila.numero_consultorio;

                ubicacion.append(
                    crear('span', 'tv-ubicacion-etiqueta', 'CONSULTORIO'),
                    crear(
                        'strong',
                        'tv-ubicacion-numero',
                        numero ? numero : 'Por asignar'
                    )
                );

                if (!numero) {
                    ubicacion.classList.add('pendiente');
                }

                tarjeta.appendChild(ubicacion);

                if (fila.valido) {
                    const cupos = crear('div', 'tv-cupos');

                    cupos.append(
                        contador(fila.libres, 'Libres', 'tv-libres'),
                        contador(fila.ocupados, 'Ocupados', 'tv-ocupados')
                    );

                    tarjeta.appendChild(cupos);
                } else {
                    tarjeta.appendChild(
                        crear('div', 'text-warning', 'Horario pendiente de revisión')
                    );
                }

                fragmento.appendChild(tarjeta);
            });

            cuadricula.replaceChildren(fragmento);

            estado.textContent = aviso || (
                hayDatos && tarjetas.length === 0 ?
                'No hay programaciones para la fecha seleccionada.' :
                ''
            );
        }

        async function cargar() {
            if (cargando) return;

            cargando = true;

            const controlador = new AbortController();
            const timeout = setTimeout(() => controlador.abort(), 15000);

            try {
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('fecha', fecha);

                const respuesta = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    cache: 'no-store',
                    signal: controlador.signal
                });

                if (!respuesta.ok) {
                    throw new Error('No se pudo consultar el panel');
                }

                const datos = await respuesta.json();

                if (!Array.isArray(datos.tarjetas)) {
                    throw new Error('Respuesta incorrecta');
                }

                tarjetas = datos.tarjetas;
                hayDatos = true;
                aviso = '';

                actualizado.textContent = 'Actualizado: ' + datos.actualizado;

                renderizar();
            } catch (error) {
                aviso = hayDatos ?
                    'No se pudo actualizar. Se muestran los últimos datos recibidos.' :
                    'No se pudo cargar el panel. Se reintentará automáticamente.';

                estado.textContent = aviso;
            } finally {
                clearTimeout(timeout);
                cargando = false;
            }
        }

        function reloj() {
            document.getElementById('tvReloj').textContent =
                new Intl.DateTimeFormat('es-PE', {
                    timeZone: 'America/Lima',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).format(new Date());
        }

        document.getElementById('tvCompleta').addEventListener('click', async function() {
            try {
                await panel.requestFullscreen();
            } catch (error) {
                estado.textContent = 'Use F11 para ampliar la ventana del navegador.';
            }
        });

        reloj();
        setInterval(reloj, 1000);

        cargar();

        // La rotación continúa aunque una actualización tarde o falle.
        setInterval(function() {
            const paginas = Math.max(1, Math.ceil(tarjetas.length / porPagina));
            pagina = (pagina + 1) % paginas;

            renderizar();
            cargar();
        }, 10000);
    });
</script>
@stop