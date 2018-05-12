<?php
/**
 * Created by PhpStorm.
 * User:    Евгений Емельянов <e9139905539@gmail.com>
 */

namespace uranum\location;


use uranum\location\components\LocationSetter;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\web\User;

class InitApp implements BootstrapInterface
{
    /**
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        if ($app instanceof \yii\web\Application) {
            $session = $app->session;
            $app->user->on(User::EVENT_AFTER_LOGIN, [$container->get('uranum\location\components\LocationSetter'), 'handleLoginEvent']);

            if ($session->isActive) {
                $city = $session->get(Module::USER_CITY);
                $app->user->on(User::EVENT_AFTER_LOGOUT, function () use ($city, $session) {
                    $session->set(Module::USER_CITY, $city);
                });
            }

            $container->setSingleton('LocationModule', function() use ($app) {
                $module = Module::getInstance();
                return $app->getModule($module->id);
            });
        }

        $container->set(LocationSetter::className());
    }
}