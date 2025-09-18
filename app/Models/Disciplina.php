<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disciplina extends Model
{
    use HasFactory;

    protected $table = "disciplinas";
    protected $primaryKey = "id_disciplina";

    protected $fillable = ["nome", "descricao", "id_curso"];

    /**
     * Relacionamento muitos-para-um com Curso
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, "id_curso", "id_curso");
    }

    /**
     * Relacionamento um-para-muitos com Enunciado
     */
    public function enunciados(): HasMany
    {
        return $this->hasMany(
            Enunciado::class,
            "id_disciplina",
            "id_disciplina",
        );
    }

    /**
     * Relacionamento um-para-muitos com MaterialDidatico
     */
    public function materiaisDidaticos(): HasMany
    {
        return $this->hasMany(
            MaterialDidatico::class,
            "id_disciplina",
            "id_disciplina",
        );
    }

    /**
     * Relacionamento um-para-muitos com Mentoria
     */
    public function mentorias(): HasMany
    {
        return $this->hasMany(
            Mentoria::class,
            "id_disciplina",
            "id_disciplina",
        );
    }

    /**
     * Scope para buscar disciplinas de um curso específico
     */
    public function scopeDoCurso($query, $cursoId)
    {
        return $query->where("id_curso", $cursoId);
    }

    /**
     * Scope para buscar disciplinas com enunciados
     */
    public function scopeComEnunciados($query)
    {
        return $query->whereHas("enunciados");
    }

    /**
     * Scope para buscar disciplinas com materiais didáticos
     */
    public function scopeComMateriaisDidaticos($query)
    {
        return $query->whereHas("materiaisDidaticos");
    }

    /**
     * Accessor para contar total de enunciados
     */
    public function getTotalEnunciadosAttribute(): int
    {
        return $this->enunciados()->count();
    }

    /**
     * Accessor para contar total de materiais didáticos
     */
    public function getTotalMateriaisDidaticosAttribute(): int
    {
        return $this->materiaisDidaticos()->count();
    }

    /**
     * Accessor para contar total de mentorias
     */
    public function getTotalMentoriasAttribute(): int
    {
        return $this->mentorias()->count();
    }
}
