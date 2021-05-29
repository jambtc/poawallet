<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Blockchain Synchronization asset bundle.
 *
 * @author Sergio Casizzone <jambtc@gmail.com>
 * @since 2.0
 */
class QRCodeReaderAsset extends AssetBundle
{
    public $basePath = '@webroot/js/qrcodescanner';
    public $baseUrl = '@web/js/qrcodescanner';
    public $css = [
        'qr-scanner.css',
    ];
    public $js = [
    ];


    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}
