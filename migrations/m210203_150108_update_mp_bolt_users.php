<?php

use yii\db\Migration;

/**
 * Class m210203_150108_update_mp_bolt_users
 */
class m210203_150108_update_mp_bolt_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('mp_users', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'password' => $this->string(255)->notNull(),
            'ga_secret_key' => $this->string(16)->defaultValue(NULL),
            'activation_code' => $this->string(50)->defaultValue(NULL),
            'status_activation_code' => $this->integer(11)->defaultValue(0),
            'oauth_provider' => $this->string(8),
            'oauth_uid' => $this->string(100),
            'authKey' => $this->string(255)->defaultValue(NULL),
            'accessToken' => $this->string(255)->defaultValue(NULL),
            'first_name' => $this->string(255)->defaultValue(NULL),
            'last_name' => $this->string(255)->defaultValue(NULL),
            'email' => $this->string(255)->defaultValue(NULL),
            'picture' => $this->string(255)->defaultValue(NULL),
            'provider' => $this->string(20)->defaultValue(NULL),
            'facade' => $this->string(20)->defaultValue(NULL),

        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('mp_users');

        // return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_150108_update_mp_bolt_users cannot be reverted.\n";

        return false;
    }
    */
}
