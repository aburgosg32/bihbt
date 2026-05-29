<x-guest-layout>
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(22, 160, 133, .16), transparent 35%),
                linear-gradient(135deg, #f7fbfa, #eef7f4) !important;
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
            color: #0b6b55;
            margin: 0;
        }

        .login-title p {
            font-size: 14px;
            color: #6b7280;
            margin-top: 6px;
        }

        .hbt-input {
            border-radius: 12px !important;
            border: 1px solid #dbe7e4 !important;
            padding: 12px 14px !important;
        }

        .hbt-input:focus {
            border-color: #0f8b6f !important;
            box-shadow: 0 0 0 3px rgba(15, 139, 111, .15) !important;
        }

        .hbt-button {
            background: linear-gradient(135deg, #0f8b6f, #16a085) !important;
            border-radius: 12px !important;
            padding: 12px 20px !important;
            font-weight: 800 !important;
            letter-spacing: .3px;
        }

        .hbt-link {
            color: #0f8b6f !important;
            font-weight: 600;
        }

        .hbt-remember {
            color: #374151;
        }
    </style>

    <div class="login-title">
        <img src="{{ asset('img/logo-hbt.png') }}" alt="Hospital Belén de Trujillo">
        <h1>HBT</h1>
        <p>Dashboard de Consulta Externa</p>
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
                    class="rounded border-gray-300 text-green-700 shadow-sm focus:ring-green-600"
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