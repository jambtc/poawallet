<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\SettingsWebapp;
use app\models\SettingsUsers;


define('STOREFIELDS',   [
        'store_denomination',
        'bps_storeid',
        'store_website',
        'network_fee_mode',
        'invoice_expiration',
        'monitoring_expiration',
        'payment_tolerance',
        'speed_policy',
        'preferred_exchange',
        'spread',
        'default_currency_pairs',
        'CustomLogo' ,
        'DefaultPaymentMethod',
        'DefaultLang',
        'RequiresRefundEmail',
        'CustomCSS',
        'HtmlTitle',
        'OnChainMinValue',
        'LightningMaxValue',
        'LightningAmountInSatoshi',
        'RedirectAutomatically',
        'ShowRecommendedFee',
        'RecommendedFeeBlockTarget',
        'command',
        'AddressType',
        'DerivationScheme',
        'readExchangeId',
        'readKeyPublic',
        'readKeySecret',
        'readBitstampId',
        'writeExchangeId',
        'writeKeyPublic',
        'writeKeySecret',
        'writeBitstampId',
        'coinsEnabled',
        'userFeeMode',
    ]);

define('MERCHANTFIELDS',   [
       'blockchainAddress',
       'blockchainAsset',
   ]);


define('USERFIELDS',   [
       'first_name',
       'last_name',
       'username',
       'photo_url',
   ]);

define('SHOPFIELDS', [
    'StoreId',
    'Title',
    'Currency',
    'ShowCustomAmount',
    'ShowDiscount',
    'EnableTips',
    'ButtonText',
    'CustomButtonText',
    'CustomTipText',
    'CustomTipPercentages',
    'CustomCSSLink',
    'Template',
    'NotificationUrl',
    'NotificationEmail',
    'RedirectAutomatically',
    'Description',
    'files',
    'EmbeddedCSS',
    'NotificationEmailWarning',
    'EnableShoppingCart',
]);


define('WEBAPPFIELDS',   [
       'poa_expiration',
       'quota_iscrizione_socio',
       'quota_iscrizione_socioGiuridico',
       'gdpr_address',
       'gdpr_cap',
       'gdpr_telefono',
       'gdpr_fax'
]);

class Settings extends Component
{
  /**
   * Questa funzione carica le impostazioni della webapp
  */
  public static function load(){
      $array = array();
      // use "slug" column as key values

      $dataProvider = new ActiveDataProvider([
          'query' => SettingsWebapp::find(),
          'pagination' => false // !!! IMPORTANT TO GET ALL MODELS
      ]);

      // echo "<pre>".print_r($dataProvider,true)."</pre>";
      // exit;
      // $iterator = new CDataProviderIterator($dataProvider);

      foreach ($dataProvider->getModels() as $item) {
        $array[$item->setting_name] = $item->setting_value;
      }
      // echo "<pre>".print_r($array,true)."</pre>";
      // exit;

      if (!(isset($array['blockchainAsset'])) || $array['blockchainAsset'] == '' || $array['blockchainAsset'] == '0')
          $array['blockchainAsset'] = "{'BTC':'BTC'}";

      if (!(isset($array['poa_decimals'])) || $array['poa_decimals'] == '')
          $array['poa_decimals'] = 2;

      if (!(isset($array['id_exchange'])) || $array['id_exchange'] == '')
        $array['id_exchange'] = 1;

      if (!(isset($array['gdpr_city'])) || $array['gdpr_city'] == '')
        $array['gdpr_city'] = 1;

      foreach (WEBAPPFIELDS as $key) {
        if (!array_key_exists($key, $array))
          $array[$key] = '';
      }

      $settings = (object) $array;
      // echo "<pre>".print_r($settings,true)."</pre>";
      // exit;
      return $settings;
  }

  // public function save($object, $fields=array()){
  //     # echo '<pre>'.print_r($object,true).'</pre>';
  //      // exit;
  //     $return = true;
  //     foreach($object as $desc => $val){
  //         $setting = null;
  //         if ($desc != 'step' && (empty($fields) || in_array($desc,$fields))){
  //             $setting = SettingsWebapp::model()->findByAttributes(array('setting_name'=>$desc));
  //             #echo "<br>".$desc." ".$val;
  //             #echo '<pre>'.print_r($setting,true).'</pre>';
  //
  //             if ($setting === null){
  //                 if ($val === null || $val == '')
  //                     $val = 0;
  //                 #echo '<pre>'.print_r('sono null',true).'</pre>';
  //                 $newSetting = new SettingsWebapp;
  //                 $newSetting->setting_name = $desc;
  //                 $newSetting->setting_value = $val;
  //                 #echo '<pre>'.print_r($newSetting->attributes,true).'</pre>';
  //                 if (!$newSetting->insert()){
  //                     #echo '<pre>'.print_r($newSetting->attributes,true).'</pre>';
  //                     #exit;//
  //                     $return = false;
  //                 }
  //             }else{
  //                 $setting->setting_value = $val;
  //                 if (!$setting->update()){
  //                     #echo '<pre>'.print_r($setting->attributes,true).'</pre>';
  //                     #exit;//$return = false;
  //                     $return = false;
  //                 }
  //             }
  //         }
  //     }
  //     return $return;
  // }

