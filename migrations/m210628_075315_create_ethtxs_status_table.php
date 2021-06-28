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
            'id_blockchain' => $this->integer(11)->notNull(),
            'blocknumber' => $this->string(50)->notNull(),
        ]);

        // creates index for column `id_blockchain`
        $this->createIndex(
            '{{%idx-ethtxs_status-id_blockchain}}',
            '{{%ethtxs_status}}',
            'id_blockchain'
        );

        //add foreign key for table `{{%ethtxs_status}}`
        $this->addForeignKey(
            '{{%fk-ethtxs_status-id_blockchain}}',
            '{{%ethtxs_status}}',
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
        // drops foreign key for table `{{%ethtxs_status}}`
        $this->dropForeignKey(
            '{{%fk-ethtxs_status-id_blockchain}}',
            '{{%ethtxs_status}}'
        );
        // drops index for column `id_blockchain`
        $this->dropIndex(
            '{{%idx-ethtxs_status-id_blockchain}}',
            '{{%ethtxs_status}}'
        );
        $this->dropTable('{{%ethtxs_status}}');
    }
}
