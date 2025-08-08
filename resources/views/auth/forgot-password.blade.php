<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="{{ url('/img/programa-logo.png') }}" style="max-width: 300px;" alt="Ingeniería UTS">
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese su correo institucional." type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('login') }}" class="mr-2 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uts-500">Regresar</a>
                <x-button class="bg-uts-500 hover:bg-uts-800 active:bg-uts-800 focus:bg-uts-800 focus:ring-uts-500">
                    {{ __('Enviar') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>