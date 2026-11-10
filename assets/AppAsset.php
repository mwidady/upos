<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;


/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/bootstrap.min.css',
        'css/bootstrap-responsive.min.css',
        'css/fullcalendar.css',
        'css/matrix-style.css',
        'css/matrix-media.css',
        'css/uniform.css',
        'css/bootstrap-wysihtml5.css',
        'css/jquery.gritter.css'
    ];
    public $js = [
        'js/ajax-modal.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
  
  public $jsOptions =['position' => \yii\web\View::POS_HEAD ]; 
}
