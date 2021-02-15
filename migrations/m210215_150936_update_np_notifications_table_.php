<?php

use yii\db\Migration;

/**
 * Class m210215_150936_update_np_notifications_table_
 */
class m210215_150936_update_np_notifications_table_ extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createIndex(
            '{{%idx-notification-id_user}}',
            '{{%np_notifications}}',
            'id_user'
        );

        $this->addForeignKey(
            '{{%fk-notifications-id_user}}',
            '{{%np_notifications}}',
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
            '{{%fk-notification-id_user}}',
            '{{%np_notifications}}'
        );
        $this->dropIndex(
            '{{%idx-notification-id_user}}',
            '{{%np_notifications}}'
        );

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210215_150936_update_np_notifications_table_ cannot be reverted.\n";

        return false;
    }
    */
}
