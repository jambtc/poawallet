<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "owner".
 *
 * @property int $id
 * @property string $owner
 * @property string $tax_code
 * @property string $address
 * @property string $cap
 * @property string $city
 * @property string $country
 * @property string $phone
 * @property string $email
 * @property string $dpo_officer
 * @property string $dpo_email
 * @property string $dpo_phone
 */
class Owner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'owner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner', 'tax_code', 'address', 'cap', 'city', 'country', 'phone', 'email', 'dpo_officer', 'dpo_email', 'dpo_phone'], 'required'],
            [['owner', 'address', 'city', 'country', 'email', 'dpo_officer', 'dpo_email', 'dpo_phone'], 'string', 'max' => 255],
            [['tax_code', 'phone'], 'string', 'max' => 50],
            [['cap'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'owner' => Yii::t('app', 'Owner'),
            'tax_code' => Yii::t('app', 'Tax Code'),
            'address' => Yii::t('app', 'Address'),
            'cap' => Yii::t('app', 'Cap'),
            'city' => Yii::t('app', 'City'),
            'country' => Yii::t('app', 'Country'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'dpo_officer' => Yii::t('app', 'Dpo Officer'),
            'dpo_email' => Yii::t('app', 'Dpo Email'),
            'dpo_phone' => Yii::t('app', 'Dpo Phone'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\OwnerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\OwnerQuery(get_called_class());
    }
}
