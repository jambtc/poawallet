<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nodes".
 *
 * @property int $id
 * @property string $url
 * @property string $port
 * @property int|null $id_blockchain
 */
class Nodes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url', 'port'], 'required'],
            [['id_blockchain'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['port'], 'string', 'max' => 50],
            [['id_blockchain'], 'exist', 'skipOnError' => true, 'targetClass' => Blockchains::className(), 'targetAttribute' => ['id_blockchain' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'port' => Yii::t('app', 'Port'),
            'id_blockchain' => Yii::t('app', 'Id Blockchain'),
        ];
    }

    /**
     * Gets query for [[Blockchain]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\BlockchainsQuery
     */
    public function getBlockchain()
    {
        return $this->hasOne(Blockchains::className(), ['id' => 'id_blockchain']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\NodesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\NodesQuery(get_called_class());
    }
}
