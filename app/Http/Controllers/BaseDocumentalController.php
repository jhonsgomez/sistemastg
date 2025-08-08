<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class BaseDocumentalController extends Controller
{
    public function index()
    {
        try {
            return view('documental.index');
        } catch (Exception $e) {
            return response()->json(['message' => 'Ha ocurrido un error. Por favor, inténtelo de nuevo.'], 500);
        }
    }
}
