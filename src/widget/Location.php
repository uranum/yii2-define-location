<?php

namespace uranum\location\widget;


use uranum\location\models\UserIp;
use uranum\location\Module;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

class Location extends Widget
{
	public $cssChooseBlockContentAutoComplete = 'ur-location-choose-autocomplete small-9 medium-5 column';
	public $cssChooseBlockContentButton = 'ur-location-choose-button small-3 medium-2 large-1 column end';
	public $cssChooseBlockContentHeader = 'ur-location-choose-header small-12 medium-4 column';
	public $cssChooseBlockContentWrapper = 'ur-location-choose-content-wrap row collapse';
	public $cssCloseBlockButton = 'ur-location-close-block-button';
	public $cssChooseBlockContent = 'ur-location-choose-content';
	public $cssChooseBlockWrapper = 'ur-location-choose-wrap';
	public $cssSubmitButton = 'ur-submit-btn button postfix';
	public $cssLocationWrapper = 'hidden-for-small-only';
	public $cssChooseBlock = 'ur-location-choose-block';
	public $cssCloseFigureClass = 'fa fa-close fa-2x';
	public $header = 'Выберите Ваше местоположение';
	public $cssLocationMarker = 'fa fa-map-marker';
	public $cssLocationLink = 'ur-location-link';
	public $sendUrl;
	public $city;
	public $cssPredefinedCities = 'ur-predefined-block';
	
	public function init()
	{
		parent::init();
		
		$session = Yii::$app->session;
		$city    = $session->get(Module::USER_CITY);
		
		if ($session->has(Module::USER_CITY)) {
			$this->city = $city;
		} elseif (empty($city) && Yii::$app->user->isGuest) {
			if (empty($this->city)) {
				$this->city = Yii::t('location', 'Выбрать');
			}
			$session->set(Module::USER_CITY, $this->city);
		} elseif (empty($city) && !Yii::$app->user->isGuest) {
			$location = UserIp::findOne(['user_id' => Yii::$app->user->id]);
			if ($location) {
				$session->set(Module::USER_CITY, $location->location);
				$this->city = $location->location;
			} else {
				$session->set(Module::USER_CITY, $this->city);
			}
		}
		
		$this->sendUrl = Url::to(['/location/default/send-city']);
		$this->registerTranslations();
		LocationAsset::register($this->getView());
	}
	
	private function registerTranslations()
	{
		Yii::$app->i18n->translations['location'] = [
			'class'          => 'yii\i18n\PhpMessageSource',
			'sourceLanguage' => 'ru-RU',
			'basePath'       => dirname(__DIR__) . '/messages',
		];
	}
	
	public function run()
	{
		$this->renderLocation();
		$this->renderChooseBlock();
	}
	
	/**
	 * Render the stroke with the city name
	 */
	private function renderLocation()
	{
		$html = Html::tag('i', '', ['class' => $this->cssLocationMarker]);
		$html = Html::tag('span', $html, ['class' => $this->cssLocationWrapper]);
		$html .= Html::tag('span', Html::encode($this->city), ['class' => $this->cssLocationLink, 'id' => 'ur-city-link']);
		echo $html;
	}
	
	/**
	 * Render the block with the region's names
	 * and autocomplete field
	 */
	private function renderChooseBlock()
	{
		$block = Html::beginTag('div', ['class' => $this->cssChooseBlockContent]);
		$block .= $this->renderContentChooseBlock();
		$block .= $this->renderCloseBlockButton();
		$block .= Html::endTag('div');
		$block .= $this->renderPredefinedCities();
		echo Html::tag('div', $block, ['class' => $this->cssChooseBlockWrapper, 'id' => 'ur-choose-block']);
	}
	
	/**
	 * Return the content of the choose block
	 */
	private function renderContentChooseBlock()
	{
		$header       = Html::tag('div', $this->renderChooseHeader(), ['class' => $this->cssChooseBlockContentHeader]);
		$autocomplete = Html::tag('div', $this->renderAutocomplete(), ['class' => $this->cssChooseBlockContentAutoComplete]);
		$locations    = Html::tag('div', $this->renderSubmitButton(), ['class' => $this->cssChooseBlockContentButton]);
		$html         = $header . $autocomplete . $locations;
		
		return Html::tag('div', $html, ['class' => $this->cssChooseBlockContentWrapper]);
	}
	
	private function renderCloseBlockButton()
	{
		$html = Html::tag('i', '', ['class' => $this->cssCloseFigureClass, 'id' => 'ur-close-button']);
		
		return Html::tag('div', $html, ['class' => $this->cssCloseBlockButton]);
	}
	
	private function renderChooseHeader()
	{
		return Html::tag('span', Yii::t('location', $this->header));
	}
	
	private function renderAutocomplete()
	{
		return AutoComplete::widget([
			'name'          => 'city',
			'id'            => 'ur-city-auto',
			'options'       => [
				'class' => 'form-control',
			],
			'clientOptions' => [
				'source'    => new JsExpression("function(request, response) {
                    $.ajax({
						url : 'https://api.vk.com/method/database.getCities?',
						dataType : 'jsonp',
						data : {
							oauth:1,
							v:'5,5',
							need_all:5,
							count:10,
							country_id:1,
							q: function() {
								return $('#ur-city-auto').val();
							}
						},
						success : function(data) {
							response($.map(data.response.items, function(item) {
								return {
									label : item.title + ' (' + item.region + ')',
									value : item.title
								}
							}));
						}
					})
				}"),
				'select'    => new JsExpression("function( event, ui ) {
					$('#ur-submit-button').removeClass('disabled');
                }"),
				'minLength' => 2,
			],
		]);
	}
	
	private function renderSubmitButton()
	{
		return Html::button('Ok', ['id' => 'ur-submit-button', 'class' => $this->cssSubmitButton . ' disabled', 'onclick' => 'sendCity("' . $this->sendUrl . '")']);
	}
	
	private function renderPredefinedCities()
	{
		$li = [
			Html::a('Новосибирск', false, ['class' => 'ur-pred-city-li']),
			Html::a('Бердск', false, ['class' => 'ur-pred-city-li']),
			Html::a('Томск', false, ['class' => 'ur-pred-city-li']),
			Html::a('Барнаул', false, ['class' => 'ur-pred-city-li']),
			Html::a('Москва', false, ['class' => 'ur-pred-city-li']),
		];
		$ul = Html::ul($li, ['class' => 'inline-list', 'encode' => false]);
		$html = Html::tag('div', $ul, ['class' => 'small-12']);
		$predefined   = Html::tag('div', $html, ['class' => $this->cssPredefinedCities]);
		return $predefined;
	}
}