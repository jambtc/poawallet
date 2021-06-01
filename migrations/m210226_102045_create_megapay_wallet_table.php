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
        $this->createTable('{{%mp_wallets}}', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11),
            'wallet_address' => $this->string(50)->notNull(),
            'blocknumber' => $this->string(50)->notNull(),
        ]);

        // creates index for column `id_user`
        $this->createIndex(
            '{{%idx-mp_wallets-id_user}}',
            '{{%mp_wallets}}',
            'id_user'
        );

        //add foreign key for table `{{%mp_wallets}}`
        $this->addForeignKey(
            '{{%fk-megapay_wallet-user_id}}',
            '{{%mp_wallets}}',
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
        // drops foreign key for table `{{%mp_user}}`
        $this->dropForeignKey(
            '{{%fk-megapay_wallet-user_id}}',
            '{{%mp_wallet}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-mp_wallets-id_user}}',
            '{{%mp_wallet}}'
        );

        $this->dropTable('{{%mp_wallets}}');
    }
}
