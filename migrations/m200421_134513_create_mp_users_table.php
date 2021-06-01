<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mp_users}}`.
 */
class m200421_134513_create_mp_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mp_users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'password' => $this->string(255)->notNull(),
            'activation_code' => $this->string(60)->defaultValue(NULL),
            'status_activation_code' => $this->integer(11)->defaultValue(0),
            'oauth_provider' => $this->string(20)->notNull(),
            'oauth_uid' => $this->string(100),
            'authKey' => $this->string(255)->defaultValue(NULL),
            'accessToken' => $this->string(255)->defaultValue(NULL),
            'first_name' => $this->string(255)->defaultValue(NULL),
            'last_name' => $this->string(255)->defaultValue(NULL),
            'email' => $this->string(255)->defaultValue(NULL),
            'picture' => $this->string(255)->defaultValue(NULL),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mp_users}}');
    }
}
