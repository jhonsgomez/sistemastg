<?php

namespace App\Http\Controllers;

use App\Models\Nivel;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function getNivel($id)
    {
        $nivel = Nivel::findOrFail($id);
        return response()->json($nivel);
    }
}
