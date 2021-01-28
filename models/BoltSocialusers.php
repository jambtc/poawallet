<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bolt_socialusers".
 *
 * @property int $id_social
 * @property string $oauth_provider
 * @property string $oauth_uid
 * @property int $id_user
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string $picture
 */
class BoltSocialusers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bolt_socialusers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['oauth_provider', 'oauth_uid', 'id_user', 'first_name', 'last_name', 'username', 'email', 'picture'], 'required'],
            [['id_user'], 'integer'],
            [['oauth_provider'], 'string', 'max' => 8],
            [['oauth_uid', 'email'], 'string', 'max' => 100],
            [['first_name', 'last_name', 'username'], 'string', 'max' => 50],
            [['picture'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_social' => Yii::t('app', 'Id Social'),
            'oauth_provider' => Yii::t('app', 'Oauth Provider'),
            'oauth_uid' => Yii::t('app', 'Oauth Uid'),
            'id_user' => Yii::t('app', 'Id User'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'picture' => Yii::t('app', 'Picture'),
        ];
    }
}
