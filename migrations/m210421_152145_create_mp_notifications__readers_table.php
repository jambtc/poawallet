<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mp_notifications__readers}}`.
 */
class m210421_152145_create_mp_notifications__readers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mp_notifications_readers}}', [
            'id' => $this->primaryKey(),
            'id_notification' => $this->integer(11)->notNull(),
            'id_user' => $this->integer(11)->notNull(),
            'alreadyread' => $this->boolean(0)->notNull(),
        ]);

        // creates index for column `id_notification`
        $this->createIndex(
           '{{%idx-mp_notifications_readers-id_notification}}',
           '{{%mp_notifications_readers}}',
           'id_notification'
        );

        // creates index for column `id_store`
        $this->createIndex(
           '{{%idx-mp_notifications_readers-id_user}}',
           '{{%mp_notifications_readers}}',
           'id_user'
        );

        // add foreign key for table `{{%notifications_readers}}`
        $this->addForeignKey(
            '{{%fk-mp_notifications_readers-id_notification}}',
            '{{%mp_notifications_readers}}',
            'id_notification',
            '{{%mp_notifications}}',
            'id',
            'CASCADE'
        );

        // add foreign key for table `{{%notifications_readers}}`
        $this->addForeignKey(
            '{{%fk-mp_notifications_readers-id_user}}',
            '{{%mp_notifications_readers}}',
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
        $this->dropTable('{{%mp_notifications__readers}}');
    }
}
