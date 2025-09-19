<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\EnunciadoController;
use App\Http\Controllers\MaterialDidaticoController;
use App\Http\Controllers\GarimpoController;
use App\Http\Controllers\MentoriaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PostagemController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\CuradoriaConteudoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas dos Alunos
Route::prefix('alunos')->group(function () {
    Route::get('/', [AlunoController::class, 'index']);
    Route::post('/', [AlunoController::class, 'store']);
    Route::get('/buscar', [AlunoController::class, 'buscar']);
    Route::get('/veteranos', [AlunoController::class, 'veteranos']);
    Route::get('/{id}', [AlunoController::class, 'show']);
    Route::put('/{id}', [AlunoController::class, 'update']);
    Route::delete('/{id}', [AlunoController::class, 'destroy']);
    Route::get('/{id}/estatisticas', [AlunoController::class, 'estatisticas']);
});

// Rotas dos Cursos
Route::prefix('cursos')->group(function () {
    Route::get('/', [CursoController::class, 'index']);
    Route::post('/', [CursoController::class, 'store']);
    Route::get('/buscar', [CursoController::class, 'buscar']);
    Route::get('/{id}', [CursoController::class, 'show']);
    Route::put('/{id}', [CursoController::class, 'update']);
    Route::delete('/{id}', [CursoController::class, 'destroy']);
    Route::get('/{id}/estatisticas', [CursoController::class, 'estatisticas']);
    Route::get('/{id}/alunos', [CursoController::class, 'alunos']);
    Route::get('/{id}/disciplinas', [CursoController::class, 'disciplinas']);
    Route::post('/{id}/alunos', [CursoController::class, 'associarAluno']);
    Route::delete('/{id}/alunos/{alunoId}', [CursoController::class, 'desassociarAluno']);
});

// Rotas das Disciplinas
Route::prefix('disciplinas')->group(function () {
    Route::get('/', [DisciplinaController::class, 'index']);
    Route::post('/', [DisciplinaController::class, 'store']);
    Route::get('/buscar', [DisciplinaController::class, 'buscar']);
    Route::get('/curso/{cursoId}', [DisciplinaController::class, 'porCurso']);
    Route::get('/{id}', [DisciplinaController::class, 'show']);
    Route::put('/{id}', [DisciplinaController::class, 'update']);
    Route::delete('/{id}', [DisciplinaController::class, 'destroy']);
    Route::get('/{id}/estatisticas', [DisciplinaController::class, 'estatisticas']);
    Route::get('/{id}/enunciados', [DisciplinaController::class, 'enunciados']);
    Route::get('/{id}/materiais-didaticos', [DisciplinaController::class, 'materiaisDidaticos']);
    Route::get('/{id}/mentorias', [DisciplinaController::class, 'mentorias']);
});

// Rotas dos Enunciados
Route::prefix('enunciados')->group(function () {
    Route::get('/', [EnunciadoController::class, 'index']);
    Route::post('/', [EnunciadoController::class, 'store']);
    Route::get('/buscar', [EnunciadoController::class, 'buscar']);
    Route::get('/estatisticas', [EnunciadoController::class, 'estatisticas']);
    Route::get('/aprovados', [EnunciadoController::class, 'aprovados']);
    Route::get('/pendentes', [EnunciadoController::class, 'pendentes']);
    Route::get('/tipo/{tipo}', [EnunciadoController::class, 'porTipo']);
    Route::get('/disciplina/{disciplinaId}', [EnunciadoController::class, 'porDisciplina']);
    Route::get('/aluno/{alunoId}', [EnunciadoController::class, 'porAluno']);
    Route::get('/{id}', [EnunciadoController::class, 'show']);
    Route::put('/{id}', [EnunciadoController::class, 'update']);
    Route::delete('/{id}', [EnunciadoController::class, 'destroy']);
    Route::get('/{id}/download', [EnunciadoController::class, 'download']);
});

