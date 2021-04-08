<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\Users',
                'filter' => ['status_activation_code' => Users::STATUS_ACTIVE],
                'message' => Yii::t('app','There is no user with this email address.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = Users::findOne([
            'status_activation_code' => Users::STATUS_ACTIVE,
            'username' => $this->email,
            'oauth_provider' => 'mail'
        ]);

        if (!$user) {
            return false;
        }

        if (!Users::isPasswordResetTokenValid($user->activation_code)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }
}
