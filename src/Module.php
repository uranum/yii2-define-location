<?php

namespace frontend\modules\location;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\web\User;

/**
 * location module definition class
 
 * Модуль сделан относительно независимым.
  
 * Для его внедрения в проект достаточно скопировать папку с модулем
 * в свой frontend/modules;
 * применить миграцию:
 *
 * ```
 * php yii migrate --migrationPath=@frontend/modules/location/migrations
 * ```
 * прописать в конфиге:
 * ```php
 * bootstrap' => [
 *       ....,
 *       'location'
 *   ],
 * ....
 * 'modules' => [
 *      'location' => [
 *  	    'class'               => 'frontend\modules\location\Module',
 *	        'userModelClass'      => 'frontend\modules\user\models\User',
 *	        'controllerNamespace' => 'frontend\modules\location\controllers',
 *	    ],
 * ]
 * ```
 * разместить код виджета:
 * ```php
 *   echo Location::widget([
 *		'city' => $city,
 *	]);
 * ```
 * Переменную $city можно задать через получение данных о городе
 * от сервиса IpGeoBase через ip пользователя:
 * ```php *
 *  $cityArr = Yii::$app->ipgeobase->getLocation(Yii::$app->request->userIP);
 *	$city = (empty($cityArr['city'])) ? 'Выбрать' : $cityArr['city'];
 * ```
 * а если не задавать эту переменную, то вместо города по-умолчанию
 * будет надпись "Выбрать"
 *
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
	/**
	 * @property string Class name of the linked User model
	 */
	public $userModelClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
	
	/**
	 * Bootstrap method to be called during application bootstrap stage.
	 * @param Application $app the application currently running
	 */
	public function bootstrap($app)
	{
		$app->user->on(User::EVENT_AFTER_LOGIN, function ($event) {
			Yii::$app->session->remove('userCity');
		});
	}
}