  /**
   * Questa funzione CARICA le impostazioni degli user Telegram dall'id_telegram
   * @param Number $telegram_id è l'id dell'utente
  */
  // public function loadTelegramUser($telegram_id){
  //     $array = [];
  //
  //     $users = Users::model()->findByAttributes(['telegram_id'=>$telegram_id]);
  //
  //     $criteria = new CDbCriteria();
  //     $criteria->compare('id_user',$users->id_user, false);
  //
  //     $dataProvider=new CActiveDataProvider('SettingsUser', array(
  //         'criteria'=>$criteria,
  //     ));
  //     $iterator = new CDataProviderIterator($dataProvider);
  //
  //     foreach($iterator as $item) {
  //       $array[$item->setting_name] = $item->setting_value;
  //     }
  //
  //     foreach (USERFIELDS as $key) {
  //         if (!array_key_exists($key, $array))
  //             $array[$key] = '';
  //     }
  //
  //     $settings = (object) $array;
  //     // echo "<pre>".print_r($settings,true)."</pre>";
  //     // exit;
  //     return $settings;
  // }

  /**
   * Questa funzione CARICA le impostazioni degli user
   * @param Number $id_user è l'id dell'utente
  */
  public function loadUser($id_user){
      $array = [];

      $query = SettingsUsers::find()->where(['id_user' => $id_user]);

      $dataProvider = new ActiveDataProvider([
          'query' => $query,
      ]);

      foreach ($dataProvider->getModels() as $item) {
        $array[$item->setting_name] = $item->setting_value;
      }


      foreach (USERFIELDS as $key) {
          if (!array_key_exists($key, $array))
              $array[$key] = '';
      }


      foreach (MERCHANTFIELDS as $key) {
          if (!array_key_exists($key, $array))
              $array[$key] = '';
      }

      if (!(isset($array['id_gateway'])) || $array['id_gateway'] == 0 || $array['id_gateway'] == '')
          $array['id_gateway'] = 1;

          if (!(isset($array['id_exchange'])) || $array['id_exchange'] == 0 || $array['id_exchange'] == '')
              $array['id_exchange'] = 1;

      if (!(isset($array['blockchainAsset'])) || $array['blockchainAsset'] == '')
          $array['blockchainAsset'] = "{'BTC':'BTC'}";

      $settings = (object) $array;
      // echo "<pre>".print_r($settings,true)."</pre>";
      // exit;
      return $settings;
  }
  /**
   * Questa funzione salva le impostazioni degli user
   * @param Number $id_user è l'id dell'utente
   * @param Array $object è un array contenente 'descrizione_impostazione'  => 'valore_impostazione'
   * @param Array $fields è un array contenente i soli campi da modificare/aggiornare
  */
  // public function saveUser($id_user,$object, $fields=array()){
  //      #echo '<pre>'.print_r($object,true).'</pre>';
  //      #exit;
  //     $return = true;
  //     foreach($object as $desc => $val){
  //         $setting = null;
  //         if ($desc != 'step' && (empty($fields) || in_array($desc,$fields))){
  //             $setting = SettingsUser::model()->findByAttributes(array('id_user'=>$id_user,'setting_name'=>$desc));
  //             #echo "<br>".$desc." ".$val;
  //             #echo '<pre>'.print_r($setting,true).'</pre>';
  //             #exit;
  //             if ($val === null || $val == '')
  //                 $val = 0;
  //
  //             if ($setting === null){
  //                 #echo '<pre>'.print_r('sono null',true).'</pre>';
  //
  //                 $newSetting = new SettingsUser;
  //                 $newSetting->id_user = $id_user;
  //                 $newSetting->setting_name = $desc;
  //                 $newSetting->setting_value = $val;
  //                 #echo '<pre>'.print_r($newSetting->attributes,true).'</pre>';
  //                 if (!$newSetting->insert()){
  //                     #echo '<pre>'.print_r($newSetting->attributes,true).'</pre>';
  //                     #exit;//
  //                     $return = false;
  //                 }
  //             }else{
  //                 $setting->setting_value = $val;
  //                 if (!$setting->update()){
  //                     #echo '<pre>'.print_r($setting->attributes,true).'</pre>';
  //                     #exit;//$return = false;
  //                     $return = false;
  //                 }
  //             }
  //         }
  //     }
  //     return $return;
  // }

