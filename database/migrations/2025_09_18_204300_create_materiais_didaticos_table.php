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
        Schema::create("materiais_didaticos", function (Blueprint $table) {
            $table->id("id_material");
            $table->string("titulo");
            $table->enum("tipo", ["slide", "livro", "resumo", "tutorial"]);
            $table->string("arquivo_url");
            $table
                ->foreignId("id_disciplina")
                ->constrained("disciplinas", "id_disciplina")
                ->onDelete("cascade");
            $table
                ->foreignId("id_aluno")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("materiais_didaticos");
    }
};
