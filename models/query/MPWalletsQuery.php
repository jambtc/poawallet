<?php

namespace app\models\query;

use Yii;

/**
 * This is the ActiveQuery class for [[MPWallets]].
 *
 * @see MPWallets
 */
class MPWalletsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return MPWallets[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return MPWallets|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


    // public function userWalletAddress($id){
    //     return $this->andWhere(['id_user'=>$id]);
    // }

    /**
	 * This function return the wallet address from user id
	 */
	 public function userAddress($id) {
 		$wallet = $this->andWhere(['id_user'=>$id])->one();

		if (null === $wallet){
			// $session = Yii::$app->session;
			// $string = Yii::$app->security->generateRandomString(32);
			// $session->set('token-wizard', $string );

			return null; //$this->redirect(['wallet/wizard','token' => $string]);
		} else {
			return $wallet->wallet_address;
		}
	}

    /**
	 * This function return the user id from wallet address
	 */
	 public function userIdFromAddress($address) {
 		$wallet = $this->andWhere(['wallet_address'=>$address])->one();
        if ($wallet !== null) {
            return $wallet->id_user;
        } else {
            return null;
        }

	}
}
