<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "np_nodes".
 *
 * @property int $id_node
 * @property string $url
 * @property string $port
 */
class Nodes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'np_nodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'port'], 'required'],
            [['url'], 'string', 'max' => 100],
            [['port'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_node' => Yii::t('app', 'Id Node'),
            'url' => Yii::t('app', 'Url'),
            'port' => Yii::t('app', 'Port'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return NodesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\NodesQuery(get_called_class());
    }
}
