<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mp_notifications}}`.
 */
class m210421_152136_create_mp_notifications_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mp_notifications}}', [
            'id' => $this->primaryKey(),
            'timestamp' => $this->integer(11)->notNull(),
            'type' => $this->string(50)->notNull(),
            'status' => $this->string(50)->notNull(),
            'description' => $this->text()->notNull(),
            'url' => $this->text(500)->notNull(),
            'price' => $this->float()->notNull(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mp_notifications}}');
    }
}
