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
        Schema::create("postagens", function (Blueprint $table) {
            $table->id("id_postagem");
            $table
                ->foreignId("id_aluno")
                ->constrained("alunos", "id_aluno")
                ->onDelete("cascade");
            $table->string("titulo");
            $table->text("conteudo");
            $table->timestamp("data_postagem")->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("postagens");
    }
};
