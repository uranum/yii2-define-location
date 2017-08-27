<?php

namespace uranum\location\widget;


use uranum\location\Module;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\web\Session;

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
    public $chooseTitle = 'Выбрать';
    public $cssLocationMarker = 'fa fa-map-marker';
    public $cssLocationLink = 'ur-location-link';
    public $sendUrl;
    public $city;
    public $cssPredefinedCities = 'ur-predefined-block';
	/** @var Session $session */
	private $session;
	/** @var  $module Module */
	private $module;

	public function init()
	{
		parent::init();
        $this->session = Yii::$app->session;
        $this->checkIpGeoComponent();
        $this->setCity();

        //todo[cors] должно отрабатывать запись местоположения на событие регистрации  --- 27.08.2017
//todo[cors] должно отрабатывать запись местоположения на событие логин при том, что у юзера нет в базе местоположения   --- 27.08.2017
        /**
         * изначально в свойство $this->city надо записать данные ipgeobase
         *
         * 1) города нет в сессии, в базе :: есть в гео  -- выводим гео  (первый заход на сайт, повторный заход без логина)
         * 2) города нет в сессии :: есть в базе, есть в гео  -- выводим базу  (повторный заход без логина)
         * 3) города нет в базе :: есть в сессии, есть в гео  -- выводим сессию  (текущая сессия незарегистрированного/незалогененного юзера)
         *
         * События:
         * 1) первый заход на сайт  -- города нет в сессии, в базе :: есть в гео (1)
         * 2) повторный заход с городом в базе, но без логина  -- города нет в сессии :: есть в гео, в базе (2)
         * 3) повторный заход без города в базе и без логина  -- города нет в сессии, в базе, :: есть есть в гео ()
         * 4) текущая сессия с внесенным городом в сессию без логина  --
         *
         * если(город уже в сессии)
         *      то в свойство записать данные сессии
         * если(Города нет в сессии и юзер не залоген)
         *      то в свойство записать 'Выбрать'
         * если(Города нет в сессии и юзер залоген)
         *      то найти его местоположение в базе
         *      если(местоположение есть)
         *          то в свойство и сессию записать найденное
         *      иначе
         *          в сессию записать свойство
         */





//		elseif (empty($cityFromSession) && Yii::$app->user->isGuest) {
//			if (empty($this->city)) {
//				$this->city = Yii::t('location', 'Выбрать');
//			}
//			$this->session->set(Module::USER_CITY, $this->city);
//		} elseif (empty($cityFromSession) && !Yii::$app->user->isGuest) {
//			$location = UserIp::findOne(['user_id' => Yii::$app->user->id]);
//			if ($location) {
//				$this->session->set(Module::USER_CITY, $location->location);
//				$this->city = $location->location;
//			} else {
//				$this->session->set(Module::USER_CITY, $this->city);
//			}
//		}
		
		$this->sendUrl = Url::to(['/location/default/send-city']);
		LocationAsset::register($this->getView());
	}

    private function setCity()
    {
        if ($this->isCityInSession()) {
            $this->city = $this->session->get(Module::USER_CITY);
        } else {
            $this->city = $this->getCityFromGeo();
        }
    }

    private function getCityFromGeo()
    {
        $result = $this->module->ipGeoComponent->getLocation(Yii::$app->request->userIP);
        return ($result['city']) ?? $this->chooseTitle;
    }

    private function isCityInSession()
    {
        return $this->session->has(Module::USER_CITY);
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
		$li = [//todo[cors] создать настройку предустановленных городов  --- 27.08.2017
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

    private function checkIpGeoComponent()
    {
        if (!$this->ipGeoComponent instanceof \himiklab\ipgeobase\IpGeoBase) {
            throw new InvalidConfigException('Property ipGeoComponent must be instance of himiklab\ipgeobase\IpGeoBase');
        }
    }
}