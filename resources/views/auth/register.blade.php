<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" id="registerUserForm" onsubmit="confirmRegister(event, 'registerUserForm', '{{ route('register') }}')" class="py-4 px-4">
            @csrf

            <h2 class="text-center text-2xl font-semibold text-gray-500 mb-4">Crear <span class="bg-gray-500 text-white text-base font-semibold px-2 py-2 rounded uppercase">Cuenta</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6 mt-8">
                <div>
                    <label for="nombres" class="block font-medium text-sm text-gray-700">
                        <i class="fa-regular fa-user text-gray-500 mr-1"></i>Nombres:
                    </label>
                    <x-input type="text" name="nombres" id="nombres"
                        :value="old('nombres')"
                        placeholder="Ingrese sus nombres."
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" />
                </div>
                <div>
                    <label for="apellidos" class="block font-medium text-sm text-gray-700">
                        <i class="fa-regular fa-user text-gray-500 mr-1"></i>Apellidos:
                    </label>
                    <x-input type="text" name="apellidos" id="apellidos"
                        :value="old('apellidos')"
                        placeholder="Ingrese sus apellidos."
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" />
                </div>
                <div>
                    <label for="tipo_documento" class="block font-medium text-sm text-gray-700">
                        <i class="fa-regular fa-id-card text-gray-500 mr-1"></i>Tipo de documento:
                    </label>
                    <select name="tipo_documento" id="tipo_documento"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                        <option value="" selected disabled>Selecciona una opción</option>
                        @foreach ($tipos_documentos as $tipo)
                        <option value="{{ $tipo->id }}" {{ old('tipo_documento') == $tipo->id ? 'selected' : ''  }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="nro_documento" class="block font-medium text-sm text-gray-700">
                        <i class="fa-regular fa-id-card text-gray-500 mr-1"></i>N° Documento:
                    </label>
                    <x-input type="text" name="nro_documento" id="nro_documento"
                        :value="old('nro_documento')"
                        placeholder="Ingrese su documento."
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" />
                </div>
                <div>
                    <label for="nivel_id" class="block font-medium text-sm text-gray-700">
                        <i class="fa-solid fa-graduation-cap text-gray-500 mr-1"></i>Nivel académico:
                    </label>
                    <select name="nivel_id" id="nivel_id"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                        <option value="" selected disabled>Selecciona una opción</option>
                        @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id }}" {{ old('nivel_id') == $nivel->id ? 'selected' : ''  }}>{{ $nivel->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="nro_celular" class="block font-medium text-sm text-gray-700">
                        <i class="fa-solid fa-phone-volume text-gray-500 mr-1"></i>N° Celular:
                    </label>
                    <x-input type="text" name="nro_celular" id="nro_celular"
                        :value="old('nro_celular')"
                        placeholder="Ingrese su número de celular."
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" />
                </div>
            </div>

            <div class="mt-4">
                <div class="flex justify-start items-center">
                    <i class="fa-regular fa-envelope text-gray-500"></i>
                    &nbsp;
                    <x-label for="email" value="{{ __('Correo:') }}" />
                </div>
                <x-input id="email" class="block mt-1 w-full focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese su correo institucional." type="email" name="email" :value="old('email')" autocomplete="username" />
            </div>

            <div class="mt-4">
                <div class="flex justify-start items-center">
                    <i class="fa-solid fa-lock text-gray-500"></i>
                    &nbsp;
                    <x-label for="password" value="{{ __('Contraseña:') }}" />
                </div>
                <x-input id="password" class="block mt-1 w-full focus:ring-uts-500 focus:border-uts-500" placeholder="*********" type="password" name="password" autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <div class="flex justify-start items-center">
                    <i class="fa-solid fa-lock text-gray-500"></i>
                    &nbsp;
                    <x-label for="password_confirmation" value="{{ __('Confirmar contraseña:') }}" />
                </div>
                <x-input id="password_confirmation" class="block mt-1 w-full focus:ring-uts-500 focus:border-uts-500" placeholder="*********" type="password" name="password_confirmation" autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                <x-label for="terms">
                    <div class="flex items-center">
                        <x-checkbox name="terms" id="terms" required />

                        <div class="ms-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
            </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uts-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4 bg-uts-500 hover:bg-uts-800 active:bg-uts-800 focus:bg-uts-800 focus:ring-uts-500">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
    @push('scripts')
    <script>
        function confirmRegister(event, id, action) {
            event.preventDefault();

            const form = document.getElementById(id);

            form.action = action;

            Swal.fire({
                heightAuto: false,
                title: '¿Está seguro?',
                text: "Desea enviar la información",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C1D631',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
    @endpush
</x-guest-layout>