<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\CorreoInstitucional;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        $validator_estudiante = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'tipo_documento_id' => ['required'],
            'nro_documento' => ['required', 'string', 'min:5', 'max:30', 'unique:users,nro_documento,' . $user->id],
            'nivel_id' => ['required'],
            'nro_celular' => ['required', 'numeric', 'min_digits:10'],
            'email' => ['required', 'email', 'max:255', new CorreoInstitucional(), Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];

        $validator_general = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'tipo_documento_id' => ['required'],
            'nro_documento' => ['required', 'string', 'min:5', 'max:30', 'unique:users,nro_documento,' . $user->id],
            'email' => ['required', 'email', 'max:255', new CorreoInstitucional(), Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];

        $rules = $validator_general;

        if (auth()->user()->hasRole(['estudiante'])) {
            $rules = $validator_estudiante;
        }

        Validator::make($input, $rules, [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener mínimo :min caractéres.',
            'name.max' => 'El nombre debe tener máximo :max caractéres.',
            'tipo_documento_id.required' => 'El tipo de documento es obligatorio.',
            'nro_documento.required' => 'El número de documento es obligatorio.',
            'nro_documento.min' => 'El documento debe tener al menos :min caractéres.',
            'nro_documento.max' => 'El documento no debe tener más de :max caractéres.',
            'nro_documento.unique' => 'Ya existe un usuario con ese número de documento.',
            'nivel_id.required' => 'El nivel académico es obligatorio.',
            'nro_celular.required' => 'El número de celular es obligatorio.',
            'nro_celular.numeric' => 'El número de celular debe ser numeríco.',
            'nro_celular.min_digits' => 'El número de celular debe ser tener al menos :min digitos.',
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'tipo_documento_id' => $input['tipo_documento_id'],
                'nro_documento' => $input['nro_documento'],
                'nivel_id' => $input['nivel_id'],
                'nro_celular' => $input['nro_celular']
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
