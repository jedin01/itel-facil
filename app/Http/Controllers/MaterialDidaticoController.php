<?php

namespace App\Http\Controllers;

use App\Models\MaterialDidatico;
use App\Models\Disciplina;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MaterialDidaticoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = MaterialDidatico::with(["disciplina.curso", "aluno"]);

        // Filtros
        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        if ($request->has("tipo")) {
            $query->porTipo($request->tipo);
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
                $q->where("titulo", "like", "%{$busca}%");
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
        $materiais = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $materiais,
            "message" => "Materiais didáticos listados com sucesso",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "titulo" => "required|string|max:255",
            "tipo" => "required|in:slide,livro,resumo,tutorial",
            "arquivo" =>
                "required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar|max:20480", // 20MB
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
                    "materiais-didaticos",
                    $nomeArquivo,
                    "public",
                );
                $data["arquivo_url"] = Storage::url($caminhoArquivo);
            }

            $material = MaterialDidatico::create($data);
            $material->load(["disciplina.curso", "aluno"]);

            return response()->json(
                [
                    "success" => true,
                    "data" => $material,
                    "message" => "Material didático criado com sucesso",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao criar material didático: " . $e->getMessage(),
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
            $material = MaterialDidatico::with([
                "disciplina.curso",
                "aluno",
                "curadorias.alunoCurador",
            ])->findOrFail($id);

            return response()->json([
                "success" => true,
                "data" => $material,
                "message" => "Material didático encontrado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Material didático não encontrado",
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
            $material = MaterialDidatico::findOrFail($id);

            $validator = Validator::make($request->all(), [
                "titulo" => "sometimes|string|max:255",
                "tipo" => "sometimes|in:slide,livro,resumo,tutorial",
                "arquivo" =>
                    "sometimes|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar|max:20480",
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
                if ($material->arquivo_url) {
                    $caminhoAntigo = str_replace(
                        "/storage/",
                        "",
                        $material->arquivo_url,
                    );
                    Storage::disk("public")->delete($caminhoAntigo);
                }

                $arquivo = $request->file("arquivo");
                $nomeArquivo = time() . "_" . $arquivo->getClientOriginalName();
                $caminhoArquivo = $arquivo->storeAs(
                    "materiais-didaticos",
                    $nomeArquivo,
                    "public",
                );
                $data["arquivo_url"] = Storage::url($caminhoArquivo);
            }

            $material->update($data);
            $material->load(["disciplina.curso", "aluno"]);

            return response()->json([
                "success" => true,
                "data" => $material,
                "message" => "Material didático atualizado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao atualizar material didático: " .
                        $e->getMessage(),
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
            $material = MaterialDidatico::findOrFail($id);

            // Remover arquivo do storage
            if ($material->arquivo_url) {
                $caminhoArquivo = str_replace(
                    "/storage/",
                    "",
                    $material->arquivo_url,
                );
                Storage::disk("public")->delete($caminhoArquivo);
            }

            $material->delete();

            return response()->json([
                "success" => true,
                "message" => "Material didático excluído com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Erro ao excluir material didático: " .
                        $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get materiais por tipo
     */
    public function porTipo(string $tipo, Request $request): JsonResponse
    {
        $validator = Validator::make(
            ["tipo" => $tipo],
            [
                "tipo" => "required|in:slide,livro,resumo,tutorial",
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

        $query = MaterialDidatico::porTipo($tipo)->with([
            "disciplina.curso",
            "aluno",
        ]);

        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        $materiais = $query->orderBy("created_at", "desc")->get();

        return response()->json([
            "success" => true,
            "data" => $materiais,
            "message" => "Materiais do tipo '{$tipo}' listados com sucesso",
        ]);
    }

    /**
     * Get materiais por disciplina
     */
    public function porDisciplina(
        string $disciplinaId,
        Request $request,
    ): JsonResponse {
        try {
            $disciplina = Disciplina::findOrFail($disciplinaId);
            $query = $disciplina->materiaisDidaticos()->with(["aluno"]);

            if ($request->has("tipo")) {
                $query->porTipo($request->tipo);
            }

            if ($request->has("aluno_id")) {
                $query->doAluno($request->aluno_id);
            }

            $materiais = $query->orderBy("created_at", "desc")->get();

            return response()->json([
                "success" => true,
                "data" => $materiais,
                "message" => "Materiais da disciplina listados com sucesso",
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
     * Get materiais por aluno
     */
    public function porAluno(string $alunoId, Request $request): JsonResponse
    {
        try {
            $aluno = Aluno::findOrFail($alunoId);
            $query = $aluno->materiaisDidaticos()->with(["disciplina.curso"]);

            if ($request->has("tipo")) {
                $query->porTipo($request->tipo);
            }

            if ($request->has("disciplina_id")) {
                $query->where("id_disciplina", $request->disciplina_id);
            }

            $materiais = $query->orderBy("created_at", "desc")->get();

            return response()->json([
                "success" => true,
                "data" => $materiais,
                "message" => "Materiais do aluno listados com sucesso",
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
     * Buscar materiais por critérios específicos
     */
    public function buscar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "termo" => "required|string|min:2",
            "tipo" => "sometimes|in:slide,livro,resumo,tutorial",
            "disciplina_id" => "sometimes|exists:disciplinas,id_disciplina",
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
        $query = MaterialDidatico::where("titulo", "like", "%{$termo}%");

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

        $materiais = $query->with(["disciplina.curso", "aluno"])->get();

        return response()->json([
            "success" => true,
            "data" => $materiais,
            "message" => "Busca realizada com sucesso",
        ]);
    }

    /**
     * Download do arquivo do material
     */
    public function download(string $id): JsonResponse
    {
        try {
            $material = MaterialDidatico::findOrFail($id);

            if (!$material->arquivo_url) {
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
                $material->arquivo_url,
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
                    "download_url" => $material->arquivo_url,
                    "nome_arquivo" => $material->nome_arquivo,
                    "extensao" => $material->extensao_arquivo,
                    "tipo_arquivo" => [
                        "is_imagem" => $material->is_imagem,
                        "is_documento" => $material->is_documento,
                        "is_apresentacao" => $material->is_apresentacao,
                    ],
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
     * Get estatísticas dos materiais didáticos
     */
    public function estatisticas(Request $request): JsonResponse
    {
        try {
            $query = MaterialDidatico::query();

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
                "total_materiais" => $query->count(),
                "por_tipo" => [
                    "slides" => (clone $query)->slides()->count(),
                    "livros" => (clone $query)->livros()->count(),
                    "resumos" => (clone $query)->resumos()->count(),
                    "tutoriais" => (clone $query)->tutoriais()->count(),
                ],
                "uploads_recentes" => $query
                    ->where("created_at", ">=", now()->subDays(30))
                    ->count(),
                "disciplinas_com_materiais" => $query
                    ->distinct("id_disciplina")
                    ->count("id_disciplina"),
                "contribuintes_ativos" => $query
                    ->distinct("id_aluno")
                    ->count("id_aluno"),
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
     * Get materiais aprovados pela curadoria
     */
    public function aprovados(Request $request): JsonResponse
    {
        $query = MaterialDidatico::whereHas("curadorias", function ($q) {
            $q->where("status", "aprovado");
        })->with(["disciplina.curso", "aluno"]);

        // Filtros
        if ($request->has("tipo")) {
            $query->porTipo($request->tipo);
        }

        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        $materiais = $query->orderBy("created_at", "desc")->get();

        return response()->json([
            "success" => true,
            "data" => $materiais,
            "message" => "Materiais aprovados listados com sucesso",
        ]);
    }

    /**
     * Get materiais pendentes de curadoria
     */
    public function pendentes(Request $request): JsonResponse
    {
        $query = MaterialDidatico::whereHas("curadorias", function ($q) {
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

        $materiais = $query->orderBy("created_at", "desc")->get();

        return response()->json([
            "success" => true,
            "data" => $materiais,
            "message" => "Materiais pendentes listados com sucesso",
        ]);
    }

    /**
     * Get materiais por categoria de arquivo
     */
    public function porCategoria(
        string $categoria,
        Request $request,
    ): JsonResponse {
        $validator = Validator::make(
            ["categoria" => $categoria],
            [
                "categoria" => "required|in:imagem,documento,apresentacao",
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Categoria inválida",
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $query = MaterialDidatico::with(["disciplina.curso", "aluno"]);

        // Filtrar por categoria de arquivo baseado na extensão
        switch ($categoria) {
            case "imagem":
                $query->whereIn("arquivo_url", function ($subQuery) {
                    $subQuery
                        ->select("arquivo_url")
                        ->from("materiais_didaticos")
                        ->where(function ($q) {
                            $extensoes = [
                                "jpg",
                                "jpeg",
                                "png",
                                "gif",
                                "bmp",
                                "svg",
                                "webp",
                            ];
                            foreach ($extensoes as $ext) {
                                $q->orWhere("arquivo_url", "like", "%.{$ext}");
                            }
                        });
                });
                break;
            case "documento":
                $query->whereIn("arquivo_url", function ($subQuery) {
                    $subQuery
                        ->select("arquivo_url")
                        ->from("materiais_didaticos")
                        ->where(function ($q) {
                            $extensoes = [
                                "pdf",
                                "doc",
                                "docx",
                                "txt",
                                "rtf",
                                "odt",
                            ];
                            foreach ($extensoes as $ext) {
                                $q->orWhere("arquivo_url", "like", "%.{$ext}");
                            }
                        });
                });
                break;
            case "apresentacao":
                $query->whereIn("arquivo_url", function ($subQuery) {
                    $subQuery
                        ->select("arquivo_url")
                        ->from("materiais_didaticos")
                        ->where(function ($q) {
                            $extensoes = ["ppt", "pptx", "odp"];
                            foreach ($extensoes as $ext) {
                                $q->orWhere("arquivo_url", "like", "%.{$ext}");
                            }
                        });
                });
                break;
        }

        if ($request->has("disciplina_id")) {
            $query->where("id_disciplina", $request->disciplina_id);
        }

        $materiais = $query->orderBy("created_at", "desc")->get();

        return response()->json([
            "success" => true,
            "data" => $materiais,
            "message" => "Materiais da categoria '{$categoria}' listados com sucesso",
        ]);
    }
}
