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

Указать в конфигурации, что модуль должен быть предзагружен:
```php
bootstrap' => [
    ....,
    'location'
],
```
Применить миграцию:
```php
php yii migrate --migrationPath=@uranum/location/migrations
```