<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%blockchain_default}}`.
 */
class m210619_133355_add_column_to_blockchain_default_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'standard_blockchain_values',
            'zerogas',
            $this->integer(11)->after('url_block_explorer')
                ->defaultValue(0)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('standard_blockchain_values', 'zerogas');
    }
}
