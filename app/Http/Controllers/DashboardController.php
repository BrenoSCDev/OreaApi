<?php

namespace App\Http\Controllers;

use App\Models\Bem;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $count_clientes = Cliente::count();
        $count_contratos_ativos = Contrato::where("status", "Ativo")->count();

        $patrim_estoque = Bem::whereHas('contrato', function ($query) {
            $query->where('status', '!=', 'Finalizado');
        })->sum('valor');

        $valor_investido = Contrato::whereNotIn('status', ['Finalizado', 'Aguardando Aprovação'])
            ->sum('valor_emprestimo');

        $anoAtual = Carbon::now()->year;

        // 🔹 Recebimento mensal
        $recebimento_mensal = Contrato::select(
                DB::raw('MONTH(dtrec) as mes'),
                DB::raw('SUM(valor_recebido) as total')
            )
            ->where('status', 'Finalizado')
            ->whereYear('dtrec', $anoAtual)
            ->groupBy(DB::raw('MONTH(dtrec)'))
            ->pluck('total', 'mes');

        $meses = collect(range(1, 12))->mapWithKeys(function ($mes) use ($recebimento_mensal) {
            return [$mes => $recebimento_mensal[$mes] ?? 0];
        })->values();

        // 🔹 Comparação Recompras vs Inadimplentes
        $contratos_por_status = Contrato::select(
                DB::raw('MONTH(dtvenc) as mes'),
                DB::raw("SUM(CASE WHEN status = 'Finalizado' THEN 1 ELSE 0 END) as recompras"),
                DB::raw("SUM(CASE WHEN status = 'Inadimplente' THEN 1 ELSE 0 END) as inadimplentes")
            )
            ->whereYear('dtvenc', $anoAtual)
            ->groupBy(DB::raw('MONTH(dtvenc)'))
            ->get()
            ->keyBy('mes');

        $dados_recompras = [];
        $dados_inadimplentes = [];

        foreach (range(1, 12) as $mes) {
            $dados_recompras[] = isset($contratos_por_status[$mes]) ? (int) $contratos_por_status[$mes]->recompras : 0;
            $dados_inadimplentes[] = isset($contratos_por_status[$mes]) ? (int) $contratos_por_status[$mes]->inadimplentes : 0;
        }

        // 🔹 Valores do mês e comparativos
        $hoje = Carbon::today();
        $inicioMesAtual = $hoje->copy()->startOfMonth();
        $fimMesAtual = $hoje->copy()->endOfMonth();

        $inicioMesAnterior = $inicioMesAtual->copy()->subMonth();
        $fimMesAnterior = $inicioMesAnterior->copy()->endOfMonth();

        $ontem = $hoje->copy()->subDay();

        $valor_a_receber_mensal = Contrato::whereBetween('dtvenc', [$inicioMesAtual, $fimMesAtual])->sum('valor_receber');

        $entradas_caixa_mensal = Contrato::where('status', 'Finalizado')
            ->whereBetween('dtvenc', [$inicioMesAtual, $fimMesAtual])
            ->sum('valor_receber');

        $entradas_do_dia = Transaction::whereDate('created_at', $hoje)
            ->where("tipo", "Entrada")
            ->sum('valor');

        $valor_a_receber_anterior = Contrato::whereBetween('dtvenc', [$inicioMesAnterior, $fimMesAnterior])->sum('valor_receber');

        $entradas_caixa_anterior = Contrato::where('status', 'Finalizado')
            ->whereBetween('dtvenc', [$inicioMesAnterior, $fimMesAnterior])
            ->sum('valor_receber');

        $entradas_ontem = Transaction::whereDate('created_at', $ontem)
            ->where("tipo", "Entrada")
            ->sum('valor');

        $percentual_recebido = $valor_a_receber_mensal > 0
            ? round(($entradas_caixa_mensal / $valor_a_receber_mensal) * 100, 2)
            : 0;

        return response()->json([
            "count_clientes" => $count_clientes,
            "count_contratos_ativos" => $count_contratos_ativos,
            "patrim_estoque" => $patrim_estoque,
            "valor_investido" => $valor_investido,
            "recebimento_mensal" => $meses,

            "valor_a_receber_mensal" => $valor_a_receber_mensal,
            "entradas_caixa_mensal" => $entradas_caixa_mensal,
            "entradas_do_dia" => $entradas_do_dia,
            "percentual_recebido" => $percentual_recebido,

            "comparativo" => [
                "valor_a_receber_anterior" => $valor_a_receber_anterior,
                "entradas_caixa_anterior" => $entradas_caixa_anterior,
                "entradas_ontem" => $entradas_ontem,
            ],

            // Novo retorno: contratos por status por mês
            "dados_recompras" => $dados_recompras,
            "dados_inadimplentes" => $dados_inadimplentes,
        ]);
    }

}
