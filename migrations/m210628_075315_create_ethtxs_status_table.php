<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ethtxs_status}}`.
 */
class m210628_075315_create_ethtxs_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ethtxs_status}}', [
            'id' => $this->primaryKey(),
            'symbol' => $this->string(255)->notNull(),
            'blocknumber' => $this->string(50)->notNull(),
        ]);

        // creates index for column `symbol`
        $this->createIndex(
            '{{%idx-ethtxs_status-symbol}}',
            '{{%ethtxs_status}}',
            'symbol'
        );


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `id_blockchain`
        $this->dropIndex(
            '{{%idx-ethtxs_status-id_blockchain}}',
            '{{%ethtxs_status}}'
        );
        $this->dropTable('{{%ethtxs_status}}');
    }
}
