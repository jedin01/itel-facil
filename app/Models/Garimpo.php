<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Garimpo extends Model
{
    use HasFactory;

    protected $table = "garimpos";
    protected $primaryKey = "id_garimpo";

    protected $fillable = [
        "id_aluno",
        "areas_interesse",
        "disponibilidade",
        "descricao",
    ];

    protected $casts = [
        "areas_interesse" => "array",
    ];

    /**
     * Relacionamento um-para-um com Aluno
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, "id_aluno", "id_aluno");
    }

    /**
     * Scope para buscar garimpos por área de interesse
     */
    public function scopePorAreaInteresse($query, $area)
    {
        return $query->whereJsonContains("areas_interesse", $area);
    }

    /**
     * Scope para buscar garimpos disponíveis
     */
    public function scopeDisponiveis($query)
    {
        return $query->whereNotNull("disponibilidade");
    }

    /**
     * Scope para buscar garimpos de veteranos
     */
    public function scopeVeteranos($query)
    {
        return $query->whereHas("aluno", function ($q) {
            $q->where("tipo", "veterano");
        });
    }

    /**
     * Verifica se o aluno está disponível para mentoria
     */
    public function isDisponivel(): bool
    {
        return !empty($this->disponibilidade);
    }

    /**
     * Verifica se o aluno tem experiência em uma área específica
     */
    public function temExperienciaEm(string $area): bool
    {
        $areas = is_array($this->areas_interesse)
            ? $this->areas_interesse
            : json_decode($this->areas_interesse, true) ?? [];

        return in_array($area, $areas);
    }

    /**
     * Accessor para formatar as áreas de interesse
     */
    public function getAreasInteresseFormatadaAttribute(): string
    {
        $areas = is_array($this->areas_interesse)
            ? $this->areas_interesse
            : json_decode($this->areas_interesse, true) ?? [];

        return implode(", ", $areas);
    }

    /**
     * Mutator para garantir que areas_interesse seja sempre um array
     */
    public function setAreasInteresseAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes["areas_interesse"] = json_encode(
                explode(",", $value),
            );
        } elseif (is_array($value)) {
            $this->attributes["areas_interesse"] = json_encode($value);
        } else {
            $this->attributes["areas_interesse"] = json_encode([]);
        }
    }
}
