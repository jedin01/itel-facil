<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Curso::with(["alunos", "disciplinas"]);

        // Filtros
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

        if ($request->has("com_alunos") && $request->com_alunos) {
            $query->comAlunos();
        }

        if ($request->has("com_disciplinas") && $request->com_disciplinas) {
            $query->comDisciplinas();
        }

        // Ordenação
        $orderBy = $request->get("order_by", "nome");
        $orderDirection = $request->get("order_direction", "asc");
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get("per_page", 15);
        $cursos = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $cursos,
            "message" => "Cursos listados com sucesso",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "nome" => "required|string|max:255|unique:cursos,nome",
            "descricao" => "nullable|string",
            "alunos" => "array",
            "alunos.*" => "exists:alunos,id_aluno",
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
            $curso = Curso::create($data);

            // Associar alunos se fornecidos
            if ($request->has("alunos")) {
                $curso->alunos()->attach($request->alunos);
            }

            $curso->load(["alunos", "disciplinas"]);

            return response()->json(
                [
                    "success" => true,
                    "data" => $curso,
                    "message" => "Curso criado com sucesso",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao criar curso: " . $e->getMessage(),
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
            $curso = Curso::with([
                "alunos" => function ($query) {
                    $query->orderBy("nome_completo");
                },
                "disciplinas" => function ($query) {
                    $query
                        ->withCount([
                            "enunciados",
                            "materiaisDidaticos",
                            "mentorias",
                        ])
                        ->orderBy("nome");
                },
            ])->findOrFail($id);

            return response()->json([
                "success" => true,
                "data" => $curso,
                "message" => "Curso encontrado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Curso não encontrado",
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
            $curso = Curso::findOrFail($id);

            $validator = Validator::make($request->all(), [
                "nome" =>
                    "sometimes|string|max:255|unique:cursos,nome," .
                    $id .
                    ",id_curso",
                "descricao" => "nullable|string",
                "alunos" => "array",
                "alunos.*" => "exists:alunos,id_aluno",
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
            $curso->update($data);

            // Sincronizar alunos se fornecidos
            if ($request->has("alunos")) {
                $curso->alunos()->sync($request->alunos);
            }

            $curso->load(["alunos", "disciplinas"]);

            return response()->json([
                "success" => true,
                "data" => $curso,
                "message" => "Curso atualizado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao atualizar curso: " . $e->getMessage(),
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
            $curso = Curso::findOrFail($id);

            // Verificar se há disciplinas associadas
            if ($curso->disciplinas()->count() > 0) {
                return response()->json(
                    [
                        "success" => false,
                        "message" =>
                            "Não é possível excluir um curso que possui disciplinas associadas",
                    ],
                    422,
                );
            }

            $curso->delete();

            return response()->json([
                "success" => true,
                "message" => "Curso excluído com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao excluir curso: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get estatísticas do curso
     */
    public function estatisticas(string $id): JsonResponse
    {
        try {
            $curso = Curso::findOrFail($id);

            $stats = [
                "total_alunos" => $curso->alunos()->count(),
                "total_disciplinas" => $curso->disciplinas()->count(),
                "alunos_veteranos" => $curso
                    ->alunos()
                    ->where("tipo", "veterano")
                    ->count(),
                "alunos_regulares" => $curso
                    ->alunos()
                    ->where("tipo", "aluno")
                    ->count(),
                "total_enunciados" => $curso
                    ->disciplinas()
                    ->withCount("enunciados")
                    ->get()
                    ->sum("enunciados_count"),
                "total_materiais" => $curso
                    ->disciplinas()
                    ->withCount("materiaisDidaticos")
                    ->get()
                    ->sum("materiais_didaticos_count"),
                "total_mentorias" => $curso
                    ->disciplinas()
                    ->withCount("mentorias")
                    ->get()
                    ->sum("mentorias_count"),
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
     * Get alunos do curso
     */
    public function alunos(string $id, Request $request): JsonResponse
    {
        try {
            $curso = Curso::findOrFail($id);

            $query = $curso->alunos();

            // Filtros
            if ($request->has("tipo")) {
                $query->where("tipo", $request->tipo);
            }

            if ($request->has("ano_escolar")) {
                $query->where("ano_escolar", $request->ano_escolar);
            }

            if ($request->has("busca")) {
                $busca = $request->busca;
                $query->where(function ($q) use ($busca) {
                    $q->where("nome_completo", "like", "%{$busca}%")->orWhere(
                        "email",
                        "like",
                        "%{$busca}%",
                    );
                });
            }

            $alunos = $query->orderBy("nome_completo")->get();

            return response()->json([
                "success" => true,
                "data" => $alunos,
                "message" => "Alunos do curso listados com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao listar alunos: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get disciplinas do curso
     */
    public function disciplinas(string $id, Request $request): JsonResponse
    {
        try {
            $curso = Curso::findOrFail($id);

            $query = $curso
                ->disciplinas()
                ->withCount(["enunciados", "materiaisDidaticos", "mentorias"]);

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

            $disciplinas = $query->orderBy("nome")->get();

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

    /**
     * Buscar cursos por critérios específicos
     */
    public function buscar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "termo" => "required|string|min:2",
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
        $query = Curso::where(function ($q) use ($termo) {
            $q->where("nome", "like", "%{$termo}%")->orWhere(
                "descricao",
                "like",
                "%{$termo}%",
            );
        });

        $cursos = $query->with(["alunos", "disciplinas"])->get();

        return response()->json([
            "success" => true,
            "data" => $cursos,
            "message" => "Busca realizada com sucesso",
        ]);
    }

    /**
     * Associar aluno ao curso
     */
    public function associarAluno(string $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "aluno_id" => "required|exists:alunos,id_aluno",
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
            $curso = Curso::findOrFail($id);
            $alunoId = $request->aluno_id;

            // Verificar se já está associado
            if ($curso->alunos()->where("id_aluno", $alunoId)->exists()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Aluno já está associado a este curso",
                    ],
                    422,
                );
            }

            $curso->alunos()->attach($alunoId);

            return response()->json([
                "success" => true,
                "message" => "Aluno associado ao curso com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao associar aluno: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Desassociar aluno do curso
     */
    public function desassociarAluno(string $id, string $alunoId): JsonResponse
    {
        try {
            $curso = Curso::findOrFail($id);

            if (!$curso->alunos()->where("id_aluno", $alunoId)->exists()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Aluno não está associado a este curso",
                    ],
                    422,
                );
            }

            $curso->alunos()->detach($alunoId);

            return response()->json([
                "success" => true,
                "message" => "Aluno desassociado do curso com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao desassociar aluno: " . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
