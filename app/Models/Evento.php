<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = "eventos";
    protected $primaryKey = "id_evento";

    protected $fillable = [
        "titulo",
        "descricao",
        "data_inicio",
        "data_fim",
        "tipo",
    ];

    protected $casts = [
        "data_inicio" => "datetime",
        "data_fim" => "datetime",
        "tipo" => "string",
    ];

    /**
     * Scope para buscar eventos por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where("tipo", $tipo);
    }

    /**
     * Scope para buscar avaliações
     */
    public function scopeAvaliacoes($query)
    {
        return $query->where("tipo", "avaliacao");
    }

    /**
     * Scope para buscar defesas
     */
    public function scopeDefesas($query)
    {
        return $query->where("tipo", "defesa");
    }

    /**
     * Scope para buscar feriados
     */
    public function scopeFeriados($query)
    {
        return $query->where("tipo", "feriado");
    }

    /**
     * Scope para buscar outros eventos
     */
    public function scopeOutros($query)
    {
        return $query->where("tipo", "outro");
    }

    /**
     * Scope para buscar eventos futuros
     */
    public function scopeFuturos($query)
    {
        return $query->where("data_inicio", ">", now());
    }

    /**
     * Scope para buscar eventos passados
     */
    public function scopePassados($query)
    {
        return $query->where("data_fim", "<", now());
    }

    /**
     * Scope para buscar eventos em andamento
     */
    public function scopeEmAndamento($query)
    {
        return $query
            ->where("data_inicio", "<=", now())
            ->where("data_fim", ">=", now());
    }

    /**
     * Scope para buscar eventos de hoje
     */
    public function scopeHoje($query)
    {
        return $query
            ->whereDate("data_inicio", "<=", today())
            ->whereDate("data_fim", ">=", today());
    }

    /**
     * Scope para buscar eventos desta semana
     */
    public function scopeDestaSemana($query)
    {
        return $query->whereBetween("data_inicio", [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope para buscar eventos deste mês
     */
    public function scopeDesteMes($query)
    {
        return $query->whereBetween("data_inicio", [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }

    /**
     * Scope para buscar eventos em um período específico
     */
    public function scopeEntreDatas($query, $dataInicio, $dataFim)
    {
        return $query->where(function ($q) use ($dataInicio, $dataFim) {
            $q->whereBetween("data_inicio", [$dataInicio, $dataFim])
                ->orWhereBetween("data_fim", [$dataInicio, $dataFim])
                ->orWhere(function ($q2) use ($dataInicio, $dataFim) {
                    $q2->where("data_inicio", "<=", $dataInicio)->where(
                        "data_fim",
                        ">=",
                        $dataFim,
                    );
                });
        });
    }

    /**
     * Verifica se o evento é futuro
     */
    public function isFuturo(): bool
    {
        return $this->data_inicio > now();
    }

    /**
     * Verifica se o evento já passou
     */
    public function isPassado(): bool
    {
        return $this->data_fim < now();
    }

    /**
     * Verifica se o evento está em andamento
     */
    public function isEmAndamento(): bool
    {
        return $this->data_inicio <= now() && $this->data_fim >= now();
    }

    /**
     * Verifica se o evento é de hoje
     */
    public function isHoje(): bool
    {
        return $this->data_inicio->isToday() ||
            $this->data_fim->isToday() ||
            ($this->data_inicio <= now() && $this->data_fim >= now());
    }

    /**
     * Verifica se é uma avaliação
     */
    public function isAvaliacao(): bool
    {
        return $this->tipo === "avaliacao";
    }

    /**
     * Verifica se é uma defesa
     */
    public function isDefesa(): bool
    {
        return $this->tipo === "defesa";
    }

    /**
     * Verifica se é um feriado
     */
    public function isFeriado(): bool
    {
        return $this->tipo === "feriado";
    }

    /**
     * Accessor para duração do evento em horas
     */
    public function getDuracaoHorasAttribute(): float
    {
        return $this->data_inicio->diffInHours($this->data_fim);
    }

    /**
     * Accessor para duração do evento em dias
     */
    public function getDuracaoDiasAttribute(): int
    {
        return $this->data_inicio->diffInDays($this->data_fim) + 1;
    }

    /**
     * Accessor para tipo formatado
     */
    public function getTipoFormatadoAttribute(): string
    {
        $tipos = [
            "avaliacao" => "Avaliação",
            "defesa" => "Defesa",
            "feriado" => "Feriado",
            "outro" => "Outro",
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }

    /**
     * Accessor para status do evento
     */
    public function getStatusAttribute(): string
    {
        if ($this->isEmAndamento()) {
            return "em_andamento";
        } elseif ($this->isFuturo()) {
            return "futuro";
        } else {
            return "passado";
        }
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute(): string
    {
        $status = [
            "em_andamento" => "Em Andamento",
            "futuro" => "Futuro",
            "passado" => "Concluído",
        ];

        return $status[$this->status] ?? $this->status;
    }
}
