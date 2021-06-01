<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%nodes}}`.
 */
class m210415_084338_create_nodes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%nodes}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%nodes}}');
    }
}