// Rotas dos Materiais Didáticos
Route::prefix('materiais-didaticos')->group(function () {
    Route::get('/', [MaterialDidaticoController::class, 'index']);
    Route::post('/', [MaterialDidaticoController::class, 'store']);
    Route::get('/buscar', [MaterialDidaticoController::class, 'buscar']);
    Route::get('/estatisticas', [MaterialDidaticoController::class, 'estatisticas']);
    Route::get('/aprovados', [MaterialDidaticoController::class, 'aprovados']);
    Route::get('/pendentes', [MaterialDidaticoController::class, 'pendentes']);
    Route::get('/tipo/{tipo}', [MaterialDidaticoController::class, 'porTipo']);
    Route::get('/categoria/{categoria}', [MaterialDidaticoController::class, 'porCategoria']);
    Route::get('/disciplina/{disciplinaId}', [MaterialDidaticoController::class, 'porDisciplina']);
    Route::get('/aluno/{alunoId}', [MaterialDidaticoController::class, 'porAluno']);
    Route::get('/{id}', [MaterialDidaticoController::class, 'show']);
    Route::put('/{id}', [MaterialDidaticoController::class, 'update']);
    Route::delete('/{id}', [MaterialDidaticoController::class, 'destroy']);
    Route::get('/{id}/download', [MaterialDidaticoController::class, 'download']);
});

// Rotas dos Garimpos
Route::prefix('garimpos')->group(function () {
    Route::get('/', [GarimpoController::class, 'index']);
    Route::post('/', [GarimpoController::class, 'store']);
    Route::get('/buscar', [GarimpoController::class, 'buscar']);
    Route::get('/disponiveis', [GarimpoController::class, 'disponiveis']);
    Route::get('/por-area/{area}', [GarimpoController::class, 'porArea']);
    Route::get('/{id}', [GarimpoController::class, 'show']);
    Route::put('/{id}', [GarimpoController::class, 'update']);
    Route::delete('/{id}', [GarimpoController::class, 'destroy']);
    Route::get('/aluno/{alunoId}', [GarimpoController::class, 'porAluno']);
});

// Rotas das Mentorias
Route::prefix('mentorias')->group(function () {
    Route::get('/', [MentoriaController::class, 'index']);
    Route::post('/', [MentoriaController::class, 'store']);
    Route::get('/buscar', [MentoriaController::class, 'buscar']);
    Route::get('/estatisticas', [MentoriaController::class, 'estatisticas']);
    Route::get('/agendadas', [MentoriaController::class, 'agendadas']);
    Route::get('/concluidas', [MentoriaController::class, 'concluidas']);
    Route::get('/canceladas', [MentoriaController::class, 'canceladas']);
    Route::get('/futuras', [MentoriaController::class, 'futuras']);
    Route::get('/passadas', [MentoriaController::class, 'passadas']);
    Route::get('/aluno/{alunoId}', [MentoriaController::class, 'porAluno']);
    Route::get('/veterano/{veteranoId}', [MentoriaController::class, 'porVeterano']);
    Route::get('/disciplina/{disciplinaId}', [MentoriaController::class, 'porDisciplina']);
    Route::get('/{id}', [MentoriaController::class, 'show']);
    Route::put('/{id}', [MentoriaController::class, 'update']);
    Route::delete('/{id}', [MentoriaController::class, 'destroy']);
    Route::patch('/{id}/concluir', [MentoriaController::class, 'concluir']);
    Route::patch('/{id}/cancelar', [MentoriaController::class, 'cancelar']);
});

