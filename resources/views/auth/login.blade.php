<x-guest-layout>
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

        body {
            background:
                radial-gradient(circle at top left, rgba(46, 134, 222, .16), transparent 35%),
                linear-gradient(135deg, var(--hbt-bg), #ffffff) !important;
        }

        .login-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-title img {
            height: 74px;
            margin: 0 auto 14px auto;
        }

        .login-title h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--hbt-dark);
            margin: 0;
        }

        .login-title p {
            font-size: 14px;
            color: #64748b;
            margin-top: 6px;
        }

        .hbt-input {
            border-radius: 12px !important;
            border: 1px solid var(--hbt-border) !important;
            padding: 12px 14px !important;
        }

        .hbt-input:focus {
            border-color: var(--hbt-accent) !important;
            box-shadow: 0 0 0 3px rgba(46, 134, 222, .18) !important;
        }

        .hbt-button {
            background: linear-gradient(135deg, var(--hbt-primary), var(--hbt-accent)) !important;
            border-radius: 12px !important;
            padding: 12px 20px !important;
            font-weight: 800 !important;
            letter-spacing: .3px;
        }

        .hbt-button:hover {
            background: linear-gradient(135deg, var(--hbt-dark), var(--hbt-primary)) !important;
        }

        .hbt-link {
            color: var(--hbt-primary) !important;
            font-weight: 600;
        }

        .hbt-link:hover {
            color: var(--hbt-dark) !important;
        }

        .hbt-remember {
            color: #374151;
        }

        input[type="checkbox"] {
            color: var(--hbt-primary) !important;
        }
    </style>

    <div class="login-title">
        <img src="{{ asset('img/logo_hbt2.jpg') }}" alt="Hospital Belén de Trujillo">
        <h1>HBT</h1>
        <p>Consulta Externa · Emergencia · Hospitalización</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="dni" value="DNI" />

            <x-text-input id="dni"
                class="block mt-1 w-full hbt-input"
                type="text"
                name="dni"
                maxlength="8"
                inputmode="numeric"
                :value="old('dni')"
                required
                autofocus
                autocomplete="username" />

            <x-input-error :messages="$errors->get('dni')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password"
                class="block mt-1 w-full hbt-input"
                type="password"
                name="password"
                required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 shadow-sm"
                    name="remember">

                <span class="ms-2 text-sm hbt-remember">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-5">
            <a href="{{ url('/') }}" class="underline text-sm hbt-link">
                Volver al inicio
            </a>

            <x-primary-button class="ms-3 hbt-button">
                Iniciar sesión
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>