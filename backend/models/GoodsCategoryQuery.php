<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-21
 * Time: 16:22
 */
namespace backend\models;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\ActiveQuery;

class GoodsCategoryQuery extends ActiveQuery{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}