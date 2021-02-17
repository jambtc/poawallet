<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Pin code application asset bundle.
 *
 * @author Sergio Casizzone <jambtc@gmail.com>
 * @since 2.0
 */
class LightWalletAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/pincode.css',
    ];
    public $js = [
        'src/ethjs/lightwallet.min.js',
        'src/ethjs/aes.js',
        'src/ethjs/aes-json-format.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}
