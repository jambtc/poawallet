<?php
namespace app\components;

use app\models\Auth;
use app\models\Users;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $attributes = $this->client->getUserAttributes();

        switch ($this->client->name) {
            case 'facebook':
                $idAttr = 'id';
                $emailAttr = 'email';
                $usernameAttr = 'email';
                $first_nameAttr = 'first_name';
                $last_nameAttr = 'last_name';
                $pictureAttr = 'id';
                break;

            case 'google':
                $idAttr = 'id';
                $emailAttr = 'email';
                $usernameAttr = 'email';
                $first_nameAttr = 'given_name';
                $last_nameAttr = 'family_name';
                $pictureAttr = 'picture';
                break;



        }
        // echo '<pre>'.print_r($this->client,true);
        // echo '<pre>'.print_r($attributes,true);exit;
        $id = ArrayHelper::getValue($attributes, $idAttr);
        $email = ArrayHelper::getValue($attributes, $emailAttr);
        $username = ArrayHelper::getValue($attributes, $usernameAttr);
        $first_name = ArrayHelper::getValue($attributes, $first_nameAttr);
        $last_name = ArrayHelper::getValue($attributes, $last_nameAttr);
        $oauth_provider = $this->client->name;

        if ($this->client->name == 'facebook'){
            $picture = 'https://graph.facebook.com/'. $pictureAttr .'/picture';
        } else {
            $picture = ArrayHelper::getValue($attributes, $pictureAttr);
        }


        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (empty($email)){
            Yii::$app->getSession()->setFlash('error',
                Yii::t('app', 'Unable to login the user. No email address provided by {client}', [
                    'client' => $this->client->getTitle(),
                ])
            );
            return false;
        }

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                /* @var User $user */
                $user = $auth->user;
                if ($this->updateUserInfo($user)){
                    Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                } else {
                    Yii::$app->getSession()->setFlash('error',
                        Yii::t('app','The user was blocked by administrator.')
                    );
                }
            } else { // signup
                $existingUser = Users::find()
                    ->andWhere(['email' => $email])
                    ->andWhere(['oauth_provider' => $this->client->name])
                    ->one();

                if ($existingUser) {
                    // echo '<pre>'.print_r($existingUser,true);exit;
                    // Yii::$app->getSession()->setFlash('error', [
                    //     Yii::t('app', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $this->client->getTitle()]),
                    // ]);
                    $auth = new Auth([
                        'user_id' => $existingUser->id,
                        'source' => $this->client->getId(),
                        'source_id' => (string) $id,
                    ]);
                    if ($this->updateUserInfo($existingUser) && $auth->save()){
                        Yii::$app->user->login($existingUser, Yii::$app->params['user.rememberMeDuration']);
                    } else {
                        Yii::$app->getSession()->setFlash('error', [
                            Yii::t('app', 'Unable to save {client} account: {errors}', [
                                'client' => $this->client->getTitle(),
                                'errors' => json_encode($auth->getErrors()),
                            ]),
                        ]);
                    }
                } else {
                    $password = Yii::$app->security->generateRandomString(60);
                    $user = new Users([
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'ga_secret_key' => null,
                        'activation_code' => Yii::$app->security->generateRandomString(50), // daportare a 60
                        'status_activation_code' => Users::STATUS_ACTIVE,
                        'oauth_provider' => $oauth_provider,
                        'oauth_uid' => $id,
                        'facade' => 'wallet',
                        'provider' => $oauth_provider,
                        'picture' => $picture,
                        'first_name' => $first_name,
            			'last_name' => $last_name,
                    ]);

                    $transaction = Users::getDb()->beginTransaction();

                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $this->client->getId(),
                            'source_id' => (string) $id,
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                        } else {
                            $transaction->rollBack();
                            Yii::$app->getSession()->setFlash('error', [
                                Yii::t('app', 'Unable to save {client} account: {errors}', [
                                    'client' => $this->client->getTitle(),
                                    'errors' => json_encode($auth->getErrors()),
                                ]),
                            ]);
                        }
                    } else {
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', [
                            Yii::t('app', 'Unable to save user: {errors}', [
                                'client' => $this->client->getTitle(),
                                'errors' => json_encode($user->getErrors()),
                            ]),
                        ]);
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'source_id' => (string)$attributes['id'],
                ]);
                if ($auth->save()) {
                    /** @var Users $user */
                    $user = $auth->user;
                    $this->updateUserInfo($user);
                    Yii::$app->getSession()->setFlash('success', [
                        Yii::t('app', 'Linked {client} account.', [
                            'client' => $this->client->getTitle()
                        ]),
                    ]);
                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', 'Unable to link {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ]),
                    ]);
                }
            } else { // there's existing auth
                Yii::$app->getSession()->setFlash('error', [
                    Yii::t('app',
                        'Unable to link {client} account. There is another user using it.',
                        ['client' => $this->client->getTitle()]),
                ]);
            }
        }
    }

    /**
     * @param User $user
     */
    private function updateUserInfo(Users $user)
    {
        if ($user->status_activation_code === Users::STATUS_INSERTED){
            $password = Yii::$app->security->generateRandomString(60);
            $user->status_activation_code = Users::STATUS_ACTIVE;
            $user->password = $password;
            return $user->save();
        }
        return $user->status_activation_code !== Users::STATUS_BLOCKED;
    }
}
