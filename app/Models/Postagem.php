<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Postagem extends Model
{
    use HasFactory;

    protected $table = "postagens";
    protected $primaryKey = "id_postagem";

    protected $fillable = ["id_aluno", "titulo", "conteudo", "data_postagem"];

    protected $casts = [
        "data_postagem" => "datetime",
    ];

    /**
     * Relacionamento muitos-para-um com Aluno
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, "id_aluno", "id_aluno");
    }

    /**
     * Relacionamento um-para-muitos com Comentario
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, "id_postagem", "id_postagem");
    }

    /**
     * Scope para buscar postagens de um aluno específico
     */
    public function scopeDoAluno($query, $alunoId)
    {
        return $query->where("id_aluno", $alunoId);
    }

    /**
     * Scope para buscar postagens recentes
     */
    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where("data_postagem", ">=", now()->subDays($dias));
    }

    /**
     * Scope para buscar postagens por período
     */
    public function scopeEntreDatas($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween("data_postagem", [$dataInicio, $dataFim]);
    }

    /**
     * Scope para buscar postagens ordenadas por data (mais recentes primeiro)
     */
    public function scopeOrdenadaPorData($query)
    {
        return $query->orderBy("data_postagem", "desc");
    }

    /**
     * Scope para buscar postagens com comentários
     */
    public function scopeComComentarios($query)
    {
        return $query->whereHas("comentarios");
    }

    /**
     * Scope para buscar postagens populares (com mais comentários)
     */
    public function scopePopulares($query)
    {
        return $query
            ->withCount("comentarios")
            ->orderBy("comentarios_count", "desc");
    }

    /**
     * Verifica se a postagem tem comentários
     */
    public function temComentarios(): bool
    {
        return $this->comentarios()->exists();
    }

    /**
     * Accessor para contar total de comentários
     */
    public function getTotalComentariosAttribute(): int
    {
        return $this->comentarios()->count();
    }

    /**
     * Accessor para resumo do conteúdo
     */
    public function getResumoConteudoAttribute(): string
    {
        return \Str::limit($this->conteudo, 150);
    }

    /**
     * Accessor para verificar se a postagem é recente (últimas 24h)
     */
    public function getIsRecenteAttribute(): bool
    {
        return $this->data_postagem >= now()->subDay();
    }

    /**
     * Accessor para tempo decorrido desde a postagem
     */
    public function getTempoDecorridoAttribute(): string
    {
        return $this->data_postagem->diffForHumans();
    }

    /**
     * Accessor para data formatada em português
     */
    public function getDataFormatadaAttribute(): string
    {
        return $this->data_postagem->format("d/m/Y H:i");
    }

    /**
     * Accessor para slug da postagem
     */
    public function getSlugAttribute(): string
    {
        return \Str::slug($this->titulo);
    }

    /**
     * Scope para buscar por título ou conteúdo
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where("titulo", "like", "%{$termo}%")->orWhere(
                "conteudo",
                "like",
                "%{$termo}%",
            );
        });
    }

    /**
     * Mutator para garantir que o título seja sempre title case
     */
    public function setTituloAttribute($value)
    {
        $this->attributes["titulo"] = \Str::title($value);
    }
}
