<?php

namespace uranum\location;

use InvalidArgumentException;
use uranum\location\components\LocationSetter;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Object;
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
class Module extends \yii\base\Module
{
	/**
	 * @property string Class name of the linked User model
	 */
	public $userModelClass;
	public $userTableName;
    public $controllerNamespace = 'uranum\location\controllers';
    public $vkSecretToken;
    /** @var \himiklab\ipgeobase\IpGeoBase */
    public $ipGeoComponent;
    private $locationSetter;
    const TABLE_NAME = '{{%userIp}}';
    const USER_CITY = 'userCity';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
        $this->initIpGeo();
        $this->locationSetter = Yii::createObject(LocationSetter::className());
        $this->userTableName = call_user_func([$this->userModelClass, 'tableName']);

        if (null === $this->vkSecretToken) {
            throw new InvalidArgumentException("Set secret token in config!");
        }
    }

    public function getSecretToken()
    {
        return $this->vkSecretToken;
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

        if (!$this->ipGeoComponent instanceof \himiklab\ipgeobase\IpGeoBase) {
            throw new InvalidConfigException('It seems there is no installed "yii2-ipgeobase-component" component. Be sure the component is correctly installed and it is the instance of himiklab\ipgeobase\IpGeoBase');
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
