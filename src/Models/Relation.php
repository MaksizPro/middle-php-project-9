<?php

namespace App\Models;

class Relation
{
    //для использования методов Model
    public Model $query;

    public function __construct(
        public string $type,          // 'hasOne', 'hasMany'
        public Model $parent,
        public string $relatedClass,
        public string $foreignKey,
        public string $localKey
    ) {
        $this->query = new $relatedClass();
    }

    //для использования методов Model
    public function __call(string $method, array $arguments): static
    {
        $this->query->$method(...$arguments);
        return $this;
    }

    // Ленивая загрузка
    public function getResults(): mixed
    {
        $localId = $this->parent->{$this->localKey} ?? null;
        if (!$localId) {
            return in_array($this->type, ['hasMany']) ? [] : null;
        }

        return match ($this->type) {
            'hasOne'        => $this->relatedClass::where($this->foreignKey, $localId)->first(),
            'hasMany'       => $this->relatedClass::where($this->foreignKey, $localId)->get(),
        };
    }

    // Жадная загрузка
    public function eagerLoad(array $models, string $relationName): void
    {
        match ($this->type) {
            'hasOne', 'hasMany' => $this->eagerLoadHas($models, $relationName),
        };
    }

    private function eagerLoadHas(array $models, string $relationName): void
    {
        $ids = array_unique(array_filter(array_map(fn($m) => $m->{$this->localKey}, $models)));

        if (empty($ids)) {
            $relatedItems = [];
        } elseif ($this->type === 'hasOne') {
            // Определяем режим MAX или MIN по сортировке
            $mode = 'MAX';
            if (!empty($this->query->orders) && str_contains(strtoupper($this->query->orders[0]), 'ASC')) {
                $mode = 'MIN';
            }
            $relatedItems = $this->query->whereInOne($this->foreignKey, $ids, $mode)->get();
        } else {
            $relatedItems = $this->query->whereIn($this->foreignKey, $ids)->get();
        }

        $grouped = [];
        foreach ($relatedItems as $item) {
            $grouped[$item->{$this->foreignKey}][] = $item;
        }

        foreach ($models as $model) {
            $key = $model->{$this->localKey};
            $value = $this->type === 'hasOne' ? ($grouped[$key][0] ?? null) : ($grouped[$key] ?? []);
            $model->setRelation($relationName, $value);
        }
    }
}
