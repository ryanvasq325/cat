<?php

declare(strict_types=1);

namespace app\database\migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507213252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Contact';
    }

    public function up(Schema $schema): void
    {
        
    }

    public function down(Schema $schema): void
    {
        // escreva aqui o rollback do up()
    }
}