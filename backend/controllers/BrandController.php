<?php

namespace backend\controllers;

use backend\models\Brand;

class BrandController extends \yii\web\Controller
{
    //添加品牌
    public function actionAdd()
    {
        $model = new Brand();

        return $this->render('add',['model'=>$model]);
    }


    public function actionIndex()
    {
        return $this->render('index');
    }

}
