<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('contrato_hash', 12)->unique();
            $table->string("status");
            $table->string("documento_path");
            $table->decimal('valor_emprestimo', 18, 2);
            $table->decimal('valor_receber', 18, 2);
            $table->integer('juros');
            $table->date('dtvenc');
            $table->foreignId('cliente_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
