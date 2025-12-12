<x-guest-layout>
    <div class="login-bg min-h-screen flex items-center justify-center">
        <div class="login-shell">
            {{-- IZQUIERDA: IMAGEN --}}
            <div class="login-left">
                <div class="login-image-wrapper">
                    <img src="{{ asset('images/login-medicos.png') }}" alt="Médicos" class="login-image">
                </div>
            </div>

            {{-- DERECHA: TARJETA BLANCA DE REGISTRO --}}
            <div class="login-right">
                <div class="login-card">
                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold text-gray-800 tracking-[0.2em] uppercase">
                            Registro
                        </h1>
                        <p class="text-xs text-gray-500 mt-2">
                            Crea una cuenta para acceder al sistema.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        {{-- Nombre --}}
                        <div>
                            <x-input-label for="name" :value="__('Nombre completo')" />
                            <x-text-input
                                id="name"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Tu nombre y apellidos"
                                class="login-input"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs ml-2" />
                        </div>

                        {{-- Correo --}}
                        <div>
                            <x-input-label for="email" :value="__('Correo electrónico')" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autocomplete="username"
                                placeholder="tucorreo@clinica.com"
                                class="login-input"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs ml-2" />
                        </div>

                        {{-- Password --}}
                        <div class="mt-2">
                            <x-input-label for="password" :value="__('Contraseña')" />

                            <div class="relative">
                                <x-text-input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Mínimo 8 caracteres"
                                    class="login-input pr-10"
                                />

                                {{-- Ojo password principal --}}
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-4 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none"
                                >
                                    <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                              d="M3 3l18 18M10.584 10.587A3 3 0 0113.412 13.41M6.228 6.228C4.45 7.39 3.186 9.138 2.458 12c1.274 4.057 5.065 7 9.542 7 1.41 0 2.76-.25 4.01-.708M17.772 17.772C19.55 16.61 20.814 14.862 21.542 12 20.268 7.943 16.477 5 12 5c-.94 0-1.852.11-2.722.317" />
                                    </svg>
                                </button>
                            </div>

                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs ml-2" />
                        </div>

                        {{-- Confirmar Password --}}
                        <div class="mt-2">
                            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />

                            <div class="relative">
                                <x-text-input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Repite la contraseña"
                                    class="login-input pr-10"
                                />

                                {{-- Ojo confirmación (ids distintos) --}}
                                <button
                                    type="button"
                                    id="togglePasswordConfirm"
                                    class="absolute inset-y-0 right-4 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none"
                                >
                                    <svg id="icon-eye-confirm" xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    <svg id="icon-eye-off-confirm" xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                              d="M3 3l18 18M10.584 10.587A3 3 0 0113.412 13.41M6.228 6.228C4.45 7.39 3.186 9.138 2.458 12c1.274 4.057 5.065 7 9.542 7 1.41 0 2.76-.25 4.01-.708M17.772 17.772C19.55 16.61 20.814 14.862 21.542 12 20.268 7.943 16.477 5 12 5c-.94 0-1.852.11-2.722.317" />
                                    </svg>
                                </button>
                            </div>

                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs ml-2" />
                        </div>

                        {{-- Botones --}}
                        <div class="flex flex-col items-center gap-3 mt-4">
                            <button type="submit" class="login-button">
                                Registrarme
                            </button>

                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center px-8 py-2 rounded-full
                                      border border-blue-500 text-blue-600 text-sm font-semibold
                                      hover:bg-blue-50 transition-colors">
                                Ya tengo una cuenta
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login-form.js') }}"></script>
</x-guest-layout>
