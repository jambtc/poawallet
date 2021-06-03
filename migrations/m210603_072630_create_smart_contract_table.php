<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%smart_contract}}`.
 */
class m210603_072630_create_smart_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%smart_contract}}', [
            'id' => $this->primaryKey(),
            'denomination' => $this->string(255)->notNull(),
            'smart_contract_address' => $this->string(255)->notNull(),
            'decimals' => $this->integer(11)->notNull(),
            'symbol' => $this->string(255)->notNull(),
        ]);

        // INSERT BINANCE SMART CHAIN DATA
        $this->insert('smart_contract', [
           'denomination' => 'Cat`s owner (on BSC)',
           'smart_contract_address' => Yii::$app->params['smartcontract_address'],
           'decimals' => '9',
           'symbol' => 'CZN',
        ]);

        // INSERT CZN POA BLOCKCHAIN DATA
        $this->insert('smart_contract', [
           'denomination' => 'Cat`s Owner (on POA)',
           'smart_contract_address' => '0x3A95470a698449d73Aa3608312D1E30a8d9E457C',
           'decimals' => '9',
           'symbol' => 'CZN',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%smart_contract}}');
    }
}
