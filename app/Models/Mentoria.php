<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mentoria extends Model
{
    use HasFactory;

    protected $table = "mentorias";
    protected $primaryKey = "id_mentoria";

    protected $fillable = [
        "id_aluno_solicitante",
        "id_veterano",
        "id_disciplina",
        "data_hora",
        "status",
    ];

    protected $casts = [
        "data_hora" => "datetime",
        "status" => "string",
    ];

    /**
     * Relacionamento muitos-para-um com Aluno (solicitante)
     */
    public function alunoSolicitante(): BelongsTo
    {
        return $this->belongsTo(
            Aluno::class,
            "id_aluno_solicitante",
            "id_aluno",
        );
    }

    /**
     * Relacionamento muitos-para-um com Aluno (veterano)
     */
    public function veterano(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, "id_veterano", "id_aluno");
    }

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
     * Scope para buscar mentorias por status
     */
    public function scopePorStatus($query, $status)
    {
        return $query->where("status", $status);
    }

    /**
     * Scope para buscar mentorias agendadas
     */
    public function scopeAgendadas($query)
    {
        return $query->where("status", "agendada");
    }

    /**
     * Scope para buscar mentorias concluídas
     */
    public function scopeConcluidas($query)
    {
        return $query->where("status", "concluida");
    }

    /**
     * Scope para buscar mentorias canceladas
     */
    public function scopeCanceladas($query)
    {
        return $query->where("status", "cancelada");
    }

    /**
     * Scope para buscar mentorias de um aluno específico (como solicitante)
     */
    public function scopeDoAluno($query, $alunoId)
    {
        return $query->where("id_aluno_solicitante", $alunoId);
    }

    /**
     * Scope para buscar mentorias de um veterano específico
     */
    public function scopeDoVeterano($query, $veteranoId)
    {
        return $query->where("id_veterano", $veteranoId);
    }

    /**
     * Scope para buscar mentorias de uma disciplina específica
     */
    public function scopeDaDisciplina($query, $disciplinaId)
    {
        return $query->where("id_disciplina", $disciplinaId);
    }

    /**
     * Scope para buscar mentorias futuras
     */
    public function scopeFuturas($query)
    {
        return $query->where("data_hora", ">", now());
    }

    /**
     * Scope para buscar mentorias passadas
     */
    public function scopePassadas($query)
    {
        return $query->where("data_hora", "<", now());
    }

    /**
     * Verifica se a mentoria está agendada
     */
    public function isAgendada(): bool
    {
        return $this->status === "agendada";
    }

    /**
     * Verifica se a mentoria foi concluída
     */
    public function isConcluida(): bool
    {
        return $this->status === "concluida";
    }

    /**
     * Verifica se a mentoria foi cancelada
     */
    public function isCancelada(): bool
    {
        return $this->status === "cancelada";
    }

    /**
     * Verifica se a mentoria é futura
     */
    public function isFutura(): bool
    {
        return $this->data_hora > now();
    }

    /**
     * Verifica se a mentoria já passou
     */
    public function isPassada(): bool
    {
        return $this->data_hora < now();
    }

    /**
     * Marca a mentoria como concluída
     */
    public function marcarComoConcluida(): bool
    {
        $this->status = "concluida";
        return $this->save();
    }

    /**
     * Cancela a mentoria
     */
    public function cancelar(): bool
    {
        $this->status = "cancelada";
        return $this->save();
    }

    /**
     * Accessor para duração estimada (em horas)
     */
    public function getDuracaoEstimadaAttribute(): int
    {
        return 1; // Duração padrão de 1 hora
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute(): string
    {
        $status = [
            "agendada" => "Agendada",
            "concluida" => "Concluída",
            "cancelada" => "Cancelada",
        ];

        return $status[$this->status] ?? $this->status;
    }
}