// Rotas dos Eventos
Route::prefix('eventos')->group(function () {
    Route::get('/', [EventoController::class, 'index']);
    Route::post('/', [EventoController::class, 'store']);
    Route::get('/buscar', [EventoController::class, 'buscar']);
    Route::get('/estatisticas', [EventoController::class, 'estatisticas']);
    Route::get('/tipo/{tipo}', [EventoController::class, 'porTipo']);
    Route::get('/hoje', [EventoController::class, 'hoje']);
    Route::get('/esta-semana', [EventoController::class, 'estaSemana']);
    Route::get('/este-mes', [EventoController::class, 'esteMes']);
    Route::get('/futuros', [EventoController::class, 'futuros']);
    Route::get('/passados', [EventoController::class, 'passados']);
    Route::get('/em-andamento', [EventoController::class, 'emAndamento']);
    Route::get('/periodo', [EventoController::class, 'porPeriodo']);
    Route::get('/{id}', [EventoController::class, 'show']);
    Route::put('/{id}', [EventoController::class, 'update']);
    Route::delete('/{id}', [EventoController::class, 'destroy']);
});

// Rotas das Postagens
Route::prefix('postagens')->group(function () {
    Route::get('/', [PostagemController::class, 'index']);
    Route::post('/', [PostagemController::class, 'store']);
    Route::get('/buscar', [PostagemController::class, 'buscar']);
    Route::get('/estatisticas', [PostagemController::class, 'estatisticas']);
    Route::get('/recentes', [PostagemController::class, 'recentes']);
    Route::get('/populares', [PostagemController::class, 'populares']);
    Route::get('/aluno/{alunoId}', [PostagemController::class, 'porAluno']);
    Route::get('/{id}', [PostagemController::class, 'show']);
    Route::put('/{id}', [PostagemController::class, 'update']);
    Route::delete('/{id}', [PostagemController::class, 'destroy']);
    Route::get('/{id}/comentarios', [PostagemController::class, 'comentarios']);
});

// Rotas dos Comentários
Route::prefix('comentarios')->group(function () {
    Route::get('/', [ComentarioController::class, 'index']);
    Route::post('/', [ComentarioController::class, 'store']);
    Route::get('/buscar', [ComentarioController::class, 'buscar']);
    Route::get('/estatisticas', [ComentarioController::class, 'estatisticas']);
    Route::get('/recentes', [ComentarioController::class, 'recentes']);
    Route::get('/postagem/{postagemId}', [ComentarioController::class, 'porPostagem']);
    Route::get('/aluno/{alunoId}', [ComentarioController::class, 'porAluno']);
    Route::get('/{id}', [ComentarioController::class, 'show']);
    Route::put('/{id}', [ComentarioController::class, 'update']);
    Route::delete('/{id}', [ComentarioController::class, 'destroy']);
});

// Rotas da Curadoria de Conteúdo
Route::prefix('curadoria-conteudo')->group(function () {
    Route::get('/', [CuradoriaConteudoController::class, 'index']);
    Route::post('/', [CuradoriaConteudoController::class, 'store']);
    Route::get('/buscar', [CuradoriaConteudoController::class, 'buscar']);
    Route::get('/estatisticas', [CuradoriaConteudoController::class, 'estatisticas']);
    Route::get('/pendentes', [CuradoriaConteudoController::class, 'pendentes']);
    Route::get('/aprovadas', [CuradoriaConteudoController::class, 'aprovadas']);
    Route::get('/rejeitadas', [CuradoriaConteudoController::class, 'rejeitadas']);
    Route::get('/materiais', [CuradoriaConteudoController::class, 'materiais']);
    Route::get('/enunciados', [CuradoriaConteudoController::class, 'enunciados']);
    Route::get('/curador/{curadorId}', [CuradoriaConteudoController::class, 'porCurador']);
    Route::get('/{id}', [CuradoriaConteudoController::class, 'show']);
    Route::put('/{id}', [CuradoriaConteudoController::class, 'update']);
    Route::delete('/{id}', [CuradoriaConteudoController::class, 'destroy']);
    Route::patch('/{id}/aprovar', [CuradoriaConteudoController::class, 'aprovar']);
    Route::patch('/{id}/rejeitar', [CuradoriaConteudoController::class, 'rejeitar']);
});
