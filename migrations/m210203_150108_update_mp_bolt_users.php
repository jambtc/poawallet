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
        $this->addColumn('mp_users', 'first_name', $this->string(255)->after('accessToken'));
        $this->addColumn('mp_users', 'last_name', $this->string(255)->after('accessToken'));
        $this->addColumn('mp_users', 'email', $this->string(255)->after('accessToken'));
        $this->addColumn('mp_users', 'picture', $this->string(255)->after('accessToken'));
        $this->addColumn('mp_users', 'provider', $this->string(20)->after('accessToken'));
        $this->addColumn('mp_users', 'facade', $this->string(20)->after('accessToken'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_150108_update_mp_bolt_users cannot be reverted.\n";

        return false;
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
