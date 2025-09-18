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
        Schema::create("disciplinas", function (Blueprint $table) {
            $table->id("id_disciplina");
            $table->string("nome");
            $table->text("descricao")->nullable();
            $table
                ->foreignId("id_curso")
                ->constrained("cursos", "id_curso")
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("disciplinas");
    }
};
