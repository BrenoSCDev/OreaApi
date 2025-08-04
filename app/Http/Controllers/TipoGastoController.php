<?php

namespace App\Http\Controllers;

use App\Models\TipoGasto;
use Illuminate\Http\Request;

class TipoGastoController extends Controller
{
    public function index()
    {
        return response()->json(['tipos' => TipoGasto::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
        ]);

        $tipo = new TipoGasto();
        $tipo->nome = $request->nome;

        $tipo->save();

        return response()->json(['message' => 'Novo Tipo de Gasto cadastrado com sucesso!', 'tipo' => $tipo], 201);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
        ]);

        $tipo = TipoGasto::find($request->id);
        $tipo->nome = $request->nome;

        $tipo->save();

        return response()->json(['message' => 'Tipo de Gasto editado com sucesso!', 'tipo' => $tipo], 201);
    }
}
