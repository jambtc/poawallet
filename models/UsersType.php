<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "np_users_type".
 *
 * @property int $id_users_type
 * @property string|null $desc
 * @property string $status
 * @property string $note
 */
class UsersType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'np_users_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'required'],
            [['desc', 'status'], 'string', 'max' => 50],
            [['note'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_users_type' => Yii::t('app', 'Id Users Type'),
            'desc' => Yii::t('app', 'Desc'),
            'status' => Yii::t('app', 'Status'),
            'note' => Yii::t('app', 'Note'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return UsersTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersTypeQuery(get_called_class());
    }
}
