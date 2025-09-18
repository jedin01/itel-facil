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
        Schema::create("alunos", function (Blueprint $table) {
            $table->id("id_aluno");
            $table->string("nome_completo");
            $table->string("email")->unique();
            $table->string("senha");
            $table->string("ano_escolar"); // ex.: 10º, 11º, 12º
            $table->enum("tipo", ["aluno", "veterano"])->default("aluno");
            $table->timestamp("data_cadastro")->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("alunos");
    }
};
