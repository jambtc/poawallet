<?php
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m210721_094411_add_dec_blocknumber_to_ethtxs_table
 */
class m210721_094411_add_dec_blocknumber_to_ethtxs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ethtxs', 'dec_blocknumber', $this->integer(11)->after('blocknumber'));

        // creates index for column `txfrom`
        $this->createIndex(
           '{{%idx-ethtxs-dec_blocknumber}}',
           '{{%ethtxs}}',
           'dec_blocknumber'
        );

        // update status field for all users
        foreach((new Query)->from('ethtxs')->each() as $row) {
            $this->update('ethtxs', ['dec_blocknumber' => hexdec($row['blocknumber'])], ['id' => $row['id']]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `dec_blocknumber`
        $this->dropIndex(
            '{{%idx-ethtxs-dec_blocknumber}}',
            '{{%ethtxs}}'
        );

        // drops index for column `dec_blocknumber`
        $this->dropColumn(
            '{{%dec_blocknumber}}',
            '{{%ethtxs}}'
        );

    }
}
