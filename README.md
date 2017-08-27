# yii2-define-location
Define the user location by ip or set it manually

### Installation
```bash
composer require uranum/yii2-define-location:"dev-master"
```

### Setup
Подключить модуль в common/config.php (он должен быть доступен для консольных команд!)
```php
'modules' => [
    'location' => [
        'class' => 'uranum\location\Module',
        'userModelClass' => 'frontend\modules\user\models\User', // здесь указать класс модели User
    ],
]
```
Для автоматического определения местоположения настройте компонент [Yii2 IpGeoBase.ru wrapper](https://github.com/himiklab/yii2-ipgeobase-component)
(инструкции по [ссылке](https://github.com/himiklab/yii2-ipgeobase-component#Установка), установка компонента не требуется, только
указание компонента в конфигурации).

Указать в конфигурации, в секции bootstrap следующие строки:
```php
bootstrap' => [
    ....,
    'location', 
    'uranum\location\InitApp'
],
```
Применить миграцию:
```php
php yii migrate --migrationPath=@uranum/location/migrations
```

Вывести виджет в нужном месте:
```php
echo  \uranum\location\widget\Location::widget([
        'ipGeoComponent' => Yii::$app->ipgeo, // здесь ipgeo - название компонента himiklab\ipgeobase\IpGeoBase, указанного в секции components в config
    ]);
```