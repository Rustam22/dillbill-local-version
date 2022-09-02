<?php


namespace frontend\assets;

use yii\web\AssetBundle;

class BasicAppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        //'css/bootstrap.min.css',
        //'css/bootstrap-grid.min.css',
        //'css/bootstrap-reboot.min.css',
        'css/general.css',
    ];
    public $js = [
        //'js/jquery.js',
        //'js/bootstrap.min.js',
        //'js/popper.min.js',
        'js/general.js',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
    ];
    public $cssOptions = [
        'position' => \yii\web\View::POS_LOAD,
    ];
}