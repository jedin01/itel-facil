<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $telecom = \App\Models\Course::where('name', 'Telecomunicações')->first();
        $informatica = \App\Models\Course::where('name', 'Informática')->first();

        if ($telecom) {
            $telecom->subjects()->createMany([
                ['name' => 'Redes de Computadores I', 'description' => 'Introdução às redes de computadores.'],
                ['name' => 'Sistemas de Transmissão', 'description' => 'Estudo dos meios de transmissão de sinais.'],
                ['name' => 'Antenas e Propagação', 'description' => 'Fundamentos de antenas e propagação de ondas.'],
            ]);
        }

        if ($informatica) {
            $informatica->subjects()->createMany([
                ['name' => 'Algoritmos e Estruturas de Dados', 'description' => 'Base da programação e organização de dados.'],
                ['name' => 'Bases de Dados', 'description' => 'Modelação e gestão de bases de dados relacionais.'],
                ['name' => 'Engenharia de Software I', 'description' => 'Ciclo de vida e metodologias de desenvolvimento de software.'],
            ]);
        }
    }
}
