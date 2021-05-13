<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AccountValueAsset extends AssetBundle
{
    public $basePath = '@webroot/bundles/AccountValue';
    public $baseUrl = '@web/bundles/AccountValue';
    public $css = [
    ];
    public $js = [
        'jquery.sparkline.js',
        'chart.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}
