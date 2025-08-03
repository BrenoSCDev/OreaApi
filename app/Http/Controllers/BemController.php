<?php

namespace App\Http\Controllers;

use App\Models\Bem;
use Illuminate\Http\Request;

class BemController extends Controller
{
    public function index()
    {
        return response()->json(['bens' => Bem::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
        ]);

        $bem = new Bem();
        $bem->nome = $request->nome;
        $bem->desc = $request->desc;
        $bem->valor = $request->valor;
        $bem->tipo_bem_id = $request->tipo_bem_id;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('bens', 'public');
            $bem->foto = $path;
        }

        $bem->save();

        return response()->json(['message' => 'Novo Tipo de Bem cadastrado com sucesso!', 'bem' => $bem], 201);
    }
}
