<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Belén de Trujillo</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo_hbt2.jpg') }}">

    <style>
        :root {
            --hbt-primary: #0F6CBD;
            --hbt-secondary: #EAF4FD;
            --hbt-dark: #0B4F8A;
            --hbt-accent: #2E86DE;
            --hbt-bg: #F4F8FB;
            --hbt-border: #D6E4F0;
            --hbt-text: #1F2937;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #EAF4FD, #F8FBFF);
            color: var(--hbt-text);
        }

        .topbar {
            height: 78px;
            background: #ffffff;
            border-bottom: 1px solid var(--hbt-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 55px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand img {
            width: 90px;
            height: auto;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--hbt-dark);
            margin: 0;
        }

        .brand-subtitle {
            font-size: 14px;
            color: #334155;
            margin: 2px 0 0;
        }

        .login-btn {
            border: 1px solid var(--hbt-primary);
            color: var(--hbt-primary);
            padding: 11px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            transition: .2s;
        }

        .login-btn:hover {
            background: var(--hbt-primary);
            color: #ffffff;
        }

        .main {
            min-height: calc(100vh - 78px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 45px;
        }

        .grid {
            width: 100%;
            max-width: 1840px;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 42px;
            align-items: center;
        }

        .hero-card,
        .summary-card {
            background: #ffffff;
            border: 1px solid var(--hbt-border);
            border-radius: 28px;
            box-shadow: 0 22px 55px rgba(15, 108, 189, .12);
        }

        .hero-card {
            padding: 48px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--hbt-secondary);
            color: var(--hbt-dark);
            padding: 9px 15px;
            border-radius: 30px;
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .dot {
            width: 12px;
            height: 12px;
            background: var(--hbt-accent);
            border-radius: 50%;
            box-shadow: 0 0 0 6px rgba(46, 134, 222, .12);
        }

        h1 {
            font-size: 48px;
            line-height: 1.1;
            color: var(--hbt-dark);
            margin: 0 0 22px;
            font-weight: 900;
            letter-spacing: -.8px;
        }

        .description {
            font-size: 17px;
            line-height: 1.7;
            color: #475569;
            max-width: 760px;
            margin-bottom: 25px;
        }

        .access-btn {
            display: inline-block;
            background: var(--hbt-primary);
            color: #ffffff;
            padding: 13px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 800;
            margin-bottom: 28px;
            box-shadow: 0 8px 20px rgba(15, 108, 189, .20);
        }

        .access-btn:hover {
            background: var(--hbt-dark);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .feature {
            border: 1px solid var(--hbt-border);
            border-radius: 16px;
            padding: 18px;
            background: #FBFDFF;
        }

        .feature strong {
            color: var(--hbt-dark);
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .feature span {
            color: #475569;
            font-size: 14px;
            line-height: 1.4;
        }

        .summary-card {
            padding: 32px;
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .summary-header h2 {
            color: var(--hbt-dark);
            font-size: 22px;
            margin: 0;
        }

        .status {
            background: var(--hbt-secondary);
            color: var(--hbt-dark);
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 13px;
        }

        .kpis {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .kpi {
            border: 1px solid var(--hbt-border);
            border-radius: 16px;
            padding: 24px;
            background: #FBFDFF;
        }

        .kpi.primary {
            background: linear-gradient(135deg, var(--hbt-primary), var(--hbt-accent));
            color: #ffffff;
            border: none;
        }

        .kpi h3 {
            font-size: 36px;
            margin: 0 0 8px;
            font-weight: 900;
        }

        .kpi p {
            margin: 0;
            font-weight: 700;
            font-size: 14px;
        }

        .progress-box {
            border: 1px solid var(--hbt-border);
            border-radius: 16px;
            padding: 20px;
            background: #FBFDFF;
        }

        .progress-row {
            margin-bottom: 16px;
        }

        .progress-title {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .bar {
            height: 10px;
            background: #E2E8F0;
            border-radius: 20px;
            overflow: hidden;
        }

        .bar span {
            display: block;
            height: 100%;
            background: linear-gradient(135deg, var(--hbt-primary), var(--hbt-accent));
            border-radius: 20px;
        }

        @media (max-width: 1000px) {
            .grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 38px;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 0 25px;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <div class="brand">
        <img src="{{ asset('img/logo_hbt2.jpg') }}" alt="Hospital Belén">
        <div>
            <h3 class="brand-title">Hospital Belén de Trujillo</h3>
            <p class="brand-subtitle">HBT · Consulta Externa · Emergencia · Hospitalización</p>
        </div>
    </div>

    <a href="{{ route('login') }}" class="login-btn">
        Iniciar sesión
    </a>
</header>

<main class="main">
    <div class="grid">

        <section class="hero-card">
            <div class="tag">
                <span class="dot"></span>
                Plataforma local de inteligencia hospitalaria
            </div>

            <h1>
                Consulta Externa - Emergencia - Hospitalización
            </h1>

            <p class="description">
                Sistema de monitoreo estadístico para visualizar citas, atenciones,
                pacientes activos, servicios, camas, diagnósticos, financiamiento y
                producción diaria del Hospital Belén de Trujillo.
            </p>

            <a href="{{ route('login') }}" class="access-btn">
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
                    <span>Indicadores para jefatura, estadística, emergencia y hospitalización.</span>
                </div>
            </div>
        </section>

        <section class="summary-card">
            <div class="summary-header">
                <h2>Resumen Operativo</h2>
                <span class="status">En línea</span>
            </div>

            <div class="kpis">
                <div class="kpi primary">
                    <h3>120</h3>
                    <p>Citas programadas</p>
                </div>

                <div class="kpi">
                    <h3>85</h3>
                    <p>Atenciones realizadas</p>
                </div>

                <div class="kpi">
                    <h3>35</h3>
                    <p>Pendientes</p>
                </div>

                <div class="kpi primary">
                    <h3>71%</h3>
                    <p>Avance diario</p>
                </div>
            </div>

            <div class="progress-box">
                <div class="progress-row">
                    <div class="progress-title">
                        <span>Medicina Interna</span>
                        <span>82%</span>
                    </div>
                    <div class="bar">
                        <span style="width: 82%"></span>
                    </div>
                </div>

                <div class="progress-row">
                    <div class="progress-title">
                        <span>Pediatría</span>
                        <span>68%</span>
                    </div>
                    <div class="bar">
                        <span style="width: 68%"></span>
                    </div>
                </div>

                <div class="progress-row" style="margin-bottom:0;">
                    <div class="progress-title">
                        <span>Ginecología</span>
                        <span>74%</span>
                    </div>
                    <div class="bar">
                        <span style="width: 74%"></span>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

</body>
</html>