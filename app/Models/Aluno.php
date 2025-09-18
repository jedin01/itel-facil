<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Aluno extends Model
{
    use HasFactory;

    protected $table = "alunos";
    protected $primaryKey = "id_aluno";

    protected $fillable = [
        "nome_completo",
        "email",
        "senha",
        "ano_escolar",
        "tipo",
        "data_cadastro",
    ];

    protected $casts = [
        "data_cadastro" => "datetime",
        "tipo" => "string",
    ];

    protected $hidden = ["senha"];

    /**
     * Relacionamento muitos-para-muitos com Curso
     */
    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(
            Curso::class,
            "aluno_curso",
            "id_aluno",
            "id_curso",
        );
    }

    /**
     * Relacionamento um-para-muitos com Enunciado (aluno que fez upload)
     */
    public function enunciados(): HasMany
    {
        return $this->hasMany(Enunciado::class, "id_aluno", "id_aluno");
    }

    /**
     * Relacionamento um-para-muitos com MaterialDidatico (aluno que enviou)
     */
    public function materiaisDidaticos(): HasMany
    {
        return $this->hasMany(MaterialDidatico::class, "id_aluno", "id_aluno");
    }

    /**
     * Relacionamento um-para-um com Garimpo
     */
    public function garimpo(): HasOne
    {
        return $this->hasOne(Garimpo::class, "id_aluno", "id_aluno");
    }

    /**
     * Relacionamento um-para-muitos com Mentoria (como solicitante)
     */
    public function mentoriasSolicitadas(): HasMany
    {
        return $this->hasMany(
            Mentoria::class,
            "id_aluno_solicitante",
            "id_aluno",
        );
    }

    /**
     * Relacionamento um-para-muitos com Mentoria (como veterano)
     */
    public function mentoriasComoVeterano(): HasMany
    {
        return $this->hasMany(Mentoria::class, "id_veterano", "id_aluno");
    }

    /**
     * Relacionamento um-para-muitos com Postagem
     */
    public function postagens(): HasMany
    {
        return $this->hasMany(Postagem::class, "id_aluno", "id_aluno");
    }

    /**
     * Relacionamento um-para-muitos com Comentario
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, "id_aluno", "id_aluno");
    }

    /**
     * Relacionamento um-para-muitos com CuradoriaConteudo (como curador)
     */
    public function curadorias(): HasMany
    {
        return $this->hasMany(
            CuradoriaConteudo::class,
            "id_aluno_curador",
            "id_aluno",
        );
    }

    /**
     * Verifica se o aluno é veterano
     */
    public function isVeterano(): bool
    {
        return $this->tipo === "veterano";
    }

    /**
     * Scope para buscar apenas veteranos
     */
    public function scopeVeteranos($query)
    {
        return $query->where("tipo", "veterano");
    }

    /**
     * Scope para buscar apenas alunos
     */
    public function scopeAlunos($query)
    {
        return $query->where("tipo", "aluno");
    }
}
