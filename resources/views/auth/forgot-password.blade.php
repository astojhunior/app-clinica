<x-guest-layout>
    <div class="login-bg min-h-screen flex items-center justify-center">
        <div class="login-shell">
            {{-- IZQUIERDA: IMAGEN --}}
            <div class="login-left">
                <div class="login-image-wrapper">
                    <img src="{{ asset('images/login-medicos.png') }}" alt="Médicos" class="login-image">
                </div>
            </div>

            {{-- DERECHA: TARJETA BLANCA --}}
            <div class="login-right">
                <div class="login-card">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800 tracking-[0.2em] uppercase">
                            Recuperar clave
                        </h1>
                        <p class="text-xs text-gray-500 mt-2">
                            Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
                        </p>
                    </div>

                    {{-- Mensaje de estado --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Correo electrónico')" />
                            <x-text-input
                                id="email"
                                class="login-input"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                placeholder="tucorreo@clinica.com"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs ml-2" />
                        </div>

                        {{-- Botón --}}
                        <div class="flex flex-col items-center gap-3 mt-2">
                            <button type="submit" class="login-button">
                                Enviar enlace de restablecimiento
                            </button>

                            <a href="{{ route('login') }}"
                               class="text-xs text-gray-500 hover:text-blue-700 underline underline-offset-2">
                                Volver al inicio de sesión
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login-form.js') }}"></script>
</x-guest-layout>
