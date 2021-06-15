<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transactions}}`.
 */
class m210421_115634_create_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'type' => $this->string(20)->notNull(),
            'status' => $this->string(20)->notNull(),
            'token_price' => $this->float()->notNull(),
            'token_received' => $this->float()->notNull(),
            'invoice_timestamp' => $this->integer(11)->notNull(),
            'expiration_timestamp' => $this->integer(11)->notNull(),
            'from_address' => $this->string(100)->notNull(),
            'to_address' => $this->string(100)->notNull(),
            'blocknumber' => $this->string(50)->notNull(),
            'txhash' => $this->string(250),
            'message' => $this->string(1000),
        ]);

        


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transactions}}');
    }
}
