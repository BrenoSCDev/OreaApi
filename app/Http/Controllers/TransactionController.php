<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function indexGroupedByDate()
    {
        // Busca todas as transações com seus contratos, ordena por data decrescente
        $transactions = Transaction::with('contrato')->orderBy('created_at', 'desc')->get();

        // Agrupa por data (apenas dia)
        $grouped = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->created_at)->format('Y-m-d');
        });

        // Ordena os grupos por data decrescente
        $sorted = $grouped->sortKeysDesc();

        return response()->json([
            'success' => true,
            'data' => $sorted
        ]);
    }

    public function indexGroupedByDateFilter(Request $request)
    {
        // Busca todas as transações com seus contratos, ordena por data decrescente
        $transactions = Transaction::with('contrato')->orderBy('created_at', 'desc')->where('tipo', $request->tipo)->get();

        // Agrupa por data (apenas dia)
        $grouped = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->created_at)->format('Y-m-d');
        });

        // Ordena os grupos por data decrescente
        $sorted = $grouped->sortKeysDesc();

        return response()->json([
            'success' => true,
            'data' => $sorted
        ]);
    }
}
