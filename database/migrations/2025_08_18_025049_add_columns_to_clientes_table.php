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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('rg')->nullable();
            $table->string('nacionalidade')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('profissao')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('rg');
            $table->dropColumn('nacionalidade');
            $table->dropColumn('estado_civil');
            $table->dropColumn('profissao');
        });
    }
};
