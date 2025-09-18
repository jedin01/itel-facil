<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentario extends Model
{
    use HasFactory;

    protected $table = "comentarios";
    protected $primaryKey = "id_comentario";

    protected $fillable = [
        "id_postagem",
        "id_aluno",
        "conteudo",
        "data_comentario",
    ];

    protected $casts = [
        "data_comentario" => "datetime",
    ];

    /**
     * Relacionamento muitos-para-um com Postagem
     */
    public function postagem(): BelongsTo
    {
        return $this->belongsTo(Postagem::class, "id_postagem", "id_postagem");
    }

    /**
     * Relacionamento muitos-para-um com Aluno
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, "id_aluno", "id_aluno");
    }

    /**
     * Scope para buscar comentários de uma postagem específica
     */
    public function scopeDaPostagem($query, $postagemId)
    {
        return $query->where("id_postagem", $postagemId);
    }

    /**
     * Scope para buscar comentários de um aluno específico
     */
    public function scopeDoAluno($query, $alunoId)
    {
        return $query->where("id_aluno", $alunoId);
    }

    /**
     * Scope para buscar comentários recentes
     */
    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where("data_comentario", ">=", now()->subDays($dias));
    }

    /**
     * Scope para buscar comentários por período
     */
    public function scopeEntreDatas($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween("data_comentario", [$dataInicio, $dataFim]);
    }

    /**
     * Scope para buscar comentários ordenados por data (mais recentes primeiro)
     */
    public function scopeOrdenadoPorData($query)
    {
        return $query->orderBy("data_comentario", "desc");
    }

    /**
     * Scope para buscar comentários ordenados por data (mais antigos primeiro)
     */
    public function scopeOrdenadoPorDataAntigo($query)
    {
        return $query->orderBy("data_comentario", "asc");
    }

    /**
     * Accessor para resumo do conteúdo
     */
    public function getResumoConteudoAttribute(): string
    {
        return \Str::limit($this->conteudo, 100);
    }

    /**
     * Accessor para verificar se o comentário é recente (últimas 24h)
     */
    public function getIsRecenteAttribute(): bool
    {
        return $this->data_comentario >= now()->subDay();
    }

    /**
     * Accessor para tempo decorrido desde o comentário
     */
    public function getTempoDecorridoAttribute(): string
    {
        return $this->data_comentario->diffForHumans();
    }

    /**
     * Accessor para data formatada em português
     */
    public function getDataFormatadaAttribute(): string
    {
        return $this->data_comentario->format("d/m/Y H:i");
    }

    /**
     * Scope para buscar por conteúdo
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where("conteudo", "like", "%{$termo}%");
    }

    /**
     * Verifica se o comentário pertence a um aluno específico
     */
    public function pertenceAo($alunoId): bool
    {
        return $this->id_aluno == $alunoId;
    }

    /**
     * Verifica se o comentário foi editado
     */
    public function foiEditado(): bool
    {
        return $this->updated_at > $this->created_at;
    }

    /**
     * Accessor para verificar se pode ser editado (últimas 24 horas)
     */
    public function getPodeEditarAttribute(): bool
    {
        return $this->data_comentario >= now()->subDay();
    }
}
