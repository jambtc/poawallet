<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%index_push_subscriptions}}`.
 */
class m210215_145436_create_index_push_subscriptions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createIndex(
            '{{%idx-push-id_user}}',
            '{{%bolt_vapid_subscription}}',
            'id_user'
        );

        $this->addForeignKey(
            '{{%fk-push-id_user}}',
            '{{%bolt_vapid_subscription}}',
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
            '{{%fk-push-id_user}}',
            '{{%bolt_vapid_subscription}}'
        );
        $this->dropIndex(
            '{{%idx-push-id_user}}',
            '{{%bolt_vapid_subscription}}'
        );
    }
}
