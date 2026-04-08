<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PracticaController extends Controller
{
    public function index()
    {
        return view('practicas.index');
    }

    public function getData()
    {
       /* $data = [];

        return response()->json($data);*/

        /* Prueba */
        return response()->json([
        ['id' => 1, 'nombre' => 'Práctica 1'],
        ['id' => 2, 'nombre' => 'Práctica 2'],
    ]);
    }
}
