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
        Schema::create('bems', function (Blueprint $table) {
            $table->id();
            $table->string("nome");
            $table->text("desc");
            $table->string("foto")->nullable();
            $table->decimal('valor', 18, 2);
            $table->foreignId('tipo_bem_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bems', function (Blueprint $table) {
            $table->dropForeign(['tipo_bem_id']);
        });
        Schema::dropIfExists('bems');
    }
};
