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
        Schema::create("garimpos", function (Blueprint $table) {
            $table->id("id_garimpo");
            $table
                ->foreignId("id_aluno")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade")
                ->unique(); // 1:1 relationship
            $table->text("areas_interesse");
            $table->text("disponibilidade");
            $table->text("descricao");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("garimpos");
    }
};
