<?php

use yii\db\Migration;

/**
 * Class m210226_104057_create_table_mp_vapid_subscription
 */
class m210226_104057_create_table_mp_vapid_subscription extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mp_subscriptions}}', [
            'id_subscription' => $this->integer(11),
            'id_user' => $this->integer(11),
            'type' => $this->string(20)->notNull(),
            'browser' => $this->string(1000)->notNull(),
            'endpoint' => $this->string(1000)->notNull(),
            'auth' => $this->string(1000)->notNull(),
            'p256dh' => $this->string(1000)->notNull(),
        ]);

        $this->addPrimaryKey('PK-megapay_vapid_id_subscription','{{%mp_subscriptions}}','id_subscription');
        $this->execute('ALTER TABLE `mp_subscriptions` CHANGE `id_subscription` `id_subscription` INT(11) NOT NULL AUTO_INCREMENT');

        // add foreign key for table `{{%mp_user}}`
        // $this->addForeignKey(
        //     '{{%fk-megapay_vapid_id_subscription-id_subscription}}',
        //     '{{%mp_subscriptions}}',
        //     'id_subscription',
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
        //     '{{%fk-megapay_vapid_id_subscription-id_subscription}}',
        //     '{{%mp_subscriptions}}'
        // );

        // drops index for column `user_id`
        // $this->dropIndex(
        //     '{{%idx-megapay_wallet-user_id}}',
        //     '{{%mp_wallet}}'
        // );

        $this->dropTable('{{%mp_subscriptions}}');

        // return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_104057_create_table_mp_vapid_subscription cannot be reverted.\n";

        return false;
    }
    */
}
