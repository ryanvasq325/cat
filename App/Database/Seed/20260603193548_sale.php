<?php

declare(strict_types=1);

namespace App\Database\Seed;

use App\Database\AbstractSeed;
use Faker\Factory;

final class Seed20260603193548 extends AbstractSeed
{
     public function getDescription(): string
    {
        return 'Popular tabela sale com dados de teste';
    }

    public function run(): void
    {
        // Inicializa o Faker configurado para português do Brasil
        $faker = Factory::create('pt_BR');

        // Define a quantidade de registros falsos que deseja gerar
        $quantidade = 10;

        for ($i = 0; $i < $quantidade; $i++) {
            // Gera uma frase aleatória curta e garante que ela seja única
            $descricaoFalsa = $faker->unique()->sentence(3);

            $this->insertIfNotExists(
                'sale',
                [
                    'descricao' => $descricaoFalsa,
                    'ativo'     => $faker->boolean(80), // 80% de chance de ser true
                    'criado_em' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                ],
                ['descricao'] // Mantém a segurança contra duplicados na coluna UNIQUE
            );
        }
    }
}
