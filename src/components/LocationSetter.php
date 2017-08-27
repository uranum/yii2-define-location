<?php
/**
 * Created by PhpStorm.
 * User:    Евгений Емельянов <e9139905539@gmail.com>
 */

namespace uranum\location\components;


use uranum\location\models\UserIp;
use uranum\location\Module;
use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\helpers\VarDumper;
use yii\web\Session;

class LocationSetter extends Component
{
    const EVENT_SET_LOCATION = 'setLocation';
    const EVENT_USER_LOGIN = 'userLogin';
    const EVENT_USER_SIGNUP = 'userSignup';

    private $session;
    private $module;

    public function __construct(Module $module, Session $session, array $config = [])
    {
        parent::__construct($config);
        $this->session = \Yii::$app->session;
        $this->module = $module;
    }

    /**
     * @param $event Event
     */
    public function handleLoginEvent($event)
    {
        // проверить базу и записать в сессию из базы
        // оставить то, что в сессии
        // записать в сессию из гео

//        VarDumper::dump($event->sender,5, true);
//        die();

        $storage = $this->hasUserCityInStorage($event->sender->id);

        if (null !== $storage) {
            $this->session->set(Module::USER_CITY, $storage);
        } else {
            $this->setUserCityFromGeo();
        }
    }

    protected function hasUserCityInStorage($id)
    {
        return UserIp::findOne(['user_id' => $id]);
    }

    protected function setUserCityFromGeo()
    {
        if (!$this->session->has(Module::USER_CITY)) {
            $this->session->set(Module::USER_CITY, $this->module->ipGeoComponent->getLocation(Yii::$app->request->userIP));
        }
    }
}