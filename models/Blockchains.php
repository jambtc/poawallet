<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blockchains".
 *
 * @property int $id
 * @property int $id_user
 * @property string $denomination
 * @property string $chain_id
 * @property string $url
 * @property string $symbol
 * @property string|null $url_block_explorer
 *
 * @property Users $user
 * @property Nodes[] $nodes
 */
class Blockchains extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blockchains';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'denomination', 'chain_id', 'url', 'symbol'], 'required'],
            [['id_user'], 'integer'],
            [['denomination', 'url', 'symbol', 'url_block_explorer'], 'string', 'max' => 255],
            [['chain_id'], 'string', 'max' => 50],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_user' => Yii::t('app', 'Id User'),
            'denomination' => Yii::t('app', 'Networks'),
            'chain_id' => Yii::t('app', 'Chain ID'),
            'url' => Yii::t('app', 'Url'),
            'symbol' => Yii::t('app', 'Symbol'),
            'url_block_explorer' => Yii::t('app', 'Url Block Explorer'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\UsersQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'id_user']);
    }

    /**
     * Gets query for [[Nodes]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\NodesQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Nodes::className(), ['id_blockchain' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\BlockchainsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BlockchainsQuery(get_called_class());
    }
}
