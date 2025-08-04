<?php

namespace App\Http\Controllers;

use App\Models\ModeloContrato;
use Illuminate\Http\Request;

class ModeloContratoController extends Controller
{
    public function index()
    {
        $contratos = ModeloContrato::get();

        return response()->json(['contratos' => $contratos]);
    }

    public function store(Request $request)
    {
        $filePath = $request->file('arquivo')->store('modelos_contratos', 'public');

        $modelo = new ModeloContrato();
        $modelo->titulo = $request->titulo;
        $modelo->arquivo_path = $filePath;
        $modelo->save();

        return response()->json(['message' => 'Modelo de contrato salvo com sucesso!', 'contrato' => $modelo], 201);
    }
}
