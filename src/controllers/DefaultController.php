<?php

namespace uranum\location\controllers;

use uranum\location\components\LocationSetter;
use uranum\location\Module;
use Yii;
use yii\web\Controller;

class DefaultController extends Controller
{
    /** @property LocationSetter */
    private $component;

    public function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->component = Yii::$container->get(LocationSetter::className());
    }

    public function actionSendCity()
    {
        $city = Yii::$app->request->getBodyParam('city');
        if (Yii::$app->request->isAjax) {
            try {
                $this->component->saveCity($city);
                echo 'good';
            } catch(\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
