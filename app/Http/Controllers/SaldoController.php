<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function index()
    {
        $saldo = Saldo::first();

        return response()->json(['saldo' => $saldo], 200);
    }

    public function edit(Request $request)
    {
        $saldo = Saldo::first();
        $saldo->valor = $request->valor;
        $saldo->save();

        return response()->json(['message' => 'Saldo editado com sucesso!'], 200);
    }
}
