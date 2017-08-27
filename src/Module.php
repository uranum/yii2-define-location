<?php

namespace uranum\location;

use uranum\location\components\LocationSetter;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\web\User;


/**
 * location module definition class
 *
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
	public $userTableName;
    public $controllerNamespace = 'uranum\location\controllers';
    /** @var \himiklab\ipgeobase\IpGeoBase */
    public $ipGeoComponent;
    const TABLE_NAME = '{{%userIp}}';
    const USER_CITY = 'userCity';

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->user->on(User::EVENT_AFTER_LOGIN, [LocationSetter::className(), 'handleLoginEvent']);
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
        $this->initIpGeo();
        $this->userTableName = call_user_func([$this->userModelClass, 'tableName']);

    }

    private function initIpGeo()
    {
        $components = Yii::$app->getComponents();
        foreach ($components as $name => $component) {
            if ($component['class'] == 'himiklab\ipgeobase\IpGeoBase') {
                $this->ipGeoComponent = Yii::$app->get($name);
                break;
            }
        }
    }

    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['location'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'ru-RU',
            'basePath'       => '@vendor/uranum/yii2-define-location/src/messages',
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t($category, $message, $params, $language);
    }
}
