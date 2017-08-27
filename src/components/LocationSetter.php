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
use yii\helpers\ArrayHelper;
use yii\web\Session;

class LocationSetter extends Component
{
    const EVENT_SET_LOCATION = 'setLocation';
    const EVENT_USER_LOGIN = 'userLogin';
    const EVENT_USER_SIGNUP = 'userSignup';

    private $session;
    private $module;

    public function __construct(Session $session, array $config = [])
    {
        parent::__construct($config);
        $this->session = \Yii::$app->session;
    }

    /**
     * @param $event Event
     */
    public function handleLoginEvent($event)
    {
        if(!$this->hasStoredCity($event->sender->id)) {
            $this->setUserCityFromGeo();
        }
        $this->saveCity($this->session->get(Module::USER_CITY));
    }

    public function saveCity($city)
    {
        if (!Yii::$app->user->isGuest) {
            /** @var UserIp $model */
            $model = UserIp::create($city);
            if (!$model->save()) {
                throw new \RuntimeException(Yii::t('location', "Ошибка! Не удалось сохранить Ваш выбор!"));
            }
        }
        $this->session->set(Module::USER_CITY, $city);
    }

    protected function hasStoredCity($userId): bool
    {
        $storage = $this->hasUserCityInStorage($userId);

        if (null !== $storage) {
            $this->session->set(Module::USER_CITY, $storage->location);
            return true;
        }
        return false;
    }

    protected function hasUserCityInStorage($id)
    {
        return UserIp::findOne(['user_id' => $id]);
    }

    protected function setUserCityFromGeo()
    {
        $this->module = Yii::$container->get('LocationModule');
        $result = $this->module->ipGeoComponent->getLocation(Yii::$app->request->userIP);
        $city = ArrayHelper::getValue($result, 'city', 'Not set');
        if (!$this->session->has(Module::USER_CITY) && !empty($city)) {
            $this->session->set(Module::USER_CITY, $city);
        }
    }
}