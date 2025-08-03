<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VerificarContratosVencidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contratos:verificar-vencidos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status de contratos vencidos para "Vencido"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoje = Carbon::today();

        $vencidos = Contrato::where('dtvenc', '<=', $hoje)
            ->where('status', '!=', 'Vencido')
            ->update(['status' => 'Vencido']);

        $this->info("Contratos atualizados para 'Vencido': {$vencidos}");
    }
}
