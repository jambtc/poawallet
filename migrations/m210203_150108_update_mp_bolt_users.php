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

        $this->execute('INSERT INTO `mp_users`
          (`id`, `username`, `password`, `ga_secret_key`, `activation_code`, `status_activation_code`,
            `oauth_provider`, `oauth_uid`, `email`, `provider`, `facade`)
            SELECT  b.id_user, b.email,b.password,b.ga_secret_key,b.activation_code,b.status_activation_code,
            b.oauth_provider,b.oauth_uid,b.email,b.oauth_provider, "dashboard"
            FROM `bolt_users` AS b WHERE 1 ');

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
