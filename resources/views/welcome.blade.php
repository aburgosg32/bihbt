<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BI HBT | Hospital Belén de Trujillo</title>

    <style>
        :root {
            --hbt-green: #0f8b6f;
            --hbt-green-2: #16a085;
            --hbt-dark: #0b6b55;
            --hbt-bg: #f4f8f7;
            --hbt-text: #1f2937;
            --hbt-muted: #6b7280;
            --hbt-border: #dbe7e4;
            --hbt-white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(22, 160, 133, .16), transparent 35%),
                linear-gradient(135deg, #f7fbfa, #eef7f4);
            color: var(--hbt-text);
        }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 82px;
            background: var(--hbt-white);
            border-bottom: 1px solid var(--hbt-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            box-shadow: 0 4px 20px rgba(15, 139, 111, .08);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand img {
            height: 52px;
            width: auto;
            object-fit: contain;
        }

        .brand-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--hbt-dark);
            line-height: 1.1;
        }

        .brand-title span {
            font-size: 13px;
            color: var(--hbt-muted);
            font-weight: 600;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn-outline {
            border: 1px solid var(--hbt-green);
            color: var(--hbt-green);
            background: #fff;
        }

        .btn-outline:hover {
            background: rgba(15, 139, 111, .08);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--hbt-green), var(--hbt-green-2));
            color: #fff;
            box-shadow: 0 10px 24px rgba(15, 139, 111, .24);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
        }

        .hero {
            flex: 1;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 40px;
            align-items: center;
            padding: 58px 7%;
        }

        .hero-card {
            background: rgba(255,255,255,.88);
            border: 1px solid var(--hbt-border);
            border-radius: 28px;
            padding: 42px;
            box-shadow: 0 24px 60px rgba(15, 139, 111, .12);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(15, 139, 111, .10);
            color: var(--hbt-dark);
            font-weight: 800;
            font-size: 13px;
            margin-bottom: 22px;
        }

        .pulse {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--hbt-green);
            box-shadow: 0 0 0 6px rgba(15, 139, 111, .15);
        }

        .hero h2 {
            font-size: 44px;
            line-height: 1.08;
            color: var(--hbt-dark);
            font-weight: 900;
            margin-bottom: 18px;
        }

        .hero p {
            font-size: 17px;
            line-height: 1.7;
            color: var(--hbt-muted);
            margin-bottom: 28px;
            max-width: 720px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 30px;
        }

        .feature {
            background: #fff;
            border: 1px solid var(--hbt-border);
            border-radius: 18px;
            padding: 18px;
        }

        .feature strong {
            display: block;
            color: var(--hbt-dark);
            font-size: 15px;
            margin-bottom: 5px;
        }

        .feature span {
            font-size: 13px;
            color: var(--hbt-muted);
            line-height: 1.4;
        }

        .visual {
            position: relative;
        }

        .panel {
            background: #fff;
            border: 1px solid var(--hbt-border);
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 24px 60px rgba(15, 139, 111, .14);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .panel-header h3 {
            color: var(--hbt-dark);
            font-size: 18px;
            font-weight: 900;
        }

        .status {
            font-size: 12px;
            font-weight: 800;
            color: var(--hbt-green);
            background: rgba(15, 139, 111, .10);
            padding: 7px 10px;
            border-radius: 999px;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .kpi {
            border-radius: 20px;
            padding: 22px;
            background: linear-gradient(135deg, var(--hbt-green), var(--hbt-green-2));
            color: #fff;
            min-height: 120px;
        }

        .kpi.secondary {
            background: #f8fcfb;
            color: var(--hbt-text);
            border: 1px solid var(--hbt-border);
        }

        .kpi h4 {
            font-size: 34px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .kpi span {
            font-size: 13px;
            font-weight: 700;
            opacity: .9;
        }

        .bars {
            margin-top: 20px;
            background: #f8fcfb;
            border: 1px solid var(--hbt-border);
            border-radius: 20px;
            padding: 20px;
        }

        .bar-row {
            margin-bottom: 15px;
        }

        .bar-row:last-child {
            margin-bottom: 0;
        }

        .bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 700;
            color: var(--hbt-text);
            margin-bottom: 7px;
        }

        .bar-track {
            height: 9px;
            border-radius: 999px;
            background: #e6f1ee;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--hbt-green), var(--hbt-green-2));
        }

        .footer {
            text-align: center;
            padding: 18px;
            font-size: 13px;
            color: var(--hbt-muted);
        }

        @media (max-width: 980px) {
            .hero {
                grid-template-columns: 1fr;
                padding: 36px 5%;
            }

            .hero h2 {
                font-size: 34px;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 0 5%;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                height: auto;
                padding: 16px;
                flex-direction: column;
                gap: 14px;
            }

            .hero-card {
                padding: 28px;
            }

            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="page">

    <header class="topbar">
        <div class="brand">
            <img src="{{ asset('img/logo-hbt.png') }}" alt="Hospital Belén de Trujillo">
            <div class="brand-title">
                <h1>Hospital Belén de Trujillo</h1>
                <span>HBT · Consulta Externa</span>
            </div>
        </div>

        <nav class="nav-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Ir al Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Iniciar sesión</a>
            @endauth
        </nav>
    </header>

    <main class="hero">
        <section class="hero-card">
            <div class="badge">
                <span class="pulse"></span>
                Plataforma local de inteligencia hospitalaria
            </div>

            <h2>Consulta Externa - Emergencia - Hospitalización</h2>

            <p>
                Sistema de monitoreo estadístico para visualizar citas, atenciones,
                especialidades, médicos y producción diaria de consulta externa del
                Hospital Belén de Trujillo.
            </p>

            <a href="{{ route('login') }}" class="btn btn-primary">
                Acceder al sistema
            </a>

            <div class="features">
                <div class="feature">
                    <strong>Tiempo real local</strong>
                    <span>Actualización operativa con Chart.js sin depender de servicios cloud.</span>
                </div>

                <div class="feature">
                    <strong>Datos seguros</strong>
                    <span>Consulta controlada mediante vistas SQL Server y acceso restringido.</span>
                </div>

                <div class="feature">
                    <strong>Gestión hospitalaria</strong>
                    <span>Indicadores para jefatura, estadística y toma de decisiones.</span>
                </div>
            </div>
        </section>

        <section class="visual">
            <div class="panel">
                <div class="panel-header">
                    <h3>Resumen Operativo</h3>
                    <span class="status">En línea</span>
                </div>

                <div class="kpi-grid">
                    <div class="kpi">
                        <h4>120</h4>
                        <span>Citas programadas</span>
                    </div>

                    <div class="kpi secondary">
                        <h4>85</h4>
                        <span>Atenciones realizadas</span>
                    </div>

                    <div class="kpi secondary">
                        <h4>35</h4>
                        <span>Pendientes</span>
                    </div>

                    <div class="kpi">
                        <h4>71%</h4>
                        <span>Avance diario</span>
                    </div>
                </div>

                <div class="bars">
                    <div class="bar-row">
                        <div class="bar-label">
                            <span>Medicina Interna</span>
                            <span>82%</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:82%"></div>
                        </div>
                    </div>

                    <div class="bar-row">
                        <div class="bar-label">
                            <span>Pediatría</span>
                            <span>68%</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:68%"></div>
                        </div>
                    </div>

                    <div class="bar-row">
                        <div class="bar-label">
                            <span>Ginecología</span>
                            <span>74%</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:74%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        Hospital Belén de Trujillo · Sistema BI HBT · Consulta Externa
    </footer>

</div>
</body>
</html>