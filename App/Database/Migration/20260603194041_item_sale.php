<?php

declare(strict_types=1);

namespace App\Database\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603195223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'item_sale';
    }

    public function up(Schema $schema): void
    {
        // escreva aqui as alterações
    }

    public function down(Schema $schema): void
    {
        // escreva aqui o rollback do up()
    }
}