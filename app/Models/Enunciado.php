<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enunciado extends Model
{
    use HasFactory;

    protected $table = "enunciados";
    protected $primaryKey = "id_enunciado";

    protected $fillable = [
        "titulo",
        "tipo",
        "ano",
        "arquivo_url",
        "id_disciplina",
        "id_aluno",
    ];

    protected $casts = [
        "tipo" => "string",
    ];

    /**
     * Relacionamento muitos-para-um com Disciplina
     */
    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(
            Disciplina::class,
            "id_disciplina",
            "id_disciplina",
        );
    }

    /**
     * Relacionamento muitos-para-um com Aluno (quem fez upload)
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, "id_aluno", "id_aluno");
    }

    /**
     * Relacionamento um-para-muitos com CuradoriaConteudo
     */
    public function curadorias(): HasMany
    {
        return $this->hasMany(
            CuradoriaConteudo::class,
            "id_enunciado",
            "id_enunciado",
        );
    }

    /**
     * Scope para buscar enunciados por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where("tipo", $tipo);
    }

    /**
     * Scope para buscar enunciados por ano
     */
    public function scopePorAno($query, $ano)
    {
        return $query->where("ano", $ano);
    }

    /**
     * Scope para buscar enunciados de uma disciplina específica
     */
    public function scopeDaDisciplina($query, $disciplinaId)
    {
        return $query->where("id_disciplina", $disciplinaId);
    }

    /**
     * Scope para buscar enunciados de um aluno específico
     */
    public function scopeDoAluno($query, $alunoId)
    {
        return $query->where("id_aluno", $alunoId);
    }

    /**
     * Scope para buscar provas
     */
    public function scopeProvas($query)
    {
        return $query->where("tipo", "prova");
    }

    /**
     * Scope para buscar testes
     */
    public function scopeTestes($query)
    {
        return $query->where("tipo", "teste");
    }

    /**
     * Scope para buscar exames
     */
    public function scopeExames($query)
    {
        return $query->where("tipo", "exame");
    }

    /**
     * Verifica se o enunciado está aprovado pela curadoria
     */
    public function isAprovado(): bool
    {
        return $this->curadorias()->where("status", "aprovado")->exists();
    }

    /**
     * Verifica se o enunciado está pendente de curadoria
     */
    public function isPendente(): bool
    {
        return $this->curadorias()->where("status", "pendente")->exists();
    }

    /**
     * Verifica se o enunciado foi rejeitado pela curadoria
     */
    public function isRejeitado(): bool
    {
        return $this->curadorias()->where("status", "rejeitado")->exists();
    }

    /**
     * Accessor para obter a extensão do arquivo
     */
    public function getExtensaoArquivoAttribute(): string
    {
        return pathinfo($this->arquivo_url, PATHINFO_EXTENSION);
    }

    /**
     * Accessor para obter o nome do arquivo
     */
    public function getNomeArquivoAttribute(): string
    {
        return pathinfo($this->arquivo_url, PATHINFO_BASENAME);
    }
}
