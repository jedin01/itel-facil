<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $table = "cursos";
    protected $primaryKey = "id_curso";

    protected $fillable = ["nome", "descricao"];

    /**
     * Relacionamento muitos-para-muitos com Aluno
     */
    public function alunos(): BelongsToMany
    {
        return $this->belongsToMany(
            Aluno::class,
            "aluno_curso",
            "id_curso",
            "id_aluno",
        );
    }

    /**
     * Relacionamento um-para-muitos com Disciplina
     */
    public function disciplinas(): HasMany
    {
        return $this->hasMany(Disciplina::class, "id_curso", "id_curso");
    }

    /**
     * Scope para buscar cursos com alunos
     */
    public function scopeComAlunos($query)
    {
        return $query->whereHas("alunos");
    }

    /**
     * Scope para buscar cursos com disciplinas
     */
    public function scopeComDisciplinas($query)
    {
        return $query->whereHas("disciplinas");
    }

    /**
     * Accessor para contar total de alunos
     */
    public function getTotalAlunosAttribute(): int
    {
        return $this->alunos()->count();
    }

    /**
     * Accessor para contar total de disciplinas
     */
    public function getTotalDisciplinasAttribute(): int
    {
        return $this->disciplinas()->count();
    }
}
