<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePracticaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'tiene_empresa' => [
                'required',
                'in:0,1'
            ],

            'hoja_vida' => [
                'required_if:tiene_empresa,0',
                'file',
                'mimes:pdf,doc,docx',
                'max:2048'
            ],

            'id_integrante_2' => [
                'nullable',
                'different:user_id'
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'tiene_empresa.required' =>
                'Debes seleccionar si tienes empresa o no.',

            'tiene_empresa.in' =>
                'Valor inválido para empresa.',

            'hoja_vida.required_if' =>
                'Debes subir la hoja de vida si NO cuentas con empresa.',

            'hoja_vida.file' =>
                'La hoja de vida debe ser un archivo válido.',

            'hoja_vida.mimes' =>
                'La hoja de vida debe ser PDF o Word.',

            'hoja_vida.max' =>
                'La hoja de vida no puede superar 2MB.',

            'id_integrante_2.different' =>
                'No puede seleccionarse a sí mismo como compañero.',
        ];
    }
}