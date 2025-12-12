<x-guest-layout>
    <div class="login-bg min-h-screen flex items-center justify-center">
        <div class="login-shell">
            {{-- IZQUIERDA: IMAGEN --}}
            <div class="login-left">
            <div class="login-image-wrapper">
                <img src="{{ asset('images/login-medicos.png') }}" alt="Médicos" class="login-image">
            </div>
             </div>

            {{-- DERECHA: TARJETA BLANCA DE LOGIN --}}
            <div class="login-right">
                <div class="login-card">
                    <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 tracking-[0.2em] uppercase">
                    Inicio Sesion </h1>
                    <p class="text-xs text-gray-500 mt-2">Por favor ingresa tus credenciales</p>
                </div>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Usuario / Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Correo Electronico')" />
                        <x-text-input
                            id="email"
                            class="login-input"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Your username"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs ml-2" />
                            </div>

                                    {{-- Password --}}
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Contraseña')" />

                        <div class="relative">
                            <x-text-input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Password"
                                class="login-input pr-10"
                            />

                            {{-- Botón ojo dentro del campo --}}
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


                            {{-- Botón ojo --}}
                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-4 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none"
                            >
                                {{-- ojo abierto --}}
                                <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                {{-- ojo cerrado (oculto por defecto) --}}
                                <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M3 3l18 18M10.584 10.587A3 3 0 0113.412 13.41M9.88 9.88L9.88 9.88M6.228 6.228C4.45 7.39 3.186 9.138 2.458 12c1.274 4.057 5.065 7 9.542 7 1.41 0 2.76-.25 4.01-.708M17.772 17.772C19.55 16.61 20.814 14.862 21.542 12 20.268 7.943 16.477 5 12 5c-.94 0-1.852.11-2.722.317" />
                                </svg>

                            </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs ml-2" />

                        {{-- Botón Login --}}
                        <div class="flex flex-col items-center gap-3 mt-2">
                            <button type="submit" class="login-button">
                                Ingresar
                            </button>

                            {{-- Botón Registrar debajo --}}
                            <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center px-8 py-2 rounded-full
                                    border border-blue-500 text-blue-600 text-sm font-semibold
                                    hover:bg-blue-50 transition-colors">
                                Registrar
                            </a>
                        </div>

                        {{-- Remember + Forgot debajo del password --}}
                        <div class="mt-3 text-xs text-gray-600 flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    class="h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    name="remember"
                                >
                                <span class="ml-2">Remember Me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="underline underline-offset-2 hover:text-blue-800">
                                    Forgot your password?
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script (usaremos para cargar estilos personalizados si quieres) --}}
    <script src="{{ asset('js/login-form.js') }}"></script>
</x-guest-layout>

