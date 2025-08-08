<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="py-4 px-4">
            <h2 class="text-center text-2xl font-semibold text-gray-500 mb-4">Iniciar <span class="bg-gray-500 text-white text-base font-semibold px-2 py-2 rounded uppercase">Sesión</span>
            </h2>
            @csrf
            <div class="mt-4">
                <div class="flex justify-start items-center">
                    <i class="fa-regular fa-envelope text-gray-500"></i>&nbsp;
                    <x-label for="email" value="{{ __('Correo:') }}" />
                </div>
                <x-input id="email" class="block mt-1 w-full focus:ring-uts-500 focus:border-uts-500"
                    type="email" name="email" placeholder="Ingrese su correo institucional." :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <div class="flex justify-start items-center">
                    <i class="fa-solid fa-lock text-gray-500"></i>
                    &nbsp;
                    <x-label for="password" value="{{ __('Contraseña:') }}" />
                </div>
                <x-input id="password" class="block mt-1 w-full focus:ring-uts-500 focus:border-uts-500"
                    type="password" name="password" placeholder="*********" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" class="focus:ring-uts-500 focus:border-uts-500 checked:bg-uts-500 checked:text-uts-500" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-4">
                <x-button class="ms-4 bg-uts-500 hover:bg-uts-800 active:bg-uts-800 focus:bg-uts-800 focus:ring-uts-500">
                    {{ __('Send') }}
                </x-button>
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="{{ route('register') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uts-500">Crear cuenta</a>
                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uts-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>