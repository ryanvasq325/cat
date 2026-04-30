<?php

declare(strict_types=1);

namespace App\Database\Builder;

use App\Database\Connection;

class DeleteQuery
{
    private string $table;
    private array  $where = [];
    private array  $binds = [];

    public static function table(string $table): self
    {
        $self        = new self;
        $self->table = $table;
        return $self;
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

        $query  = "delete from {$this->table}";
        $query .= count($this->where) ? ' where ' . implode(' ', $this->where) : '';

        return $query;
    }

    public function delete(): bool
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