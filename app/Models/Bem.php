<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bem extends Model
{
    use HasFactory;

    public function tipo()
    {
        return $this->belongsTo(TipoBem::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
}
