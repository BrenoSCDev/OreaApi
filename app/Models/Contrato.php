<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function bem()
    {
        return $this->hasOne(Bem::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
