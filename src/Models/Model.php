<?php

namespace App\Models;

use App\Models\Relation;
use ArrayAccess;
use Carbon\Carbon;

abstract class Model implements ArrayAccess
{
    protected static \PDO $pdo;
    protected static string $table = '';

    protected array $attributes = [];
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $selects = [];
    protected array $orders = [];
    protected array $relations = [];
    protected array $eagerLoad = [];
    protected bool $distinct = false;

    public static function setConnection(\PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public function __get(string $key): mixed
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }
        if (method_exists($this, $key)) {
            $relation = $this->$key();
            $this->relations[$key] = $relation instanceof Relation ? $relation->getResults() : $relation;
            return $this->relations[$key];
        }
        return null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (property_exists($this, (string) $offset)) {
            return $this->$offset;
        }
        return $this->__get((string) $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->attributes[] = $value;
        } elseif (property_exists($this, (string) $offset)) {
            $this->$offset = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, (string) $offset)
            || isset($this->attributes[$offset])
            || isset($this->relations[$offset]);
    }

    public function offsetUnset(mixed $offset): void
    {
        if (property_exists($this, (string) $offset)) {
            unset($this->$offset);
        } else {
            unset($this->attributes[$offset]);
        }
    }

    public static function __callStatic(string $method, array $arguments): mixed
    {
        return (new static())->$method(...$arguments);
    }

    // Для вызова по цепочке на объекте: ->where(...)->get()
    public function __call(string $method, array $arguments): mixed
    {
        $internalMethod = '_' . $method;

        if (method_exists($this, $internalMethod)) {
            return $this->$internalMethod(...$arguments);
        }

        throw new \BadMethodCallException("Метод {$method} не существует в " . static::class);
    }

    public function setRelation(string $name, mixed $value): void
    {
        $this->relations[$name] = $value;
    }

    public function _select(array $cols): static
    {
        $this->selects = array_merge($this->selects, $cols);
        return $this;
    }

    public function _with(array $relations): static
    {
        $this->eagerLoad = array_merge($this->eagerLoad, $relations);
        return $this;
    }

    public function _get(string $orderBy = 'DESC', false|int $limit = false): array
    {
        $table = static::$table;
        $sql = "SELECT";

        if ($this->distinct) {
            $sql .= ' DISTINCT';
        }

        if (!empty($this->selects)) {
            $sql .= ' ' . implode(', ', $this->selects);
        } else {
            $sql .= ' *';
        }

        $sql .= " FROM {$table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        } elseif (!$this->distinct) {
            $sql .= " ORDER BY id {$orderBy}";
        }

        if ($limit !== false) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($this->bindings);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [];
        }

        $models = [];
        foreach ($rows as $row) {
            $model = new static();
            $model->attributes = $row;
            $models[] = $model;
        }

        if (!empty($this->eagerLoad) && !empty($models)) {
            foreach ($this->eagerLoad as $relationName) {
                if (method_exists($models[0], $relationName)) {
                    $relation = $models[0]->$relationName();
                    if ($relation instanceof Relation) {
                        $relation->eagerLoad($models, $relationName);
                    }
                }
            }
        }

        return $models;
    }

    public function _first(): ?static
    {
        return $this->get('DESC', 1)[0] ?? null;
    }

    public function _where(string $column, int|string $operator, mixed $value = null): static
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = "$column $operator ?";
        $this->bindings[] = $value;

        return $this;
    }

    public function _whereIn(string $col, array $values): static
    {
        if (empty($values)) {
            $this->wheres[] = "1 = 0";
            return $this;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $this->wheres[] = "$col IN ($placeholders)";
        $this->bindings = array_merge($this->bindings, array_values($values));

        return $this;
    }

    public function _whereInOne(string $col, array $values, string $mode = 'MAX'): static
    {
        if (empty($values)) {
            $this->wheres[] = "1 = 0";
            return $this;
        }

        $table = static::$table;
        $func = strtoupper($mode) === 'MIN' ? 'MIN' : 'MAX';

        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $this->wheres[] = "id IN (SELECT $func(id) FROM $table WHERE $col IN ($placeholders) GROUP BY $col)";
        $this->bindings = array_merge($this->bindings, array_values($values));

        return $this;
    }

    public function _distinct(): static
    {
        $this->distinct = true;
        return $this;
    }

    public static function find(int|string $id): ?static
    {
        return static::where('id', '=', $id)->get('DESC', 1)[0] ?? null;
    }

    public function save(): bool
    {
        $table = static::$table;

        if (isset($this->attributes['id'])) {
            // UPDATE
            $fields = [];
            foreach ($this->attributes as $key => $value) {
                if ($key !== 'id') {
                    $fields[] = "{$key} = :{$key}";
                }
            }
            $fieldsSql = implode(', ', $fields);

            $sql = "UPDATE {$table} SET {$fieldsSql} WHERE id = :id";
            $stmt = self::$pdo->prepare($sql);

            return $stmt->execute($this->attributes);
        } else {
            // INSERT
            if (!isset($this->attributes['created_at'])) {
                $this->attributes['created_at'] = Carbon::now();
            }

            $columns = implode(', ', array_keys($this->attributes));
            $placeholders = ':' . implode(', :', array_keys($this->attributes));

            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = self::$pdo->prepare($sql);
            $result = $stmt->execute($this->attributes);

            if ($result) {
                $this->attributes['id'] = (int) self::$pdo->lastInsertId();
            }

            return $result;
        }
    }

    public function _orderBy(string $column, string $direction = 'DESC'): static
    {
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        // Очищаем имя колонки от лишних символов для базовой защиты от SQL-инъекций
        $column = preg_replace('/[^a-zA-Z0-9_.]/', '', $column);
        $this->orders[] = "{$column} {$dir}";
        return $this;
    }

    protected function hasOne(string $relatedClass, string $foreignKey, string $localKey = 'id'): Relation
    {
        return new Relation('hasOne', $this, $relatedClass, $foreignKey, $localKey);
    }

    protected function hasMany(string $relatedClass, string $foreignKey, string $localKey = 'id'): Relation
    {
        return new Relation('hasMany', $this, $relatedClass, $foreignKey, $localKey);
    }
}
