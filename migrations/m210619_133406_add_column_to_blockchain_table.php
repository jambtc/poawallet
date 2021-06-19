<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%blockchain}}`.
 */
class m210619_133406_add_column_to_blockchain_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'blockchains',
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
        $this->dropColumn('blockchains', 'zerogas');
    }
}
