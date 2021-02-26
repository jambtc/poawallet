<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;


class Logo extends Component
{

  /*
  * In questa funzione che stampa a video il footer, la variabile
  * $color deve essere inserita nel formato: #ffdd1a
  */
  public function footer($color=NULL){
      $versionfilename = Yii::$app->basePath."/../version.txt";
      if(file_exists($versionfilename)){
          $version = file_get_contents($versionfilename);
          $time = filemtime($versionfilename);
      }else{
          $version = "test";
          $time = time();
      }
      $footer = '';
      $footer .= '<div class="row">&nbsp;</div>';
      $footer .= '<center>';
      $footer .= '<div class="copyright">';
      if (!($color))
        $footer .= '<p>';
      else
        $footer .= '<p style="color:'.$color.';">';
      $footer .= 'Made with ❤️ by ';
      $footer .= '<a href="' . \Yii::$app->params['website'] . '" target="_blank">' . \Yii::$app->params['adminName'] . '</a>';
      $footer .= '<br>';
      $footer .= 'Release n. '.substr($version,0,7) .' ' .date('d/m/Y',$time);
      $footer .= '</p>';
      $footer .= '</div>';
      $footer .= '</center>';
      $footer .= '</div>';
      $footer .= '<div class="row">&nbsp;</div>';

      return $footer;
  }

  public function login(){
    $logo = '
    <a href="'. Url::to(['site/index']) .'">
        <div style="padding: 0px;" >
            <img alt="logo" style="display: block; margin-left: auto;	margin-right: auto; max-width: 150px; " src="css/images/logo.png">
        </div>
    </a>
     ';
     echo $logo;
 }
 public function header(){
    $logo = '
    <a href="'. Url::to(['site/index']) .'">

        <img alt="logo" style="display: block; height:57px; margin-top:5px;" src="'. Yii::app()->request->baseUrl.Yii::app()->params['logoApplicazione'].'">

    </a>
    ';
    // <h1 class="logo-name">'.Yii::t('app','BOLT').' <small class="logo-descri">'.Yii::t('app','TTS wallet').'</small></h1>
    echo $logo;
}
}
