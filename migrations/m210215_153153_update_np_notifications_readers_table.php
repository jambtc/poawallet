<?php

use yii\db\Migration;

/**
 * Class m210215_153153_update_np_notifications_readers_table
 */
class m210215_153153_update_np_notifications_readers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            '{{%idx-notificationreaders-id_user}}',
            '{{%np_notifications_readers}}',
            'id_user'
        );

        $this->addForeignKey(
            '{{%fk-notificationreaders-id_user}}',
            '{{%np_notifications_readers}}',
            'id_user',
            '{{%mp_users}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-notificationreaders-id_notification}}',
            '{{%np_notifications_readers}}',
            'id_notification'
        );

        $this->addForeignKey(
            '{{%fk-notificationreaders-id_notification}}',
            '{{%np_notifications_readers}}',
            'id_notification',
            '{{%np_notifications}}',
            'id_notification',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-notificationreaders-id_user}}',
            '{{%np_notifications_readers}}'
        );

        $this->dropForeignKey(
            '{{%fk-notificationreaders-id_user}}',
            '{{%np_notifications_readers}}'
        );

        $this->dropIndex(
            '{{%idx-notificationreaders-id_notification}}',
            '{{%np_notifications_readers}}'
        );

        $this->dropForeignKey(
            '{{%fk-notificationreaders-id_notification}}',
            '{{%np_notifications_readers}}'
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
        echo "m210215_153153_update_np_notifications_readers_table cannot be reverted.\n";

        return false;
    }
    */
}
