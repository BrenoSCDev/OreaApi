<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Transaction;
use App\Models\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GastoController extends Controller
{
    public function index()
    {
        $gastos = Gasto::with('tipoGasto')->orderBy('created_at', 'desc')->get();
        return response()->json(['gastos' => $gastos]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_gasto_id' => 'required|exists:tipo_gastos,id',
            'desc' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01'
        ]);

        $gasto = new Gasto();
        $gasto->tipo_gasto_id = $request->tipo_gasto_id;
        $gasto->desc = $request->desc;
        $gasto->valor = $request->valor;
        $gasto->save();

        $valorFormatado = number_format($gasto->valor, 2, ',', '.');

        $transaction = new Transaction();
        $transaction->tipo = "Saida";
        $transaction->valor = $gasto->valor;
        $transaction->desc = "Gasto registrado: {$gasto->desc} no valor de R$ {$valorFormatado}.";
        $transaction->save();

        $saldo = Saldo::first();
        $saldo->valor -= $gasto->valor;
        $saldo->save();

        $gasto->load('tipoGasto');

        return response()->json([
            'message' => 'Gasto registrado com sucesso!',
            'gasto' => $gasto
        ], 201);
    }
}
