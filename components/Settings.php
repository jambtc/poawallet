<?php
/**
* Questa classe carica le impostazioni della webapp
*/

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\Owner;
use app\models\Blockchains;
use app\models\Vapid;
use app\models\Host;
use app\models\Nodes;

class Settings extends Component
{

    public static function owner(){
        return Owner::findOne(1);
    }


    // questa funziona ora assume come valore lo user id 
    public static function poa($user_id = 0){
        if ($user_id != 0) {
            $node = Nodes::find()->where(['id_user'=> $user_id])->one();
        } else {
            $node = Nodes::find()->where(['id_user'=> Yii::$app->user->id])->one();
        }
        return $node;
    }

    public static function vapid(){
        return Vapid::findOne(1);
    }

    public static function host(){
        return Host::findOne(1);
    }

}