  /**
   * Questa funzione CARICA le impostazioni degli stores
   * @param Number $id_store è l'id dello store
  */
  // public function loadStore($id_store){
  //     $array = [];
  //     $criteria = new CDbCriteria();
  //     $criteria->compare('id_store',$id_store, false);
  //
  //     $dataProvider=new CActiveDataProvider('SettingsStores', array(
  //         'criteria'=>$criteria,
  //     ));
  //     $iterator = new CDataProviderIterator($dataProvider);
  //
  //     foreach($iterator as $item) {
  //         $array[$item->setting_name] = $item->setting_value;
  //     }
  //
  //     foreach (STOREFIELDS as $key) {
  //         if (!array_key_exists($key, $array))
  //             $array[$key] = '';
  //     }
  //
  //
  //     $settings = (object) $array;
  //     // echo "<pre>".print_r($settings,true)."</pre>";
  //     // exit;
  //     return $settings;
  // }
   /**
   * Questa funzione salva le impostazioni degli stores
   * @param Number $id_user è l'id dell'utente
   * @param Array $object è un array contenente 'descrizione_impostazione'  => 'valore_impostazione'
   * @param Array $fields è un array contenente i soli campi da modificare/aggiornare
  */
  // public function saveStore($id_store,$attributes, $fields=array()){
  //     #echo '<pre>'.print_r($attributes,true).'</pre>';
  //     #exit;
  //     $return = true;
  //     foreach($attributes as $desc => $val){
  //         $setting = null;
  //         if (empty($fields) || in_array($desc,$fields)){
  //             $setting = SettingsStores::model()->findByAttributes(array('id_store'=>$id_store,'setting_name'=>$desc));
  //
  //             if ($setting === null){
  //                 if ($val === null || $val == '')
  //                     $val = 0;
  //                 $newSetting = new SettingsStores;
  //                 $newSetting->id_store = $id_store;
  //                 $newSetting->setting_name = $desc;
  //                 $newSetting->setting_value = $val;
  //                 #echo '<pre>'.print_r($newSetting->attributes,true).'</pre>';
  //                 #exit;
  //                 if (!$newSetting->insert()){
  //                     #echo '<pre>'.print_r($newSetting->attributes,true).'</pre>';
  //                     #exit;//
  //                     $return = false;
  //                     #echo "<br>".$desc." non salvato!";
  //                 }else{
  //                     #echo "<br>".$desc." salvato!";
  //                 }
  //
  //             }else{
  //                 $setting->setting_value = $val;
  //                 if (!$setting->update()){
  //                     #echo '<pre>'.print_r($setting->attributes,true).'</pre>';
  //                     #exit;//$return = false;
  //                     #echo "<br>".$desc." non aggiornato!";
  //                     $return = false;
  //                 }
  //                 else{
  //                     #echo "<br>".$desc." aggiornato!";
  //                 }
  //             }
  //         }
  //     }
  //     #echo 'return è: '.var_dump($return);
  //     #exit;
  //     #exit;
  //     return $return;
  // }
  /**
   * Questa funzione CARICA le impostazioni dello shop online
   * @param Number $id_shop è l'id della app
  */
  // public function loadShop($id_shop){
  //     $array = [];
  //     $criteria = new CDbCriteria();
  //     $criteria->compare('id_shop',$id_shop, false);
  //
  //     $dataProvider=new CActiveDataProvider('SettingsShops', array(
  //         'criteria'=>$criteria,
  //     ));
  //     $iterator = new CDataProviderIterator($dataProvider);
  //
  //     foreach($iterator as $item) {
  //         $array[$item->setting_name] = $item->setting_value;
  //     }
  //
  //     foreach (SHOPFIELDS as $key) {
  //         if (!array_key_exists($key, $array))
  //             $array[$key] = '';
  //     }
  //
  //     $settings = (object) $array;
  //     // echo "<pre>".print_r($settings,true)."</pre>";
  //     // exit;
  //     return $settings;
  // }

  /**
   * Questa funzione salva le impostazioni degli stores
   * @param Number $id_shop è l'id dello shop
   * @param Array $object è un array contenente 'descrizione_impostazione'  => 'valore_impostazione'
   * @param Array $fields è un array contenente i soli campi da modificare/aggiornare
  */
  // public function saveShop($id_shop,$attributes, $fields=array()){
  //     $return = true;
  //     foreach($attributes as $desc => $val){
  //         $setting = null;
  //         if (empty($fields) || in_array($desc,$fields)){
  //             $setting = SettingsShops::model()->findByAttributes(array('id_shop'=>$id_shop,'setting_name'=>$desc));
  //
  //             if ($setting === null){
  //                 if ($val === null || $val == '')
  //                     $val = 0;
  //                 $newSetting = new SettingsShops;
  //                 $newSetting->id_shop = $id_shop;
  //                 $newSetting->setting_name = $desc;
  //                 $newSetting->setting_value = $val;
  //                 if (!$newSetting->insert()){
  //                     $return = false;
  //                 }
  //
  //             }else{
  //                 $setting->setting_value = $val;
  //                 if (!$setting->update()){
  //                     $return = false;
  //                 }
  //
  //             }
  //         }
  //     }
  //     #echo 'return è: '.var_dump($return);
  //     #exit;
  //     #exit;
  //     return $return;
  // }

}
