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
                            Verifica tu correo
                        </h1>
                        <p class="text-xs text-gray-500 mt-3">
                            Gracias por registrarte. Revisa tu bandeja de entrada y haz clic en el enlace
                            de verificación que te hemos enviado. Si no lo recibiste, puedes solicitar uno nuevo.
                        </p>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 font-medium text-xs text-green-600 text-center">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                        </div>
                    @endif

                    <div class="mt-4 flex flex-col gap-4">
                        {{-- Reenviar enlace --}}
                        <form method="POST" action="{{ route('verification.send') }}" class="flex justify-center">
                            @csrf
                            <button type="submit" class="login-button">
                                {{ __('Reenviar correo de verificación') }}
                            </button>
                        </form>

                        {{-- Cerrar sesión --}}
                        <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
                            @csrf
                            <button
                                type="submit"
                                class="text-xs text-gray-500 hover:text-blue-700 underline underline-offset-2">
                                {{ __('Cerrar sesión') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login-form.js') }}"></script>
</x-guest-layout>
