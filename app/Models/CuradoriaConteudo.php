<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuradoriaConteudo extends Model
{
    use HasFactory;

    protected $table = "curadorias_conteudo";
    protected $primaryKey = "id_curadoria";

    protected $fillable = [
        "id_material",
        "id_enunciado",
        "id_aluno_curador",
        "status",
        "data_avaliacao",
    ];

    protected $casts = [
        "data_avaliacao" => "datetime",
        "status" => "string",
    ];

    /**
     * Relacionamento muitos-para-um com MaterialDidatico (opcional)
     */
    public function materialDidatico(): BelongsTo
    {
        return $this->belongsTo(
            MaterialDidatico::class,
            "id_material",
            "id_material",
        );
    }

    /**
     * Relacionamento muitos-para-um com Enunciado (opcional)
     */
    public function enunciado(): BelongsTo
    {
        return $this->belongsTo(
            Enunciado::class,
            "id_enunciado",
            "id_enunciado",
        );
    }

    /**
     * Relacionamento muitos-para-um com Aluno (curador)
     */
    public function alunoCurador(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, "id_aluno_curador", "id_aluno");
    }

    /**
     * Scope para buscar curadorias por status
     */
    public function scopePorStatus($query, $status)
    {
        return $query->where("status", $status);
    }

    /**
     * Scope para buscar curadorias aprovadas
     */
    public function scopeAprovadas($query)
    {
        return $query->where("status", "aprovado");
    }

    /**
     * Scope para buscar curadorias rejeitadas
     */
    public function scopeRejeitadas($query)
    {
        return $query->where("status", "rejeitado");
    }

    /**
     * Scope para buscar curadorias pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where("status", "pendente");
    }

    /**
     * Scope para buscar curadorias de materiais didáticos
     */
    public function scopeMateriaisDidaticos($query)
    {
        return $query->whereNotNull("id_material");
    }

    /**
     * Scope para buscar curadorias de enunciados
     */
    public function scopeEnunciados($query)
    {
        return $query->whereNotNull("id_enunciado");
    }

    /**
     * Scope para buscar curadorias de um curador específico
     */
    public function scopeDoCurador($query, $curadorId)
    {
        return $query->where("id_aluno_curador", $curadorId);
    }

    /**
     * Scope para buscar curadorias avaliadas em um período
     */
    public function scopeAvaliadasEntre($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween("data_avaliacao", [$dataInicio, $dataFim]);
    }

    /**
     * Scope para buscar curadorias recentes
     */
    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where("created_at", ">=", now()->subDays($dias));
    }

    /**
     * Verifica se a curadoria está aprovada
     */
    public function isAprovada(): bool
    {
        return $this->status === "aprovado";
    }

    /**
     * Verifica se a curadoria foi rejeitada
     */
    public function isRejeitada(): bool
    {
        return $this->status === "rejeitado";
    }

    /**
     * Verifica se a curadoria está pendente
     */
    public function isPendente(): bool
    {
        return $this->status === "pendente";
    }

    /**
     * Verifica se a curadoria é de material didático
     */
    public function isMaterialDidatico(): bool
    {
        return !is_null($this->id_material);
    }

    /**
     * Verifica se a curadoria é de enunciado
     */
    public function isEnunciado(): bool
    {
        return !is_null($this->id_enunciado);
    }

    /**
     * Aprova o conteúdo
     */
    public function aprovar(): bool
    {
        $this->status = "aprovado";
        $this->data_avaliacao = now();
        return $this->save();
    }

    /**
     * Rejeita o conteúdo
     */
    public function rejeitar(): bool
    {
        $this->status = "rejeitado";
        $this->data_avaliacao = now();
        return $this->save();
    }

    /**
     * Retorna o conteúdo que está sendo curado
     */
    public function getConteudo()
    {
        if ($this->isMaterialDidatico()) {
            return $this->materialDidatico;
        }

        if ($this->isEnunciado()) {
            return $this->enunciado;
        }

        return null;
    }

    /**
     * Accessor para tipo de conteúdo
     */
    public function getTipoConteudoAttribute(): string
    {
        if ($this->isMaterialDidatico()) {
            return "Material Didático";
        }

        if ($this->isEnunciado()) {
            return "Enunciado";
        }

        return "Desconhecido";
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute(): string
    {
        $status = [
            "aprovado" => "Aprovado",
            "rejeitado" => "Rejeitado",
            "pendente" => "Pendente",
        ];

        return $status[$this->status] ?? $this->status;
    }

    /**
     * Accessor para verificar se foi avaliado
     */
    public function getFoiAvaliadoAttribute(): bool
    {
        return !is_null($this->data_avaliacao);
    }

    /**
     * Accessor para tempo de avaliação
     */
    public function getTempoAvaliacaoAttribute(): ?string
    {
        if (!$this->foi_avaliado) {
            return null;
        }

        return $this->data_avaliacao->diffForHumans();
    }

    /**
     * Boot method para garantir integridade dos dados
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Garantir que apenas um dos dois campos seja preenchido
            if (!$model->id_material && !$model->id_enunciado) {
                throw new \InvalidArgumentException(
                    "Deve ser especificado id_material ou id_enunciado",
                );
            }

            if ($model->id_material && $model->id_enunciado) {
                throw new \InvalidArgumentException(
                    "Apenas um dos campos id_material ou id_enunciado pode ser preenchido",
                );
            }
        });

        static::updating(function ($model) {
            // Se o status mudou para aprovado ou rejeitado, definir data_avaliacao
            if (
                in_array($model->status, ["aprovado", "rejeitado"]) &&
                is_null($model->data_avaliacao)
            ) {
                $model->data_avaliacao = now();
            }
        });
    }
}
