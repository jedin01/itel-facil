<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Disciplina::with(["curso"]);

        // Filtros
        if ($request->has("curso_id")) {
            $query->where("id_curso", $request->curso_id);
        }

        if ($request->has("busca")) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where("nome", "like", "%{$busca}%")->orWhere(
                    "descricao",
                    "like",
                    "%{$busca}%",
                );
            });
        }

        if ($request->has("com_enunciados") && $request->com_enunciados) {
            $query->comEnunciados();
        }

        if ($request->has("com_materiais") && $request->com_materiais) {
            $query->comMateriaisDidaticos();
        }

        // Contadores opcionais
        if (
            $request->has("incluir_contadores") &&
            $request->incluir_contadores
        ) {
            $query->withCount([
                "enunciados",
                "materiaisDidaticos",
                "mentorias",
            ]);
        }

        // Ordenação
        $orderBy = $request->get("order_by", "nome");
        $orderDirection = $request->get("order_direction", "asc");
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get("per_page", 15);
        $disciplinas = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $disciplinas,
            "message" => "Disciplinas listadas com sucesso",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "nome" => "required|string|max:255",
            "descricao" => "nullable|string",
            "id_curso" => "required|exists:cursos,id_curso",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Dados inválidos",
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        try {
            $data = $validator->validated();
            $disciplina = Disciplina::create($data);
            $disciplina->load("curso");

            return response()->json(
                [
                    "success" => true,
                    "data" => $disciplina,
                    "message" => "Disciplina criada com sucesso",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao criar disciplina: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $disciplina = Disciplina::with([
                "curso",
                "enunciados" => function ($query) {
                    $query->with("aluno")->orderBy("created_at", "desc");
                },
                "materiaisDidaticos" => function ($query) {
                    $query->with("aluno")->orderBy("created_at", "desc");
                },
                "mentorias" => function ($query) {
                    $query
                        ->with(["alunoSolicitante", "veterano"])
                        ->orderBy("data_hora", "desc");
                },
            ])->findOrFail($id);

            return response()->json([
                "success" => true,
                "data" => $disciplina,
                "message" => "Disciplina encontrada com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Disciplina não encontrada",
                ],
                404,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $disciplina = Disciplina::findOrFail($id);

            $validator = Validator::make($request->all(), [
                "nome" => "sometimes|string|max:255",
                "descricao" => "nullable|string",
                "id_curso" => "sometimes|exists:cursos,id_curso",
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Dados inválidos",
                        "errors" => $validator->errors(),
                    ],
                    422,
                );
            }

            $data = $validator->validated();
            $disciplina->update($data);
            $disciplina->load("curso");

            return response()->json([
                "success" => true,
                "data" => $disciplina,
                "message" => "Disciplina atualizada com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao atualizar disciplina: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $disciplina = Disciplina::findOrFail($id);

            // Verificar se há enunciados associados
            if ($disciplina->enunciados()->count() > 0) {
                return response()->json(
                    [
                        "success" => false,
                        "message" =>
                            "Não é possível excluir uma disciplina que possui enunciados associados",
                    ],
                    422,
                );
            }

            // Verificar se há materiais didáticos associados
            if ($disciplina->materiaisDidaticos()->count() > 0) {
                return response()->json(
                    [
                        "success" => false,
                        "message" =>
                            "Não é possível excluir uma disciplina que possui materiais didáticos associados",
                    ],
                    422,
                );
            }

            // Verificar se há mentorias associadas
            if ($disciplina->mentorias()->count() > 0) {
                return response()->json(
                    [
                        "success" => false,
                        "message" =>
                            "Não é possível excluir uma disciplina que possui mentorias associadas",
                    ],
                    422,
                );
            }

            $disciplina->delete();

            return response()->json([
                "success" => true,
                "message" => "Disciplina excluída com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao excluir disciplina: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get estatísticas da disciplina
     */
    public function estatisticas(string $id): JsonResponse
    {
        try {
            $disciplina = Disciplina::findOrFail($id);

            $stats = [
                "total_enunciados" => $disciplina->enunciados()->count(),
                "total_materiais" => $disciplina->materiaisDidaticos()->count(),
                "total_mentorias" => $disciplina->mentorias()->count(),
                "enunciados_por_tipo" => [
                    "prova" => $disciplina->enunciados()->provas()->count(),
                    "teste" => $disciplina->enunciados()->testes()->count(),
                    "exame" => $disciplina->enunciados()->exames()->count(),
                ],
                "materiais_por_tipo" => [
                    "slide" => $disciplina
                        ->materiaisDidaticos()
                        ->slides()
                        ->count(),
                    "livro" => $disciplina
                        ->materiaisDidaticos()
                        ->livros()
                        ->count(),
                    "resumo" => $disciplina
                        ->materiaisDidaticos()
                        ->resumos()
                        ->count(),
                    "tutorial" => $disciplina
                        ->materiaisDidaticos()
                        ->tutoriais()
                        ->count(),
                ],
                "mentorias_por_status" => [
                    "agendadas" => $disciplina
                        ->mentorias()
                        ->agendadas()
                        ->count(),
                    "concluidas" => $disciplina
                        ->mentorias()
                        ->concluidas()
                        ->count(),
                    "canceladas" => $disciplina
                        ->mentorias()
                        ->canceladas()
                        ->count(),
                ],
            ];

            return response()->json([
                "success" => true,
                "data" => $stats,
                "message" => "Estatísticas obtidas com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao obter estatísticas: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get enunciados da disciplina
     */
    public function enunciados(string $id, Request $request): JsonResponse
    {
        try {
            $disciplina = Disciplina::findOrFail($id);

            $query = $disciplina->enunciados()->with("aluno");

            // Filtros
            if ($request->has("tipo")) {
                $query->porTipo($request->tipo);
            }

            if ($request->has("ano")) {
                $query->porAno($request->ano);
            }

            if ($request->has("aluno_id")) {
                $query->doAluno($request->aluno_id);
            }

            if ($request->has("busca")) {
                $busca = $request->busca;
                $query->where("titulo", "like", "%{$busca}%");
            }

            $enunciados = $query->orderBy("created_at", "desc")->get();

            return response()->json([
                "success" => true,
                "data" => $enunciados,
                "message" => "Enunciados da disciplina listados com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao listar enunciados: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get materiais didáticos da disciplina
     */
    public function materiaisDidaticos(
        string $id,
        Request $request,
    ): JsonResponse {
        try {
            $disciplina = Disciplina::findOrFail($id);

            $query = $disciplina->materiaisDidaticos()->with("aluno");

            // Filtros
            if ($request->has("tipo")) {
                $query->porTipo($request->tipo);
            }

            if ($request->has("aluno_id")) {
                $query->doAluno($request->aluno_id);
            }

            if ($request->has("busca")) {
                $busca = $request->busca;
                $query->where("titulo", "like", "%{$busca}%");
            }

            $materiais = $query->orderBy("created_at", "desc")->get();

            return response()->json([
                "success" => true,
                "data" => $materiais,
                "message" =>
                    "Materiais didáticos da disciplina listados com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao listar materiais: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get mentorias da disciplina
     */
    public function mentorias(string $id, Request $request): JsonResponse
    {
        try {
            $disciplina = Disciplina::findOrFail($id);

            $query = $disciplina
                ->mentorias()
                ->with(["alunoSolicitante", "veterano"]);

            // Filtros
            if ($request->has("status")) {
                $query->porStatus($request->status);
            }

            if ($request->has("aluno_id")) {
                $query->where(function ($q) use ($request) {
                    $q->where(
                        "id_aluno_solicitante",
                        $request->aluno_id,
                    )->orWhere("id_veterano", $request->aluno_id);
                });
            }

            if ($request->has("futuras") && $request->futuras) {
                $query->futuras();
            }

            if ($request->has("passadas") && $request->passadas) {
                $query->passadas();
            }

            $mentorias = $query->orderBy("data_hora", "desc")->get();

            return response()->json([
                "success" => true,
                "data" => $mentorias,
                "message" => "Mentorias da disciplina listadas com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao listar mentorias: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Buscar disciplinas por critérios específicos
     */
    public function buscar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "termo" => "required|string|min:2",
            "curso_id" => "sometimes|exists:cursos,id_curso",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Parâmetros de busca inválidos",
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $termo = $request->termo;
        $query = Disciplina::where(function ($q) use ($termo) {
            $q->where("nome", "like", "%{$termo}%")->orWhere(
                "descricao",
                "like",
                "%{$termo}%",
            );
        });

        if ($request->has("curso_id")) {
            $query->where("id_curso", $request->curso_id);
        }

        $disciplinas = $query->with("curso")->get();

        return response()->json([
            "success" => true,
            "data" => $disciplinas,
            "message" => "Busca realizada com sucesso",
        ]);
    }

    /**
     * Get disciplinas por curso
     */
    public function porCurso(string $cursoId): JsonResponse
    {
        try {
            $disciplinas = Disciplina::doCurso($cursoId)
                ->withCount(["enunciados", "materiaisDidaticos", "mentorias"])
                ->orderBy("nome")
                ->get();

            return response()->json([
                "success" => true,
                "data" => $disciplinas,
                "message" => "Disciplinas do curso listadas com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao listar disciplinas: " . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
