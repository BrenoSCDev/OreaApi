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

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function indexGroupedByDateFilter(Request $request)
    {
        // Busca todas as transações com seus contratos, ordena por data decrescente
        $transactions = Transaction::with('contrato')->orderBy('created_at', 'desc')->where('tipo', $request->tipo)->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}
