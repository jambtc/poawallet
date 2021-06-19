<?php

use yii\db\Migration;

/**
 * Class m210619_134045_update_all_blockchains_table_with_correct_values
 */
class m210619_134045_update_all_blockchains_table_with_correct_values extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('standard_blockchain_values', ['zerogas' => 1], ['chain_id' => 1337]);
        $this->update('standard_blockchain_values', ['zerogas' => 1], ['chain_id' => 2018]);
        $this->update('blockchains', ['zerogas' => 1], ['chain_id' => 1337]);
        $this->update('blockchains', ['zerogas' => 1], ['chain_id' => 2018]);


        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210619_134045_update_all_blockchains_table_with_correct_values cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210619_134045_update_all_blockchains_table_with_correct_values cannot be reverted.\n";

        return false;
    }
    */
}
