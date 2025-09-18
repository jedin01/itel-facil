<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialDidatico extends Model
{
    use HasFactory;

    protected $table = "materiais_didaticos";
    protected $primaryKey = "id_material";

    protected $fillable = [
        "titulo",
        "tipo",
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
     * Relacionamento muitos-para-um com Aluno (quem enviou)
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
            "id_material",
            "id_material",
        );
    }

    /**
     * Scope para buscar materiais por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where("tipo", $tipo);
    }

    /**
     * Scope para buscar materiais de uma disciplina específica
     */
    public function scopeDaDisciplina($query, $disciplinaId)
    {
        return $query->where("id_disciplina", $disciplinaId);
    }

    /**
     * Scope para buscar materiais de um aluno específico
     */
    public function scopeDoAluno($query, $alunoId)
    {
        return $query->where("id_aluno", $alunoId);
    }

    /**
     * Scope para buscar slides
     */
    public function scopeSlides($query)
    {
        return $query->where("tipo", "slide");
    }

    /**
     * Scope para buscar livros
     */
    public function scopeLivros($query)
    {
        return $query->where("tipo", "livro");
    }

    /**
     * Scope para buscar resumos
     */
    public function scopeResumos($query)
    {
        return $query->where("tipo", "resumo");
    }

    /**
     * Scope para buscar tutoriais
     */
    public function scopeTutoriais($query)
    {
        return $query->where("tipo", "tutorial");
    }

    /**
     * Verifica se o material está aprovado pela curadoria
     */
    public function isAprovado(): bool
    {
        return $this->curadorias()->where("status", "aprovado")->exists();
    }

    /**
     * Verifica se o material está pendente de curadoria
     */
    public function isPendente(): bool
    {
        return $this->curadorias()->where("status", "pendente")->exists();
    }

    /**
     * Verifica se o material foi rejeitado pela curadoria
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

    /**
     * Accessor para verificar se é um arquivo de imagem
     */
    public function getIsImagemAttribute(): bool
    {
        $extensoes = ["jpg", "jpeg", "png", "gif", "bmp", "svg", "webp"];
        return in_array(strtolower($this->extensao_arquivo), $extensoes);
    }

    /**
     * Accessor para verificar se é um arquivo de documento
     */
    public function getIsDocumentoAttribute(): bool
    {
        $extensoes = ["pdf", "doc", "docx", "txt", "rtf", "odt"];
        return in_array(strtolower($this->extensao_arquivo), $extensoes);
    }

    /**
     * Accessor para verificar se é um arquivo de apresentação
     */
    public function getIsApresentacaoAttribute(): bool
    {
        $extensoes = ["ppt", "pptx", "odp"];
        return in_array(strtolower($this->extensao_arquivo), $extensoes);
    }
}
