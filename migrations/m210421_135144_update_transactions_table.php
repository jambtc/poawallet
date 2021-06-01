<?php

use yii\db\Migration;

/**
 * Class m210421_135144_update_transactions_table
 */
class m210421_135144_update_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        // creates index for column `id_user`
        $this->createIndex(
            '{{%idx-transactions-id_user}}',
            '{{%transactions}}',
            'id_user'
        );

        // add foreign key for table `{{%transactions}}`
        $this->addForeignKey(
            '{{%fk-transactions-id_user}}',
            '{{%transactions}}',
            'id_user',
            '{{%mp_users}}',
            'id',
            'CASCADE'
        );



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-transactions-id_users}}',
            '{{%transactions}}'
        );
        $this->dropIndex(
            '{{%idx-transactions-id_users}}',
            '{{%transactions}}'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210421_135144_update_transactions_table cannot be reverted.\n";

        return false;
    }
    */
}
