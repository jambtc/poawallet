<?php

use yii\db\Migration;

/**
 * Class m210614_143537_add_column_transactions_table
 */
class m210614_143537_add_column_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('transactions', 'id_smart_contract', $this->integer(11)->after('type'));

        // creates index for column `id_smart_contract`
        $this->createIndex(
           '{{%idx-transactions-id_smart_contract}}',
           '{{%transactions}}',
           'id_smart_contract'
        );

        // add foreign key for table `{{%transactions}}`
        $this->addForeignKey(
            '{{%fk-transactions-id_smart_contract}}',
            '{{%transactions}}',
            'id_smart_contract',
            '{{%smart_contract}}',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210614_143537_add_column_transactions_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210614_143537_add_column_transactions_table cannot be reverted.\n";

        return false;
    }
    */
}
