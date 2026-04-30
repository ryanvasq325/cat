<?php

declare(strict_types=1);

namespace App\Database\Builder;

use App\Database\Connection;

class UpdateQuery
{
    private string $table;
    private array  $fieldsAndValues = [];
    private array  $where           = [];
    private array  $binds           = [];

    public static function table(string $table): self
    {
        $self        = new self;
        $self->table = $table;
        return $self;
    }

    public function set(array $fieldsAndValues): self
    {
        $this->fieldsAndValues = $fieldsAndValues;
        return $this;
    }

    public function where(string $field, string $operator, string|int $value, ?string $logic = null): self
    {
        $placeholder = str_contains($field, '.')
            ? substr($field, strpos($field, '.') + 1)
            : $field;

        $this->where[]             = "{$field} {$operator} :{$placeholder} {$logic}";
        $this->binds[$placeholder] = $value;
        return $this;
    }

    private function createQuery(): string
    {
        if (!$this->table) {
            throw new \Exception('A consulta precisa invocar o método table.');
        }
        if (!$this->fieldsAndValues) {
            throw new \Exception('A consulta precisa dos dados para realizar a atualização.');
        }

        $query = "update {$this->table} set ";

        $setParts = [];
        foreach ($this->fieldsAndValues as $field => $value) {
            $setParts[]              = "{$field} = :{$field}";
            $this->binds[$field]     = $value;
        }

        $query .= implode(', ', $setParts);
        $query .= count($this->where) ? ' where ' . implode(' ', $this->where) : '';

        return $query;
    }

    public function update(): bool
    {
        $query = $this->createQuery();
        try {
            $conn     = Connection::connection();
            $affected = $conn->executeStatement($query, $this->binds);
            return $affected > 0;
        } catch (\Exception $e) {
            throw new \Exception("Restrição: {$e->getMessage()}");
        }
    }
}