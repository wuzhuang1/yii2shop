<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //配置好下面的
        'authManager'=>[
            'class'=>\yii\rbac\DbManager::className()
        ],
    ] ,
];
