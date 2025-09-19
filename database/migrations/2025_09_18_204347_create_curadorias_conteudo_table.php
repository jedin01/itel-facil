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
        Schema::create("curadorias_conteudo", function (Blueprint $table) {
            $table->id("id_curadoria");
            $table
                ->foreignId("id_material")
                ->nullable()
                ->constrained("materiais_didaticos", "id_material")
                ->onDelete("cascade");
            $table
                ->foreignId("id_enunciado")
                ->nullable()
                ->constrained("enunciados", "id_enunciado")
                ->onDelete("cascade");
            $table
                ->foreignId("id_aluno_curador")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade");
            $table
                ->enum("status", ["aprovado", "rejeitado", "pendente"])
                ->default("pendente");
            $table->timestamp("data_avaliacao")->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("curadorias_conteudo");
    }
};
