<?php

use yii\db\Migration;

/**
 * Class m210206_202045_add_memo_field_to_transaction
 */
class m210206_202045_add_memo_field_to_transaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bolt_tokens', 'memo', $this->string(500)->after('txhash'));
        $this->execute('UPDATE `bolt_tokens` as t
                INNER JOIN bolt_tokens_memo m ON t.`id_token` = m.`id_token`
                SET t.`memo`= m.`memo` ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('bolt_tokens', 'memo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210206_202045_add_memo_field_to_transaction cannot be reverted.\n";

        return false;
    }
    */
}
