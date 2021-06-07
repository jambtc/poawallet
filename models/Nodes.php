<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nodes".
 *
 * @property int $id
 * @property int $id_user
 * @property int $id_blockchain
 * @property int $id_smart_contract
 *
 * @property Blockchains $blockchain
 * @property SmartContracts $smartContract
 * @property Users $user
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
            [['id_user', 'id_blockchain', 'id_smart_contract'], 'required'],
            [['id_user', 'id_blockchain', 'id_smart_contract'], 'integer'],
            [['id_blockchain'], 'exist', 'skipOnError' => true, 'targetClass' => Blockchains::className(), 'targetAttribute' => ['id_blockchain' => 'id']],
            [['id_smart_contract'], 'exist', 'skipOnError' => true, 'targetClass' => SmartContracts::className(), 'targetAttribute' => ['id_smart_contract' => 'id']],
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
            'id_blockchain' => Yii::t('app', 'Blockchain'),
            'id_smart_contract' => Yii::t('app', 'Smart Contract'),
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
     * Gets query for [[SmartContract]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\SmartContractQuery
     */
    public function getSmartContract()
    {
        return $this->hasOne(SmartContracts::className(), ['id' => 'id_smart_contract']);
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
     * {@inheritdoc}
     * @return \app\models\query\NodesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\NodesQuery(get_called_class());
    }
}
