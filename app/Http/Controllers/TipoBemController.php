<?php

namespace App\Http\Controllers;

use App\Models\TipoBem;
use Illuminate\Http\Request;

class TipoBemController extends Controller
{
    public function index()
    {
        return response()->json(['tipos' => TipoBem::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
        ]);

        $tipo = new TipoBem();
        $tipo->nome = $request->nome;

        $tipo->save();

        return response()->json(['message' => 'Novo Tipo de Bem cadastrado com sucesso!', 'tipo' => $tipo], 201);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
        ]);

        $tipo = TipoBem::find($request->id);
        $tipo->nome = $request->nome;

        $tipo->save();

        return response()->json(['message' => 'Tipo de Bem editado com sucesso!', 'tipo' => $tipo], 201);
    }
}
