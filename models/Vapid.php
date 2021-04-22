<?php

namespace app\models;

use Yii;
use app\components\WebApp;

/**
 * This is the model class for table "vapid".
 *
 * @property int $id
 * @property string $public_key
 * @property string $secret_key
 */
class Vapid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vapid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key', 'secret_key'], 'required'],
            [['public_key', 'secret_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'public_key' => Yii::t('app', 'Public Key'),
            'secret_key' => Yii::t('app', 'Secret Key'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\VapidQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\VapidQuery(get_called_class());
    }

    public function beforeSave($insert) {
        $this->secret_key = WebApp::encrypt($this->secret_key);

        return parent::beforeSave($insert);
    }
}
