<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ethtxs}}`.
 */
class m210628_075300_create_ethtxs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ethtxs}}', [
            'id' => $this->primaryKey(),
            'timestamp' => $this->integer(11)->notNull(),
            'txfrom' => $this->string(100)->notNull(),
            'txto' => $this->string(100)->notNull(),
            'gas' => $this->string(50)->notNull(),
            'gasprice' => $this->string(50)->notNull(),
            'blocknumber' => $this->string(50)->notNull(),
            'txhash' => $this->string(250),
            'value' => $this->float()->notNull(),
            'contract_to' => $this->string(100),
            'contract_value' => $this->float()->notNull(),
        ]);

        // creates index for column `txfrom`
        $this->createIndex(
           '{{%idx-ethtxs-txfrom}}',
           '{{%ethtxs}}',
           'txfrom'
        );

        // creates index for column `txto`
        $this->createIndex(
           '{{%idx-ethtxs-txto}}',
           '{{%ethtxs}}',
           'txto'
        );

        // creates index for column `contract_to`
        $this->createIndex(
           '{{%idx-ethtxs-contract_to}}',
           '{{%ethtxs}}',
           'contract_to'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        // drops index for column `contract_to`
        $this->dropIndex(
            '{{%idx-ethtxs-contract_to}}',
            '{{%ethtxs}}'
        );
        // drops index for column `txto`
        $this->dropIndex(
            '{{%idx-ethtxs-txto}}',
            '{{%ethtxs}}'
        );
        // drops index for column `txfrom`
        $this->dropIndex(
            '{{%idx-ethtxs-txfrom}}',
            '{{%ethtxs}}'
        );
        $this->dropTable('{{%ethtxs}}');
    }
}
