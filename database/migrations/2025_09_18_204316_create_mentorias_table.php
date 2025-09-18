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
        Schema::create("mentorias", function (Blueprint $table) {
            $table->id("id_mentoria");
            $table
                ->foreignId("id_aluno_solicitante")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade");
            $table
                ->foreignId("id_veterano")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade");
            $table
                ->foreignId("id_disciplina")
                ->constrained("disciplinas", "id_disciplina")
                ->onDelete("cascade");
            $table->dateTime("data_hora");
            $table
                ->enum("status", ["agendada", "concluida", "cancelada"])
                ->default("agendada");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("mentorias");
    }
};
