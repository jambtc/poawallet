<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "np_settings_webapp".
 *
 * @property int $id_setting
 * @property string $setting_name
 * @property string $setting_value
 */
class SettingsWebapp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'np_settings_webapp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['setting_name', 'setting_value'], 'required'],
            [['setting_name'], 'string', 'max' => 50],
            [['setting_value'], 'string', 'max' => 15000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_setting' => Yii::t('app', 'Id Setting'),
            'setting_name' => Yii::t('app', 'Setting Name'),
            'setting_value' => Yii::t('app', 'Setting Value'),
        ];
    }
}
