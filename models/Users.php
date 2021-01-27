<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "np_users".
 *
 * @property int $id_user
 * @property int $id_users_type
 * @property string $email
 * @property string $password
 * @property string|null $ga_secret_key
 * @property string $name
 * @property string $surname
 * @property string|null $corporate
 * @property string|null $denomination
 * @property string $vat
 * @property string $address
 * @property string $cap
 * @property string $city
 * @property string $country
 * @property string $activation_code
 * @property int $status_activation_code
 *
 * @property NpApi[] $npApis
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'np_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_users_type', 'email', 'password', 'name', 'surname', 'vat', 'address', 'cap', 'city', 'country', 'activation_code', 'status_activation_code'], 'required'],
            [['id_users_type', 'status_activation_code'], 'integer'],
            [['email', 'password', 'name', 'surname'], 'string', 'max' => 255],
            [['ga_secret_key', 'corporate'], 'string', 'max' => 16],
            [['denomination', 'vat', 'address', 'cap', 'city', 'country'], 'string', 'max' => 250],
            [['activation_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => Yii::t('model', 'Id User'),
            'id_users_type' => Yii::t('model', 'Id Users Type'),
            'email' => Yii::t('model', 'Email'),
            'password' => Yii::t('model', 'Password'),
            'ga_secret_key' => Yii::t('model', 'Ga Secret Key'),
            'name' => Yii::t('model', 'Name'),
            'surname' => Yii::t('model', 'Surname'),
            'corporate' => Yii::t('model', 'Corporate'),
            'denomination' => Yii::t('model', 'Denomination'),
            'vat' => Yii::t('model', 'Vat'),
            'address' => Yii::t('model', 'Address'),
            'cap' => Yii::t('model', 'Cap'),
            'city' => Yii::t('model', 'City'),
            'country' => Yii::t('model', 'Country'),
            'activation_code' => Yii::t('model', 'Activation Code'),
            'status_activation_code' => Yii::t('model', 'Status Activation Code'),
        ];
    }

    /**
     * Gets query for [[NpApis]].
     *
     * @return \yii\db\ActiveQuery|NpApiQuery
     */
    public function getNpApis()
    {
        return $this->hasMany(NpApi::className(), ['id_user' => 'id_user']);
    }

    /**
     * {@inheritdoc}
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }
}
