<?php

namespace App\Models;

use Carbon\Carbon;
use ArrayAccess;

abstract class Model implements ArrayAccess
{
    protected static \PDO $pdo;
    protected static string $table = '';

    protected array $attributes = [];
    protected array $wheres = [];
    protected array $bindings = [];

    public static function setConnection(\PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function offsetGet(mixed $key): mixed
    {
        if (property_exists($this, (string) $key)) {
            return $this->$key;
        }
        return $this->attributes[$key] ?? null;
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        if ($key === null) {
            $this->attributes[] = $value;
        } elseif (property_exists($this, (string) $key)) {
            $this->$key = $value;
        } else {
            $this->attributes[$key] = $value;
        }
    }

    public function offsetExists(mixed $key): bool
    {
        return property_exists($this, (string) $key) || isset($this->attributes[$key]);
    }

    public function offsetUnset(mixed $key): void
    {
        if (property_exists($this, (string) $key)) {
            unset($this->$key);
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

        throw new \BadMethodCallException("Method {$method} does not exist in " . static::class);
    }

    public function _get(string $orderBy = 'DESC', false|int $limit = false): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM {$table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $sql .= " ORDER BY id {$orderBy}";

        if($limit !== false){
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
            $this->where[] = "1 = 0";
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
            $this->where[] = "1 = 0";
            return $this;
        }

        $table = static::$table;
        $func = strtoupper($mode) === 'MIN' ? 'MIN' : 'MAX';

        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $this->wheres[] = "id IN (SELECT $func(id) FROM $table WHERE $col IN ($placeholders) GROUP BY $col)";
        $this->bindings = array_merge($this->bindings, array_values($values));

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
            if(!isset($this->attributes['created_at'])){
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
}