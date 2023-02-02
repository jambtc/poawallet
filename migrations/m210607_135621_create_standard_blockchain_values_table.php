<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%standard_blockchain_values}}`.
 */
class m210607_135621_create_standard_blockchain_values_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%standard_blockchain_values}}', [
            'id' => $this->primaryKey(),
            'denomination' => $this->string(255)->notNull(),
            'chain_id' => $this->string(50)->notNull(),
            'url' => $this->string(255)->notNull(),
            'symbol' => $this->string(255)->notNull(),
            'url_block_explorer' => $this->string(255),
        ]);

        // INSERT BINANCE SMART CHAIN DATA
        // $this->insert('standard_blockchain_values', [
        //    'denomination' => 'BSC - Smart Chain',
        //    'chain_id' => '56',
        //    'url' => 'https://bsc-dataseed.binance.org/',
        //    'symbol' => 'BNB',
        //    'url_block_explorer' => 'https://bscscan.com',
        // ]);

        // // INSERT BINANCE TEST CHAIN DATA
        // $this->insert('standard_blockchain_values', [
        //    'denomination' => 'Smart Chain - Testnet',
        //    'chain_id' => '97',
        //    'url' => 'https://data-seed-prebsc-1-s1.binance.org:8545/',
        //    'symbol' => 'BNB',
        //    'url_block_explorer' => 'https://testnet.bscscan.com',
        // ]);

        // INSERT CZN POA BLOCKCHAIN DATA
        // $this->insert('standard_blockchain_values', [
        //    'denomination' => 'POA Cat`s Owner',
        //    'chain_id' => '1337',
        //    'url' => 'https://poa-node.catsowner.tk/',
        //    'symbol' => 'CZN',
        //    'url_block_explorer' => 'https://explorer.catsowner.tk',
        // ]);

        // INSERT FIDELITY POA BLOCKCHAIN DATA
        $this->insert('standard_blockchain_values', [
           'denomination' => 'Fidelity POA',
           'chain_id' => '2118',
           'url' => 'https://poa.fid3lize.tk/',
           'symbol' => 'FIDGAS',
           'url_block_explorer' => 'https://blockscout.fid3lize.tk',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%standard_blockchain_values}}');
    }
}
