<?php

declare(strict_types=1);

namespace App\Database\Builder;

use App\Database\Connection;

class SelectQuery
{
    private ?string $table  = null;
    private ?string $fields = null;
    private string  $order;
    private string  $group;
    private int     $limit  = 10;
    private int     $offset = 0;
    private array   $where  = [];
    private array   $join   = [];
    private array   $binds  = [];
    private string  $limits;

    public static function select(string $fields = '*'): self
    {
        $self         = new self;
        $self->fields = $fields;
        return $self;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(
        string           $field,
        string           $operator,
        string|int|bool|float $value,
        ?string          $logic = null
    ): self {
        $op = strtoupper($operator);

        $placeholder = str_contains($field, '.')
            ? substr($field, strpos($field, '.') + 1)
            : $field;
        $placeholder = preg_replace('/[^a-z0-9_]/i', '_', $placeholder);

        $base = $placeholder;
        $i    = 1;
        while (array_key_exists($placeholder, $this->binds)) {
            $placeholder = "{$base}_" . (++$i);
        }

        $lhs = ($op === 'LIKE' || $op === 'ILIKE') ? "{$field}::TEXT" : $field;

        $param = ($op === 'LIKE' || $op === 'ILIKE')
            ? '%' . (string) $value . '%'
            : $value;

        $cond          = "{$lhs} {$op} :{$placeholder}";
        $this->where[] = $logic ? "({$cond}) {$logic}" : "({$cond})";
        $this->binds[$placeholder] = $param;

        return $this;
    }

    public function join(string $foreignTable, string $logic, string $type = 'inner'): self
    {
        $this->join[] = " {$type} join {$foreignTable} on {$logic}";
        return $this;
    }

    public function group(string $field): self
    {
        $this->group = " group by {$field}";
        return $this;
    }

    public function order(string $field, string $value): self
    {
        $this->order = " order by {$field} {$value}";
        return $this;
    }

    public function limit(int $limit, int $offset): self
    {
        $this->limit  = $limit;
        $this->offset = $offset;
        $this->limits = " limit {$this->limit} offset {$this->offset}";
        return $this;
    }

    public function between(
        string     $field,
        string|int $value1,
        string|int $value2,
        ?string    $logic = null
    ): self {
        $ph1 = $field . '_1';
        $ph2 = $field . '_2';
        $this->where[]   = "{$field} between :{$ph1} and :{$ph2} {$logic}";
        $this->binds[$ph1] = $value1;
        $this->binds[$ph2] = $value2;
        return $this;
    }

    private function createQuery(): string
    {
        if (!$this->fields) {
            throw new \Exception('A query precisa chamar o método select.');
        }
        if (!$this->table) {
            throw new \Exception('A query precisa chamar o método from.');
        }

        $query  = "select {$this->fields} from {$this->table}";
        $query .= count($this->join)  ? implode(' ', $this->join) : '';
        $query .= count($this->where) ? ' where ' . implode(' ', $this->where) : '';
        $query .= $this->group  ?? '';
        $query .= $this->order  ?? '';
        $query .= $this->limits ?? '';

        return $query;
    }

    public function fetch(bool $isArray = true): mixed
    {
        $query = $this->createQuery();
        try {
            $conn    = Connection::connection();
            $result  = $conn->executeQuery($query, $this->binds);
            return $isArray
                ? $result->fetchAssociative()
                : (object) $result->fetchAssociative();
        } catch (\Exception $e) {
            throw new \Exception("Restrição: {$e->getMessage()}");
        }
    }

    public function fetchAll(bool $isArray = true): array
    {
        $query = $this->createQuery();
        try {
            $conn   = Connection::connection();
            $result = $conn->executeQuery($query, $this->binds);
            $rows   = $result->fetchAllAssociative();
            return $isArray ? $rows : array_map(fn($r) => (object) $r, $rows);
        } catch (\Exception $e) {
            throw new \Exception("Restrição: {$e->getMessage()}");
        }
    }
}