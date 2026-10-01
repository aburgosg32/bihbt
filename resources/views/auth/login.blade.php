<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión | Hospital Belén de Trujillo</title>
    <link rel="icon" href="{{ asset('img/logo_hbt2.jpg') }}">
    <style>
        :root {
            --azul: #0b4f8a;
            --celeste: #149eb4;
            --borde: #d8e4ec;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: #1d2939;
            background: #eef5f8;
        }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: clamp(35px, 8vh, 76px) 20px 20px;
        }

        .logo {
            width: min(440px, 88vw);
            max-height: 155px;
            object-fit: contain;
            margin-bottom: 25px;
        }

        .card {
            width: min(450px, 100%);
            padding: 34px 28px 32px;
            background: white;
            border: 1px solid var(--borde);
            border-radius: 12px;
            box-shadow: 0 16px 35px rgba(9, 53, 87, .09);
        }

        h1 {
            margin: 0 0 7px;
            text-align: center;
            color: var(--azul);
            font-size: 26px;
        }

        .subtitle {
            margin: 0 0 27px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        .field {
            margin-bottom: 19px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 700;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1px solid #c7d5df;
            border-radius: 7px;
            background: #fff;
            color: #1d2939;
            font: inherit;
        }

        input:focus {
            outline: 2px solid rgba(20, 158, 180, .18);
            border-color: var(--celeste);
        }

        .invalid {
            border-color: #c0392b !important;
        }

        .error {
            color: #b42318;
            font-size: 13px;
            margin-top: 6px;
        }

        .status {
            padding: 11px 13px;
            background: #e6f7f2;
            border-radius: 7px;
            color: #146c56;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 4px 0 25px;
            font-size: 13px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin: 0;
            font-weight: 400;
            cursor: pointer;
        }

        .remember input {
            accent-color: var(--celeste);
        }

        a {
            color: var(--azul);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            min-height: 46px;
            border: 0;
            border-radius: 7px;
            background: var(--celeste);
            color: white;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background: #0e849a;
        }

        footer {
            margin-top: auto;
            padding-top: 52px;
            text-align: center;
            color: #536779;
            font-size: 13px;
            line-height: 1.5;
        }

        @media (max-width: 480px) {
            .page {
                padding-top: 45px;
            }

            .logo {
                max-height: 125px;
            }

            .card {
                padding: 28px 20px;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <img class="logo" src="{{ asset('img/logo-hbt.png') }}" alt="Hospital Belén de Trujillo">
        <section class="card" aria-labelledby="login-title">
            <h1 id="login-title">Iniciar sesión</h1>
            <p class="subtitle">Sistema Trazabilidad Hospitalario</p>

            @if (session('status'))
            <div class="status" role="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="dni">DNI</label>
                    <input id="dni" name="dni" type="text" inputmode="numeric" pattern="[0-9]{8}" maxlength="8" autocomplete="username" placeholder="Ingrese su DNI" value="{{ old('dni') }}" class="@error('dni') invalid @enderror" required autofocus>
                    @error('dni')<div class="error" role="alert">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Ingrese su contraseña" class="@error('password') invalid @enderror" required>
                    @error('password')<div class="error" role="alert">{{ $message }}</div>@enderror
                </div>
                <div class="options">
                    <label class="remember" for="remember"><input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))> Recordarme</label>
                    <a href="{{ url('/') }}">Volver al inicio</a>
                </div>
                <button type="submit">INGRESAR</button>
            </form>
        </section>
        <footer>Hospital Belén de Trujillo · Oficina de Estadística e Informática<br>Área de Informática HBT</footer>
    </main>
</body>

</html>