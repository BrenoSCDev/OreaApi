<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Notifications\ContratoProximoDoVencimento;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ContratoAvisarVencimento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contratos:avisar-vencimento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hojeMais7 = Carbon::today()->addDays(7);

        $contratos = Contrato::where('status', 'Ativo')
            ->whereDate('dtvenc', '<=', $hojeMais7)
            ->whereDate('dtvenc', '>=', Carbon::today())
            ->get();

        foreach ($contratos as $contrato) {
            $user = $contrato->user; // ou responsável pelo contrato
            if ($user) {
                $user->notify(new ContratoProximoDoVencimento($contrato));
            }
        }
    }
}
