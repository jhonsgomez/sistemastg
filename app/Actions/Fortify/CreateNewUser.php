<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\CorreoInstitucional;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'nombres' => ['required', 'string', 'min:3', 'max:255'],
            'apellidos' => ['required', 'string', 'min:3', 'max:255'],
            'tipo_documento' => ['required'],
            'nro_documento' => ['required', 'string', 'min:5', 'max:30', 'unique:users,nro_documento'],
            'nivel_id' => ['required'],
            'nro_celular' => ['required', 'numeric', 'min_digits:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new CorreoInstitucional()],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.min' => 'Los nombres deben tener mínimo :min caractéres.',
            'nombres.max' => 'Los nombres deben tener máximo :max caractéres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.min' => 'Los apellidos deben tener mínimo :min caractéres.',
            'apellidos.max' => 'Los apellidos deben tener máximo :max caractéres.',
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'nro_documento.required' => 'El número de documento es obligatorio.',
            'nro_documento.min' => 'El documento debe tener al menos :min caractéres.',
            'nro_documento.max' => 'El documento no debe tener más de :max caractéres.',
            'nro_documento.unique' => 'Ya existe un usuario con ese número de documento.',
            'email.unique' => 'Ya existe un usuario con ese correo electrónico.',
            'nivel_id.required' => 'El nivel académico es obligatorio.',
            'nro_celular.required' => 'El número de celular es obligatorio.',
            'nro_celular.numeric' => 'El número de celular debe ser numeríco.',
            'nro_celular.min_digits' => 'El número de celular debe ser tener al menos :min digitos.',
        ])->validate();

        return User::create([
            'name' => ucwords(strtolower($input['nombres'])) . ' ' . ucwords(strtolower($input['apellidos'])),
            'tipo_documento_id' => $input['tipo_documento'],
            'nro_documento' => $input['nro_documento'],
            'nivel_id' => $input['nivel_id'],
            'nro_celular' => $input['nro_celular'],
            'email' => strtolower($input['email']),
            'password' => Hash::make($input['password']),
        ]);
    }
}
