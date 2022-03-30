<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%standard_smart_contract_values}}`.
 */
class m210607_140150_create_standard_smart_contract_values_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%standard_smart_contract_values}}', [
            'id' => $this->primaryKey(),
            'id_blockchain' => $this->integer(11)->notNull(),
            'id_contract_type' => $this->integer(11)->notNull(),
            'denomination' => $this->string(255)->notNull(),
            'smart_contract_address' => $this->string(255)->notNull(),
            'decimals' => $this->integer(11)->notNull(),
            'symbol' => $this->string(255)->notNull(),
        ]);

        // INSERT BINANCE SMART CHAIN DATA
        // $this->insert('standard_smart_contract_values', [
        //     'id_blockchain' => 1,
        //     'id_contract_type' => 2,
        //    'denomination' => 'Cat`s owner (on BSC)',
        //    'smart_contract_address' => Yii::$app->params['smartcontract_address'],
        //    'decimals' => '9',
        //    'symbol' => 'CZN',
        // ]);

        // INSERT CZN POA BLOCKCHAIN DATA
        $this->insert('standard_smart_contract_values', [
            'id_blockchain' => 1,
            'id_contract_type' => 1,
           'denomination' => 'Cat`s Owner (on POA)',
           'smart_contract_address' => '0x3A95470a698449d73Aa3608312D1E30a8d9E457C',
           'decimals' => '9',
           'symbol' => 'CZN',
        ]);

        // INSERT FIDELITY SMART CONTRACT
        $this->insert('standard_smart_contract_values',[
            'id_blockchain' => 2,
            'id_contract_type' => 1,
            'denomination' => 'Fidelity Token (on POA)',
            'smart_contract_address' => '0xb5B49A84664B52b808E9FDe9096fc6BEd94F8D91',
            'decimals' => '2',
            'symbol' => 'FIDES',
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%standard_smart_contract_values}}');
    }
}
