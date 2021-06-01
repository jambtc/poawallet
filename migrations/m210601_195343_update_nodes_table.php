<?php

use yii\db\Migration;

/**
 * Class m210601_195343_update_nodes_table
 */
class m210601_195343_update_nodes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('nodes', 'id_user', $this->integer(11)->after('id')->notNull());
        // creates index for column `id_user`
        $this->createIndex(
           '{{%idx-nodes-id_user}}',
           '{{%nodes}}',
           'id_user'
        );

        // add foreign key for table `{{%nodes}}`
        $this->addForeignKey(
            '{{%fk-nodes-id_user}}',
            '{{%nodes}}',
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
        echo "m210601_195343_update_nodes_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210601_195343_update_nodes_table cannot be reverted.\n";

        return false;
    }
    */
}
