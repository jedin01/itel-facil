<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Aluno::with(["cursos", "garimpo"]);

        // Filtros
        if ($request->has("tipo")) {
            $query->where("tipo", $request->tipo);
        }

        if ($request->has("ano_escolar")) {
            $query->where("ano_escolar", $request->ano_escolar);
        }

        if ($request->has("curso_id")) {
            $query->whereHas("cursos", function ($q) use ($request) {
                $q->where("id_curso", $request->curso_id);
            });
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

        // Ordenação
        $orderBy = $request->get("order_by", "nome_completo");
        $orderDirection = $request->get("order_direction", "asc");
        $query->orderBy($orderBy, $orderDirection);

        // Paginação
        $perPage = $request->get("per_page", 15);
        $alunos = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $alunos,
            "message" => "Alunos listados com sucesso",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "nome_completo" => "required|string|max:255",
            "email" => "required|email|unique:alunos,email",
            "senha" => "required|string|min:6",
            "ano_escolar" => "required|string|max:10",
            "tipo" => "required|in:aluno,veterano",
            "cursos" => "array",
            "cursos.*" => "exists:cursos,id_curso",
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
            $data["senha"] = Hash::make($data["senha"]);
            $data["data_cadastro"] = now();

            $aluno = Aluno::create($data);

            // Associar cursos se fornecidos
            if ($request->has("cursos")) {
                $aluno->cursos()->attach($request->cursos);
            }

            $aluno->load(["cursos", "garimpo"]);

            return response()->json(
                [
                    "success" => true,
                    "data" => $aluno,
                    "message" => "Aluno criado com sucesso",
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao criar aluno: " . $e->getMessage(),
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
            $aluno = Aluno::with([
                "cursos",
                "garimpo",
                "enunciados.disciplina",
                "materiaisDidaticos.disciplina",
                "mentoriasSolicitadas.disciplina",
                "mentoriasComoVeterano.disciplina",
                "postagens.comentarios",
                "comentarios.postagem",
                "curadorias",
            ])->findOrFail($id);

            return response()->json([
                "success" => true,
                "data" => $aluno,
                "message" => "Aluno encontrado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Aluno não encontrado",
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
            $aluno = Aluno::findOrFail($id);

            $validator = Validator::make($request->all(), [
                "nome_completo" => "sometimes|string|max:255",
                "email" =>
                    "sometimes|email|unique:alunos,email," . $id . ",id_aluno",
                "senha" => "sometimes|string|min:6",
                "ano_escolar" => "sometimes|string|max:10",
                "tipo" => "sometimes|in:aluno,veterano",
                "cursos" => "array",
                "cursos.*" => "exists:cursos,id_curso",
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

            // Hash da senha se fornecida
            if (isset($data["senha"])) {
                $data["senha"] = Hash::make($data["senha"]);
            }

            $aluno->update($data);

            // Sincronizar cursos se fornecidos
            if ($request->has("cursos")) {
                $aluno->cursos()->sync($request->cursos);
            }

            $aluno->load(["cursos", "garimpo"]);

            return response()->json([
                "success" => true,
                "data" => $aluno,
                "message" => "Aluno atualizado com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao atualizar aluno: " . $e->getMessage(),
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
            $aluno = Aluno::findOrFail($id);
            $aluno->delete();

            return response()->json([
                "success" => true,
                "message" => "Aluno excluído com sucesso",
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Erro ao excluir aluno: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get alunos veteranos disponíveis para mentoria
     */
    public function veteranos(Request $request): JsonResponse
    {
        $query = Aluno::veteranos()
            ->whereHas("garimpo")
            ->with(["garimpo", "cursos"]);

        if ($request->has("area_interesse")) {
            $query->whereHas("garimpo", function ($q) use ($request) {
                $q->porAreaInteresse($request->area_interesse);
            });
        }

        $veteranos = $query->get();

        return response()->json([
            "success" => true,
            "data" => $veteranos,
            "message" => "Veteranos listados com sucesso",
        ]);
    }

    /**
     * Get estatísticas do aluno
     */
    public function estatisticas(string $id): JsonResponse
    {
        try {
            $aluno = Aluno::findOrFail($id);

            $stats = [
                "total_enunciados" => $aluno->enunciados()->count(),
                "total_materiais" => $aluno->materiaisDidaticos()->count(),
                "total_postagens" => $aluno->postagens()->count(),
                "total_comentarios" => $aluno->comentarios()->count(),
                "mentorias_solicitadas" => $aluno
                    ->mentoriasSolicitadas()
                    ->count(),
                "mentorias_realizadas" => $aluno
                    ->mentoriasComoVeterano()
                    ->where("status", "concluida")
                    ->count(),
                "curadorias_realizadas" => $aluno->curadorias()->count(),
            ];

            if ($aluno->isVeterano()) {
                $stats["mentorias_agendadas"] = $aluno
                    ->mentoriasComoVeterano()
                    ->where("status", "agendada")
                    ->where("data_hora", ">", now())
                    ->count();
            }

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
     * Buscar alunos por critérios específicos
     */
    public function buscar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "termo" => "required|string|min:2",
            "tipo" => "sometimes|in:aluno,veterano",
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
        $query = Aluno::where(function ($q) use ($termo) {
            $q->where("nome_completo", "like", "%{$termo}%")->orWhere(
                "email",
                "like",
                "%{$termo}%",
            );
        });

        if ($request->has("tipo")) {
            $query->where("tipo", $request->tipo);
        }

        if ($request->has("curso_id")) {
            $query->whereHas("cursos", function ($q) use ($request) {
                $q->where("id_curso", $request->curso_id);
            });
        }

        $alunos = $query->with(["cursos", "garimpo"])->get();

        return response()->json([
            "success" => true,
            "data" => $alunos,
            "message" => "Busca realizada com sucesso",
        ]);
    }
}
