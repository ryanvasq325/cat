<?php

declare(strict_types=1);

namespace App\Database\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504162921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Enterprise';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('enterprise');

        $table->addColumn('id',            'bigint',  ['autoincrement' => true,  'notnull' => true]);
        $table->addColumn('fantasia',      'string',  ['length' => 255, 'notnull' => true]);
        $table->addColumn('razao_social',  'string',  ['length' => 255, 'notnull' => false]);
        $table->addColumn('cnpj',          'string',  ['length' => 18,  'notnull' => false]);
        $table->addColumn('ie',            'string',  ['length' => 30,  'notnull' => false]);
        $table->addColumn('ativo',         'boolean', ['default' => true,  'notnull' => true]);
        $table->addColumn('excluido',      'boolean', ['default' => false, 'notnull' => true]);
        $table->addColumn('criado_em',     'datetime', ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('atualizado_em', 'datetime', ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['cnpj']);
        $table->addIndex(['ativo']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('enterprise');
    }
}

/*

<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ItemSale extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('item_sale', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
            ->addColumn('id_venda', 'biginteger', ['null' => true])
            ->addColumn('id_produto', 'biginteger', ['null' => true])
            ->addColumn('descricao', 'text', ['null' => true])
            ->addColumn('quantidade', 'decimal', ['precision' => 18, 'scale' => 4, 'null' => true])
            ->addColumn('total_bruto', 'decimal', ['precision' => 18, 'scale' => 4, 'null' => true])
            ->addColumn('total_liquido', 'decimal', [
                'precision' => 18,
                'scale' => 4,
                'null' => true,
                'comment' => 'Valor a ser pago produto.'
            ])
            ->addColumn('desconto', 'decimal', ['precision' => 18, 'scale' => 4, 'null' => true])
            ->addColumn('acrescimo', 'decimal', ['precision' => 18, 'scale' => 4, 'null' => true])
            ->addColumn('nome', 'text', ['null' => true])
            ->addColumn('data_cadastro', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('data_atualizacao', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('id_venda', 'sale', 'id', ['delete' => 'CASCADE', 'update' => 'NO ACTION'])
            ->addForeignKey('id_produto', 'product', 'id', ['delete' => 'CASCADE', 'update' => 'NO ACTION'])
            ->create();
    }
}
*/ 
