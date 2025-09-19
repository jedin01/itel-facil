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
        Schema::create("aluno_curso", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("id_aluno")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade");
            $table
                ->foreignId("id_curso")
                ->constrained("cursos", "id_curso")
                ->onDelete("cascade");
            $table->timestamps();

            $table->unique(["id_aluno", "id_curso"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("aluno_curso");
    }
};
