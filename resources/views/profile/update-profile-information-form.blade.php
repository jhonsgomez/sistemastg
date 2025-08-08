<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
            <!-- Profile Photo File Input -->
            <input type="file" id="photo" class="hidden"
                wire:model.live="photo"
                x-ref="photo"
                x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

            <x-label for="photo" value="{{ __('Photo') }}" />

            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
                <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview" style="display: none;">
                <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                </span>
            </div>

            <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                {{ __('Select A New Photo') }}
            </x-secondary-button>

            @if ($this->user->profile_photo_path)
            <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                {{ __('Remove Photo') }}
            </x-secondary-button>
            @endif

            <x-input-error for="photo" class="mt-2" />
        </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <div class="flex justify-start items-center">
                <i class="fa-regular fa-user text-gray-500"></i>
                &nbsp;
                <x-label for="name" value="{{ __('Nombre:') }}" />
            </div>
            <x-input id="name" type="text" class="mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese su nombre." wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4 grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-6 mt-4 mb-4">
            <div>
                <div class="flex justify-start items-center">
                    <i class="fa-solid fa-id-card text-gray-500"></i>
                    &nbsp;
                    <x-label for="tipo_documento_id" value="{{ __('Tipo de documento:') }}" />
                </div>
                <select name="tipo_documento_id" id="tipo_documento_id" wire:model="state.tipo_documento_id"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" required>
                    <option value="" selected>Selecciona una opción</option>
                    @foreach ($tipos_documentos as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
                <x-input-error for="tipo_documento_id" class="mt-2" />
            </div>

            <div>
                <div class="flex justify-start items-center">
                    <i class="fa-solid fa-address-card text-gray-500"></i>
                    &nbsp;
                    <x-label for="nro_documento" value="{{ __('N° documento:') }}" />
                </div>
                @if (isset($user->nro_documento))
                <x-input id="nro_documento" type="text" class="mt-1 bg-gray-100 block w-full focus:ring-uts-500 focus:border-uts-500 cursor-not-allowed" placeholder="Ingrese su número de documento." wire:model="state.nro_documento" required disabled readonly />
                @else
                <x-input id="nro_documento" type="text" class="mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese su número de documento." wire:model="state.nro_documento" required />
                @endif
                <x-input-error for="nro_documento" class="mt-2" />
            </div>

            @if(auth()->user()->hasRole(['estudiante']))
                <div>
                    <div class="flex justify-start items-center">
                        <i class="fa-solid fa-graduation-cap text-gray-500"></i>
                        &nbsp;
                        <x-label for="nivel_id" value="{{ __('Nivel académico:') }}" />
                    </div>
                    <select name="nivel_id" id="nivel_id" wire:model="state.nivel_id"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                        required>
                        <option value="" selected>Selecciona una opción</option>
                        @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="nivel_id" class="mt-2" />
                </div>

                <div>
                    <div class="flex justify-start items-center">
                        <i class="fa-solid fa-phone-volume text-gray-500"></i>
                        &nbsp;
                        <x-label for="nro_celular" value="{{ __('N° Celular:') }}" />
                    </div>
                    <x-input id="nro_celular" type="text" class="mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese su número de celular." wire:model="state.nro_celular" required />
                    <x-input-error for="nro_celular" class="mt-2" />
                </div>
            @endif
        </div>

        <div class="col-span-6 sm:col-span-4">
            <div class="flex justify-start items-center">
                <i class="fa-regular fa-envelope text-gray-500"></i>
                &nbsp;
                <x-label for="email" value="{{ __('Correo:') }}" />
            </div>
            <x-input id="email" type="email" class="mt-1 block w-full focus:ring-uts-500 focus:border-uts-500 cursor-not-allowed bg-gray-100" placeholder="Ingrese su correo institucional." wire:model="state.email" required autocomplete="username" disabled readonly />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
            <p class="text-sm mt-2">
                {{ __('Your email address is unverified.') }}

                <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                    {{ __('Click here to re-send the verification email.') }}
                </button>
            </p>

            @if ($this->verificationLinkSent)
            <p class="mt-2 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to your email address.') }}
            </p>
            @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3 text-green-600" on="saved">
            {{ __('Guardado correctamente.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" class="me-3 bg-uts-500 hover:bg-uts-800 active:bg-uts-800 focus:bg-uts-800 focus:ring-uts-500" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>