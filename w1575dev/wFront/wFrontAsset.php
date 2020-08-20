<?php

namespace w1575\wFront;

class wFrontAsset extends \yii\web\AssetBundle
{
    public $sourcePath = "@w1575/wFront/src";

    public $js = [
        'js/w.js'
    ];

    public $css = [
        'css/w.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}