<?php

declare(strict_types=1);

namespace App\Database\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603194042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'sale';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('sale');

        $table->addColumn('id',            'bigint',  ['autoincrement' => true,  'notnull' => true]);
        $table->addColumn('id_cliente',      'bigint',  [ 'notnull' => true]);
        $table->addColumn('id_usuario',    'bigint',   [ 'notnull' => true]);
        $table->addColumn('descricao',     'string',  ['length' => 255, 'notnull' => true]);
        $table->addColumn('quantidade',    'decimal',  ['precision' => 18, 'scale' => 4, 'notnull' => false]);
        $table->addColumn('total_bruto',   'decimal',  ['precision' => 18, 'scale' => 4, 'notnull' => false]);
        $table->addColumn('total_liquido', 'decimal',  ['precision' => 18, 'scale' => 4, 'notnull' => false]);
        $table->addColumn('desconto', 'decimal',  ['precision' => 18, 'scale' => 4, 'notnull' => false]);
        $table->addColumn('acrescimo', 'decimal',  ['precision' => 18, 'scale' => 4, 'notnull' => false]);
        $table->addColumn('nome',            'string',  ['length' => 30,  'notnull' => false]);
        $table->addColumn('criado_em',     'datetime', ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('atualizado_em', 'datetime', ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['id_cliente']);
        $table->addIndex(['ativo']);
        $table->addForeignKeyConstraint(
            'customer',
            ['id_cliente'],
            ['id'],
            ['onDelete' => 'RESTRICT', 'onUpdate' => 'CASCADE'],
            'fk_sale_customer'
        );
        $table->addForeignKeyConstraint(
            'users',
            ['id_usuario'],
            ['id'],
            ['onDelete' => 'RESTRICT', 'onUpdate' => 'CASCADE'],
            'fk_sale_users'
        );

        }

    public function down(Schema $schema): void
    {
        $schema->dropTable('sale');
    }
}

