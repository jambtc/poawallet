<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%nodes}}`.
 */
class m210603_074605_create_nodes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%nodes}}', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'id_blockchain' => $this->integer(11)->notNull(),
            'id_smart_contract' => $this->integer(11)->notNull(),
        ]);

        // creates index for column `id_user`
        $this->createIndex(
            '{{%idx-nodes-id_user}}',
            '{{%nodes}}',
            'id_user'
        );
        // creates index for column `id_blockchain`
        $this->createIndex(
            '{{%idx-nodes-id_blockchain}}',
            '{{%nodes}}',
            'id_blockchain'
        );
        // creates index for column `id_smart_contract`
        $this->createIndex(
            '{{%idx-nodes-id_smart_contract}}',
            '{{%nodes}}',
            'id_smart_contract'
        );

        //add foreign key for table `{{%mp_users}}`
        $this->addForeignKey(
            '{{%fk-nodes-id_user}}',
            '{{%nodes}}',
            'id_user',
            '{{%mp_users}}',
            'id',
            'CASCADE'
        );
        //add foreign key for table `{{%blockchains}}`
        $this->addForeignKey(
            '{{%fk-nodes-id_blockchain}}',
            '{{%nodes}}',
            'id_blockchain',
            '{{%blockchains}}',
            'id',
            'CASCADE'
        );
        //add foreign key for table `{{%smart_contract}}`
        $this->addForeignKey(
            '{{%fk-nodes-id_smart_contract}}',
            '{{%nodes}}',
            'id_smart_contract',
            '{{%smart_contract}}',
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
            '{{%fk-nodes-id_smart_contract}}',
            '{{%nodes}}'
        );
        // drops foreign key for table `{{%nodes}}`
        $this->dropForeignKey(
            '{{%fk-nodes-id_blockchain}}',
            '{{%nodes}}'
        );
        // drops foreign key for table `{{%nodes}}`
        $this->dropForeignKey(
            '{{%fk-nodes-id_user}}',
            '{{%nodes}}'
        );

        // drops index for column `id_smart_contract`
        $this->dropIndex(
            '{{%idx-nodes-id_smart_contract}}',
            '{{%nodes}}'
        );

        // drops index for column `id_blockchain`
        $this->dropIndex(
            '{{%idx-nodes-id_blockchain}}',
            '{{%nodes}}'
        );

        // drops index for column `id_user`
        $this->dropIndex(
            '{{%idx-nodes-id_user}}',
            '{{%nodes}}'
        );


        $this->dropTable('{{%nodes}}');
    }
}
