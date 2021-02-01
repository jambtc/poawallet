<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "np_settings_user".
 *
 * @property int $id_setting
 * @property int $id_user
 * @property string $setting_name
 * @property string $setting_value
 */
class SettingsUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'np_settings_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'setting_name', 'setting_value'], 'required'],
            [['id_user'], 'integer'],
            [['setting_name'], 'string', 'max' => 50],
            [['setting_value'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_setting' => Yii::t('app', 'Id Setting'),
            'id_user' => Yii::t('app', 'Id User'),
            'setting_name' => Yii::t('app', 'Setting Name'),
            'setting_value' => Yii::t('app', 'Setting Value'),
        ];
    }
}
