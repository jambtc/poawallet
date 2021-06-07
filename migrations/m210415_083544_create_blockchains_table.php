<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%blockchains}}`.
 */
class m210415_083544_create_blockchains_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%blockchains}}', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'denomination' => $this->string(255)->notNull(),
            'chain_id' => $this->string(50)->notNull(),
            'url' => $this->string(255)->notNull(),
            'symbol' => $this->string(255)->notNull(),
            'url_block_explorer' => $this->string(255),
        ]);

        // creates index for column `id_user`
        $this->createIndex(
            '{{%idx-blockchains-id_user}}',
            '{{%blockchains}}',
            'id_user'
        );

        //add foreign key for table `{{%blockchains}}`
        $this->addForeignKey(
            '{{%fk-blockchains-id_user}}',
            '{{%blockchains}}',
            'id_user',
            '{{%mp_users}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%blockchains}}`
        $this->dropForeignKey(
            '{{%fk-blockchains-id_user}}',
            '{{%blockchains}}'
        );

        // drops index for column `id_user`
        $this->dropIndex(
            '{{%idx-blockchains-id_user}}',
            '{{%blockchains}}'
        );
        $this->dropTable('{{%blockchains}}');
    }
}
