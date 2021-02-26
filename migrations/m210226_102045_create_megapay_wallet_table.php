<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%megapay_wallet}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%mp_user}}`
 */
class m210226_102045_create_megapay_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mp_wallet}}', [
            'id_wallet' => $this->integer(11),
            'id_user' => $this->integer(11),
            'wallet_address' => $this->string(50)->notNull(),
            'blocknumber' => $this->string(50)->notNull(),
        ]);

        $this->addPrimaryKey('PK-megapay_wallet_id_wallet','{{%mp_wallet}}','id_wallet');
        $this->execute('ALTER TABLE `mp_wallet` CHANGE `id_wallet` `id_wallet` INT(11) NOT NULL AUTO_INCREMENT');


        // add foreign key for table `{{%mp_user}}`
        // $this->addForeignKey(
        //     '{{%fk-megapay_wallet-user_id}}',
        //     '{{%mp_wallet}}',
        //     'id_user',
        //     '{{%mp_users}}',
        //     'id',
        //     'CASCADE'
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%mp_user}}`
        // $this->dropForeignKey(
        //     '{{%fk-megapay_wallet-user_id}}',
        //     '{{%mp_wallet}}'
        // );

        // // drops index for column `user_id`
        // $this->dropIndex(
        //     '{{%idx-megapay_wallet-user_id}}',
        //     '{{%mp_wallet}}'
        // );

        $this->dropTable('{{%mp_wallet}}');
    }
}
