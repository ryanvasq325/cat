<?php

declare(strict_types=1);

namespace App\Database\Builder;

use App\Database\Connection;

class InsertQuery
{
    private string $table;
    private array  $fieldsAndValues = [];

    public static function insert(string $table): self
    {
        $self        = new self;
        $self->table = $table;
        return $self;
    }

    private function createQuery(): string
    {
        if (!$this->table) {
            throw new \Exception('A consulta precisa invocar o método insert.');
        }
        if (!$this->fieldsAndValues) {
            throw new \Exception('A consulta precisa dos dados para realizar a inserção.');
        }

        $keys   = array_keys($this->fieldsAndValues);
        $query  = "insert into {$this->table} (";
        $query .= implode(', ', $keys);
        $query .= ') values (:';
        $query .= implode(', :', $keys) . ')';

        return $query;
    }

    public function save(array $fieldsAndValues): bool
    {
        $this->fieldsAndValues = $fieldsAndValues;
        $query = $this->createQuery();
        try {
            $conn    = Connection::connection();
            $affected = $conn->executeStatement($query, $this->fieldsAndValues);
            return $affected > 0;
        } catch (\Exception $e) {
            throw new \Exception("Restrição: {$e->getMessage()}");
        }
    }
}