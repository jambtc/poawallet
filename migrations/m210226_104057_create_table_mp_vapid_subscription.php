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
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11),
            'type' => $this->string(20)->notNull(),
            'browser' => $this->string(1000)->notNull(),
            'endpoint' => $this->string(1000)->notNull(),
            'auth' => $this->string(1000)->notNull(),
            'p256dh' => $this->string(1000)->notNull(),
        ]);


        //add foreign key for table `{{%mp_subscriptions}}`
        $this->addForeignKey(
            '{{%fk-mp_subscriptions-id_user}}',
            '{{%mp_subscriptions}}',
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
            '{{%fk-mp_subscriptions-id_subsid_usercription}}',
            '{{%mp_subscriptions}}'
        );

        $this->dropTable('{{%mp_subscriptions}}');

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
