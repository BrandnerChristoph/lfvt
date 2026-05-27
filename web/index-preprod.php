<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'preprod');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web_preprod.php';


$app = new yii\web\Application($config);

// detect previous page
$referrer = Yii::$app->request->referrer;

if ($referrer && strpos($referrer, 'preprod') !== false) {
    Yii::$app->homeUrl = '/index-preprod.php';
} else {
    Yii::$app->homeUrl = '/index.php';
}

$app->run();

/*


(new yii\web\Application($config))->run();
*/