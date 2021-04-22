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

class Settings extends Component
{

    public static function owner(){
        return Owner::findOne(1);
    }

    public static function poa($id){
        return Blockchains::findOne($id);
    }

    public static function vapid(){
        return Vapid::findOne(1);
    }

    public static function host(){
        return Host::findOne(1);
    }

}
