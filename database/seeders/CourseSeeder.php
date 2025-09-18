<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Course::create(['name' => 'Telecomunicações', 'description' => 'Curso focado em sistemas de telecomunicação, redes e tecnologias associadas.']);
        \App\Models\Course::create(['name' => 'Informática', 'description' => 'Curso abrangente sobre desenvolvimento de software, sistemas de informação e computação.']);
        \App\Models\Course::create(['name' => 'Eletrónica', 'description' => 'Curso dedicado ao estudo de circuitos eletrónicos, automação e sistemas embarcados.']);
    }
}
