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
            'id_users_type' => Yii::t('model', 'Id Users Type'),
            'desc' => Yii::t('model', 'Desc'),
            'status' => Yii::t('model', 'Status'),
            'note' => Yii::t('model', 'Note'),
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
