<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("eventos", function (Blueprint $table) {
            $table->id("id_evento");
            $table->string("titulo");
            $table->text("descricao")->nullable();
            $table->dateTime("data_inicio");
            $table->dateTime("data_fim");
            $table->enum("tipo", ["avaliacao", "defesa", "feriado", "outro"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("eventos");
    }
};
