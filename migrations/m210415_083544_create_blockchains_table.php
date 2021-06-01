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
            'denomination' => $this->string(255)->notNull(),
            'chain_id' => $this->string(50)->notNull(),
            'url' => $this->string(255)->notNull(),
            'symbol' => $this->string(255)->notNull(),
            'url_block_explorer' => $this->string(255),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%blockchains}}');
    }
}
