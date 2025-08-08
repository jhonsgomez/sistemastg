<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <div class="flex justify-start items-center">
                <i class="fa-solid fa-key text-gray-500"></i>
                &nbsp;
                <x-label for="current_password" value="{{ __('Contraseña actual:') }}" />
            </div>

            <x-input id="current_password" type="password" class="mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" placeholder="*********" wire:model="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <div class="flex justify-start items-center">
                <i class="fa-solid fa-lock text-gray-500"></i>
                &nbsp;
                <x-label for="password" value="{{ __('Contraseña nueva:') }}" />
            </div>

            <x-input id="password" type="password" class="mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" placeholder="*********" wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <div class="flex justify-start items-center">
                <i class="fa-solid fa-lock text-gray-500"></i>
                &nbsp;
                <x-label for="password_confirmation" value="{{ __('Confirmar contraseña:') }}" />
            </div>

            <x-input id="password_confirmation" type="password" class="mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" placeholder="*********" wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3 text-green-600" on="saved">
            {{ __('Guardado correctamente.') }}
        </x-action-message>

        <x-button class="me-3 bg-uts-500 hover:bg-uts-800 active:bg-uts-800 focus:bg-uts-800 focus:ring-uts-500">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>