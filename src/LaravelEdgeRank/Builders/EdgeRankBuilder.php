<?php

namespace LaravelEdgeRank\Builders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use LaravelEdgeRank\Models\Post;

class EdgeRankBuilder
{
    private ?Model $model = null;

    private int $offset = 0;

    private ?int $limit = null;

    private array $weights = [];

    private array $after = null;

    public function __construct(
        ?Model $model = null
    ) {
        $this->model = $model ?: new(config('edgerank.models.main', Post::class));
        $this->weights = config('edgerank.weights');
    }

    public static function make(?Model $model = null): EdgeRankBuilder
    {
        return new self($model);
    }

    protected function scoring(Builder $query): void
    {
        $select = [];

        $bindings = [];

        foreach ($this->weights as $name => $weight) {
            if (method_exists($this->model, $name)) {
                $relation = (new $this->model)->$name();
                $relatedTable = $relation->getRelated()->getTable();
                $foreignKey = $relation->getForeignKeyName() ?? $relation->getQualifiedForeignKeyName();

                $select[] = sprintf(
                    "((SELECT COUNT(*) FROM %s WHERE %s = %s.id) * ?)",
                    $relatedTable,
                    $foreignKey,
                    $this->model->getTable(),
                    $name
                );

                $bindings[] = $weight;
            }
        }

        $bindings[] = config('edgerank.weights.time_decay') / 100000;
        $bindings[] = now();

        if (!empty($select)) {
            $query->selectRaw(
                sprintf(
                    '((1 + %s) * EXP(-? * TIMESTAMPDIFF(SECOND, created_at, ?))) as score',
                    implode(' + ', $select)
                ),

                $bindings
            );
        } else {
            $query->selectRaw(
                'EXP(-:time_decay * POW(TIMESTAMPDIFF(HOUR, created_at, :now), :time_exponent)) as score',
                $bindings
            );
        }
    }

    public function query(bool $withScoring = true): Builder
    {
        $model = $this->model;

        $query = ($this->model)::query()
            ->select("*")
            ->orderByDesc('score')
            ->orderByDesc('created_at')
            ->offset($this->offset)
            ->limit($this->limit);

        if ($this->after !== null) {
            $query->where('id', '>', $this->after);
        }

        if ($withScoring) {
            $this->scoring($query);
        }

        return $query;
    }

    public function get()
    {
        return $this->query()->get();
    }

    public function weights(array $weights): self
    {
        $this->weights = $weights;
        return $this;
    }

    public function offset(int $offset): EdgeRankBuilder
    {
        $this->offset = $offset;
        return $this;
    }

    public function limit(?int $limit): EdgeRankBuilder
    {
        $this->limit = $limit;
        return $this;
    }
}
