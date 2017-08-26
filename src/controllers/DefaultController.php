<?php

namespace frontend\modules\location\controllers;

use frontend\modules\location\models\UserIp;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;

class DefaultController extends Controller
{
	public function actionSendCity()
	{
		$city    = Yii::$app->request->post('city');
		$session = Yii::$app->session;
		
		if (Yii::$app->request->isAjax) {
			if (!Yii::$app->user->isGuest) {
				if (!$model = UserIp::findOne(['user_id' => Yii::$app->user->id])) {
					$model = new UserIp();
				}
				$model->location = $city;
				$model->save();
			}
			if (!empty($city)) {
				$session->set('userCity', $city);
			}
			echo 'good';
		}
	}
}
