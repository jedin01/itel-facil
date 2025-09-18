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
        Schema::create("comentarios", function (Blueprint $table) {
            $table->id("id_comentario");
            $table
                ->foreignId("id_postagem")
                ->constrained("postagens", "id_postagem")
                ->onDelete("cascade");
            $table
                ->foreignId("id_aluno")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade");
            $table->text("conteudo");
            $table->timestamp("data_comentario")->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("comentarios");
    }
};
