<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vapid}}`.
 */
class m210415_085447_create_vapid_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vapid}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->string(255)->notNull(),
            'secret_key' => $this->string(255)->notNull(),
        ]);

        // Igenerate vapid keys from https://d3v.one/vapid-key-generator/
        $this->insert('vapid', [
           'public_key' => 'BOnchKPJ2XM1RoWmXWAhn_C8mtTvnpO94aOFIN1QPdF8NBAV7udLQ89h5BrbCJxb814Cg9CiIbHqxh6on9edl-I',
           'secret_key' => 'HFR-QNlfmBVrKfGd5Iw9kbDy2IGLd_wR304WHxqfDc8',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%vapid}}');
    }
}
