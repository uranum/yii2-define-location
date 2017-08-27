<?php

namespace uranum\location\controllers;

use uranum\location\models\UserIp;
use uranum\location\Module;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;

class DefaultController extends Controller
{
	public function actionSendCity()
	{
		$city    = Yii::$app->request->getBodyParam('city');
		$session = Yii::$app->session;

        /**
         * основное действие:
         *  запись города в базу для логгед юзера
         *  запись города в сессию
         * при успешной записи выбранного города в базу
         */
		
		if (Yii::$app->request->isAjax) {
			if (!Yii::$app->user->isGuest) {
				if (!$model = UserIp::findOne(['user_id' => Yii::$app->user->id])) {
					$model = new UserIp();
				}
				$model->location = $city;
				$model->save();
			}
			if (!empty($city)) {
				$session->set(Module::USER_CITY, $city);
			}
			echo 'good';
		}
	}
}
