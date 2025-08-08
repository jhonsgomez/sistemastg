<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CorreoInstitucional implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dominiosPermitidos = ['uts.edu.co', 'correo.uts.edu.co'];
        $dominioCorreo = substr(strrchr($value, "@"), 1);

        if (!in_array($dominioCorreo, $dominiosPermitidos)) {
            $fail("Solo se permiten los correos institucionales.");
        }
    }
}
