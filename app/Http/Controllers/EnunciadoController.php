<?php

namespace App\Http\Controllers;

use App\Models\Enunciado;
use App\Models\Disciplina;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EnunciadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Enunciado::with(["disciplina.curso", "aluno"]);

        // Filtros
        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        if ($request->has("tipo")) {
            $query->porTipo($request->tipo);
        }

        if ($request->has("ano")) {
            $query->porAno($request->ano);
        }

        if ($request->has("aluno_id")) {
            $query->doAluno($request->aluno_id);
        }

        if ($request->has("curso_id")) {
            $query->whereHas("disciplina", function ($q) use ($request) {
                $q->where("id_curso", $request->curso_id);
            });
        }

        if ($request->has("busca")) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where("titulo", "like", "%{$busca}%")->orWhere(
                    "ano",
                    "like",
                    "%{$busca}%",
                );
            });
        }

        // Incluir status de curadoria se solicitado
        if ($request->has("incluir_curadoria") && $request->incluir_curadoria) {
            $query->with("curadorias");
        }

        // Ordenação
        $orderBy = $request->get("order_by", "created_at");
        $orderDirection = $request->get("order_direction", "desc");
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get("per_page", 15);
        $enunciados = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $enunciados,
            "message" => "Enunciados listados com sucesso",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "titulo" => "required|string|max:255",
            "tipo" => "required|in:prova,teste,exame",
            "ano" => "required|string|max:10",
            "arquivo" =>
                "required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240", // 10MB
            "id_disciplina" => "required|exists:disciplinas,id_disciplina",
            "id_aluno" => "required|exists:alunos,id_aluno",
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

            // Upload do arquivo
            if ($request->hasFile("arquivo")) {
                $arquivo = $request->file("arquivo");
                $nomeArquivo = time() . "_" . $arquivo->getClientOriginalName();
                $caminhoArquivo = $arquivo->storeAs(
                    "enunciados",
                    $nomeArquivo,
                    "public",
                );
                $data["arquivo_url"] = Storage::url($caminhoArquivo);
            }

            $enunciado = Enunciado::create($data);
            $enunciado->load(["disciplina.curso", "aluno"]);

            return response()->json(
                [
                    "success" => true,
                    "data" => $enunciado,
                    "message" => "Enunciado criado com sucesso",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao criar enunciado: " . $e->getMessage(),
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
            $enunciado = Enunciado::with([
                "disciplina.curso",
                "aluno",
                "curadorias.alunoCurador",
            ])->findOrFail($id);

            return response()->json([
                "success" => true,
                "data" => $enunciado,
                "message" => "Enunciado encontrado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Enunciado não encontrado",
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
            $enunciado = Enunciado::findOrFail($id);

            $validator = Validator::make($request->all(), [
                "titulo" => "sometimes|string|max:255",
                "tipo" => "sometimes|in:prova,teste,exame",
                "ano" => "sometimes|string|max:10",
                "arquivo" =>
                    "sometimes|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240",
                "id_disciplina" => "sometimes|exists:disciplinas,id_disciplina",
                "id_aluno" => "sometimes|exists:alunos,id_aluno",
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

            // Upload do novo arquivo se fornecido
            if ($request->hasFile("arquivo")) {
                // Remover arquivo antigo
                if ($enunciado->arquivo_url) {
                    $caminhoAntigo = str_replace(
                        "/storage/",
                        "",
                        $enunciado->arquivo_url,
                    );
                    Storage::disk("public")->delete($caminhoAntigo);
                }

                $arquivo = $request->file("arquivo");
                $nomeArquivo = time() . "_" . $arquivo->getClientOriginalName();
                $caminhoArquivo = $arquivo->storeAs(
                    "enunciados",
                    $nomeArquivo,
                    "public",
                );
                $data["arquivo_url"] = Storage::url($caminhoArquivo);
            }

            $enunciado->update($data);
            $enunciado->load(["disciplina.curso", "aluno"]);

            return response()->json([
                "success" => true,
                "data" => $enunciado,
                "message" => "Enunciado atualizado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao atualizar enunciado: " . $e->getMessage(),
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
            $enunciado = Enunciado::findOrFail($id);

            // Remover arquivo do storage
            if ($enunciado->arquivo_url) {
                $caminhoArquivo = str_replace(
                    "/storage/",
                    "",
                    $enunciado->arquivo_url,
                );
                Storage::disk("public")->delete($caminhoArquivo);
            }

            $enunciado->delete();

            return response()->json([
                "success" => true,
                "message" => "Enunciado excluído com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao excluir enunciado: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get enunciados por tipo
     */
    public function porTipo(string $tipo, Request $request): JsonResponse
    {
        $validator = Validator::make(
            ["tipo" => $tipo],
            [
                "tipo" => "required|in:prova,teste,exame",
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Tipo inválido",
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $query = Enunciado::porTipo($tipo)->with(["disciplina.curso", "aluno"]);

        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        if ($request->has("ano")) {
            $query->porAno($request->ano);
        }

        $enunciados = $query->orderBy("created_at", "desc")->get();

        return response()->json([
            "success" => true,
            "data" => $enunciados,
            "message" => "Enunciados do tipo '{$tipo}' listados com sucesso",
        ]);
    }

    /**
     * Get enunciados por disciplina
     */
    public function porDisciplina(
        string $disciplinaId,
        Request $request,
    ): JsonResponse {
        try {
            $disciplina = Disciplina::findOrFail($disciplinaId);
            $query = $disciplina->enunciados()->with(["aluno"]);

            if ($request->has("tipo")) {
                $query->porTipo($request->tipo);
            }

            if ($request->has("ano")) {
                $query->porAno($request->ano);
            }

            if ($request->has("aluno_id")) {
                $query->doAluno($request->aluno_id);
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
     * Get enunciados por aluno
     */
    public function porAluno(string $alunoId, Request $request): JsonResponse
    {
        try {
            $aluno = Aluno::findOrFail($alunoId);
            $query = $aluno->enunciados()->with(["disciplina.curso"]);

            if ($request->has("tipo")) {
                $query->porTipo($request->tipo);
            }

            if ($request->has("disciplina_id")) {
                $query->where("id_disciplina", $request->disciplina_id);
            }

            if ($request->has("ano")) {
                $query->porAno($request->ano);
            }

            $enunciados = $query->orderBy("created_at", "desc")->get();

            return response()->json([
                "success" => true,
                "data" => $enunciados,
                "message" => "Enunciados do aluno listados com sucesso",
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
     * Buscar enunciados por critérios específicos
     */
    public function buscar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "termo" => "required|string|min:2",
            "tipo" => "sometimes|in:prova,teste,exame",
            "disciplina_id" => "sometimes|exists:disciplinas,id_disciplina",
            "curso_id" => "sometimes|exists:cursos,id_curso",
            "ano" => "sometimes|string",
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
        $query = Enunciado::where(function ($q) use ($termo) {
            $q->where("titulo", "like", "%{$termo}%")->orWhere(
                "ano",
                "like",
                "%{$termo}%",
            );
        });

        if ($request->has("tipo")) {
            $query->porTipo($request->tipo);
        }

        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        if ($request->has("curso_id")) {
            $query->whereHas("disciplina", function ($q) use ($request) {
                $q->where("id_curso", $request->curso_id);
            });
        }

        if ($request->has("ano")) {
            $query->porAno($request->ano);
        }

        $enunciados = $query->with(["disciplina.curso", "aluno"])->get();

        return response()->json([
            "success" => true,
            "data" => $enunciados,
            "message" => "Busca realizada com sucesso",
        ]);
    }

    /**
     * Download do arquivo do enunciado
     */
    public function download(string $id): JsonResponse
    {
        try {
            $enunciado = Enunciado::findOrFail($id);

            if (!$enunciado->arquivo_url) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Arquivo não encontrado",
                    ],
                    404,
                );
            }

            $caminhoArquivo = str_replace(
                "/storage/",
                "",
                $enunciado->arquivo_url,
            );

            if (!Storage::disk("public")->exists($caminhoArquivo)) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Arquivo não existe no servidor",
                    ],
                    404,
                );
            }

            return response()->json([
                "success" => true,
                "data" => [
                    "download_url" => $enunciado->arquivo_url,
                    "nome_arquivo" => $enunciado->nome_arquivo,
                    "extensao" => $enunciado->extensao_arquivo,
                ],
                "message" => "Link de download gerado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao gerar download: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get estatísticas dos enunciados
     */
    public function estatisticas(Request $request): JsonResponse
    {
        try {
            $query = Enunciado::query();

            // Filtros opcionais para as estatísticas
            if ($request->has("disciplina_id")) {
                $query->where("id_disciplina", $request->disciplina_id);
            }

            if ($request->has("curso_id")) {
                $query->whereHas("disciplina", function ($q) use ($request) {
                    $q->where("id_curso", $request->curso_id);
                });
            }

            if ($request->has("aluno_id")) {
                $query->where("id_aluno", $request->aluno_id);
            }

            $stats = [
                "total_enunciados" => $query->count(),
                "por_tipo" => [
                    "provas" => (clone $query)->provas()->count(),
                    "testes" => (clone $query)->testes()->count(),
                    "exames" => (clone $query)->exames()->count(),
                ],
                "por_ano" => $query
                    ->selectRaw("ano, COUNT(*) as total")
                    ->groupBy("ano")
                    ->orderBy("ano", "desc")
                    ->pluck("total", "ano"),
                "uploads_recentes" => $query
                    ->where("created_at", ">=", now()->subDays(30))
                    ->count(),
                "disciplinas_com_enunciados" => $query
                    ->distinct("id_disciplina")
                    ->count("id_disciplina"),
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
     * Get enunciados aprovados pela curadoria
     */
    public function aprovados(Request $request): JsonResponse
    {
        $query = Enunciado::whereHas("curadorias", function ($q) {
            $q->where("status", "aprovado");
        })->with(["disciplina.curso", "aluno"]);

        // Filtros
        if ($request->has("tipo")) {
            $query->porTipo($request->tipo);
        }

        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        if ($request->has("ano")) {
            $query->porAno($request->ano);
        }

        $enunciados = $query->orderBy("created_at", "desc")->get();

        return response()->json([
            "success" => true,
            "data" => $enunciados,
            "message" => "Enunciados aprovados listados com sucesso",
        ]);
    }

    /**
     * Get enunciados pendentes de curadoria
     */
    public function pendentes(Request $request): JsonResponse
    {
        $query = Enunciado::whereHas("curadorias", function ($q) {
            $q->where("status", "pendente");
        })
            ->orWhereDoesntHave("curadorias")
            ->with(["disciplina.curso", "aluno"]);

        // Filtros
        if ($request->has("tipo")) {
            $query->porTipo($request->tipo);
        }

        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        $enunciados = $query->orderBy("created_at", "desc")->get();

        return response()->json([
            "success" => true,
            "data" => $enunciados,
            "message" => "Enunciados pendentes listados com sucesso",
        ]);
    }
}
