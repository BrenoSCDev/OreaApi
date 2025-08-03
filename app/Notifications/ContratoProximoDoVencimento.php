<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Contrato;

class ContratoProximoDoVencimento extends Notification
{
    use Queueable;

    public $contrato;

    public function __construct(Contrato $contrato)
    {
        $this->contrato = $contrato;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Contrato prestes a vencer',
            'message' => "O contrato {$this->contrato->id} vence em breve.",
            'contrato_id' => $this->contrato->id,
            'dtvenc' => $this->contrato->dtvenc,
        ];
    }
}
