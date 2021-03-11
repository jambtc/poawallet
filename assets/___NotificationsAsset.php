<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Notifications application asset bundle.
 *
 * @author Sergio Casizzone <jambtc@gmail.com>
 * @since 2.0
 */
class NotificationsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        
    ];
    public $js = [
        'js/notifications-MainWorker.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}
