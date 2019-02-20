<?php
/**
 * Created by PhpStorm.
 * User:    Евегний Емельянов <emel.yanov@mail.ru>
 * Date:    02.12.16
 * Time:    14:43
 * Project: inknsk.dev
 */

namespace uranum\location\widget;


use yii\web\AssetBundle;

class LocationAsset extends AssetBundle
{
	public $sourcePath = '@vendor/uranum/yii2-define-location/src/widget/assets/';
	public $css = [
	    'css/styles.css'
	];
	public $js = [
	    'js/scripts.js'
	];
	public $depends = [
		'yii\web\JqueryAsset',
		'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
	];
}