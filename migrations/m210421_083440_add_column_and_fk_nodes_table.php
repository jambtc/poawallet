<?php

use yii\db\Migration;

/**
 * Class m210421_083440_add_column_and_fk_nodes_table
 */
class m210421_083440_add_column_and_fk_nodes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('nodes', 'id_blockchain', $this->integer(11)->after('id'));

        // creates index for column `id_blockchain`
        $this->createIndex(
           '{{%idx-nodes-id_blockchain}}',
           '{{%nodes}}',
           'id_blockchain'
        );

        // add foreign key for table `{{%nodes}}`
        $this->addForeignKey(
            '{{%fk-nodes-id_blockchain}}',
            '{{%nodes}}',
            'id_blockchain',
            '{{%blockchains}}',
            'id',
            'CASCADE'
        );


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%nodes}}`
        $this->dropForeignKey(
            '{{%fk-nodes-id_blockchain}}',
            '{{%nodes}}'
        );

        // drops index for column `id_blockchain`
        $this->dropIndex(
            '{{%idx-nodes-id_blockchain}}',
            '{{%nodes}}'
        );

        $this->dropColumn('nodes', 'id_blockchain');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210421_083440_add_column_and_fk_nodes_table cannot be reverted.\n";

        return false;
    }
    */
}
