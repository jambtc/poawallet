<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%smart_contract}}`.
 */
class m210603_072630_create_smart_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%smart_contract}}', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->notNull(),
            'id_contract_type' => $this->integer(11)->notNull(),
            'denomination' => $this->string(255)->notNull(),
            'smart_contract_address' => $this->string(255)->notNull(),
            'decimals' => $this->integer(11)->notNull(),
            'symbol' => $this->string(255)->notNull(),
        ]);

        // creates index for column `id_user`
        $this->createIndex(
            '{{%idx-smart_contract-id_user}}',
            '{{%smart_contract}}',
            'id_user'
        );

        // creates index for column `id_contract_type`
        $this->createIndex(
            '{{%idx-smart_contract-id_contract_type}}',
            '{{%smart_contract}}',
            'id_contract_type'
        );

        //add foreign key for table `{{%smart_contract}}`
        $this->addForeignKey(
            '{{%fk-smart_contract-id_user}}',
            '{{%smart_contract}}',
            'id_user',
            '{{%mp_users}}',
            'id',
            'CASCADE'
        );

        //add foreign key for table `{{%smart_contract}}`
        $this->addForeignKey(
            '{{%fk-smart_contract-id_contract_type}}',
            '{{%smart_contract}}',
            'id_contract_type',
            '{{%contract_type}}',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%smart_contract}}`
        $this->dropForeignKey(
            '{{%fk-smart_contract-id_user}}',
            '{{%smart_contract}}'
        );
        // drops foreign key for table `{{%smart_contract}}`
        $this->dropForeignKey(
            '{{%fk-smart_contract-id_contract_type}}',
            '{{%smart_contract}}'
        );
        // drops index for column `id_contract_type`
        $this->dropIndex(
            '{{%idx-smart_contract-id_contract_type}}',
            '{{%smart_contract}}'
        );
        // drops index for column `id_user`
        $this->dropIndex(
            '{{%idx-smart_contract-id_user}}',
            '{{%smart_contract}}'
        );
        $this->dropTable('{{%smart_contract}}');
    }
}
