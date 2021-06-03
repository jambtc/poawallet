<?php

use yii\db\Migration;

/**
 * Class m210603_070044_insert_rows_blockchains_table
 */
class m210603_070044_insert_rows_blockchains_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // INSERT BINANCE SMART CHAIN DATA
        $this->insert('blockchains', [
           'denomination' => 'BSC',
           'chain_id' => '56',
           'url' => 'https://bsc-dataseed.binance.org/',
           'symbol' => 'BNB',
           'url_block_explorer' => 'https://bscscan.com',
        ]);

        // INSERT CZN POA BLOCKCHAIN DATA
        $this->insert('blockchains', [
           'denomination' => 'POA Cat`s Owner',
           'chain_id' => '1337',
           'url' => 'https://poa-node.catsowner.tk/',
           'symbol' => 'CZN',
           'url_block_explorer' => 'https://explorer.catsowner.tk',
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('blockchains', ['id' => 2]);
        $this->delete('blockchains', ['id' => 1]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210603_070044_insert_rows_blockchains_table cannot be reverted.\n";

        return false;
    }
    */
}
