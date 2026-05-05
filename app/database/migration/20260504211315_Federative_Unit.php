<?php

declare(strict_types=1);

namespace app\database\migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504211434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Federative_Unit';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('federative_Unit');

        $table->addColumn('id',            'bigint', ['autoincrement' => true]);
        $table->addColumn('id_pais',            'bigint', ['autoincrement' => true]);
        $table->addColumn('codigo', 'string',  ['length' => 255]);
        $table->addColumn('nome', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('sigla',      'string',  ['length' => 18]);
        $table->addColumn('criado_em',     'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('atualizado_em', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);

        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('federative_Unit');
    }
}
