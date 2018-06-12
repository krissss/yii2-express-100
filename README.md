Yii2 快递100接口
============
Yii2 快递100接口

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kriss/yii2-express-100
```

or add

```
"kriss/yii2-express-100": "*"
```

to the require section of your `composer.json` file.


Usage
-----

配置 Component

```php
use kriss\express100\ExpressApi;

$config = [
    'components' => [
        ExpressApi::COMPONENT_NAME => [
            'class' => ExpressApi::class,
            // 其他配置参数
        ]
    ]
]
```

使用

```php
use kriss\express100\ExpressApi;
use Yii;

Yii::$app->get(ExpressApi::COMPONENT_NAME)->api('快递名称', '快递单号');
```