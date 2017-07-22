<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-21
 * Time: 15:17
 */
namespace backend\controllers;
use backend\models\GoodsCategory;
use yii\web\Controller;
use yii\web\Request;
use yii\web\HttpException;

class GoodsCategoryController extends Controller{
    //列表
    public function actionIndex(){
        $models=GoodsCategory::find()->all();
        return $this->render('index',['models'=>$models]);
    }
    //添加
    public function actionAdd(){
        $models = new GoodsCategory(['parent_id'=>0]);
        $request=new Request();
        //判断提交方式
        if($request->isPost) {
            $models->load($request->post());
            //验证数据
            if ($models->validate()) {
                //提交数据到数据库
                //$models->save();
                //跳转页面
                //return $this->redirect(['goods-category/index']);
                if($models->parent_id){
                    //非一级分类

                    $category = GoodsCategory::findOne(['id'=>$models->parent_id]);
                    if($category){
                        $models->prependTo($category);
                    }else{
                        throw new HttpException(404,'上级分类不存在');
                    }
                }else{
                    //一级分类
                    $models->makeRoot();
                }
                \Yii::$app->session->setFlash('success','分类添加成功');
                return $this->redirect(['index']);
            }
        }
        //显示添加页面
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['models'=>$models,'categories'=>$categories]);
    }
    //修改
    public function actionEdit($id){
        //$models = new GoodsCategory(['parent_id'=>0]);
        $models=GoodsCategory::findOne(['id'=>$id]);
        $request=new Request();
        //判断提交方式
        if($request->isPost) {
            $models->load($request->post());
            //验证数据
            if ($models->validate()) {
                //提交数据到数据库
                //$models->save();
                //跳转页面
                //return $this->redirect(['goods-category/index']);
                if($models->parent_id){
                    //非一级分类

                    $category = GoodsCategory::findOne(['id'=>$models->parent_id]);
                    if($category){
                        $models->prependTo($category);
                    }else{
                        throw new HttpException(404,'上级分类不存在');
                    }
                }else{
                    //一级分类
                    $models->makeRoot();
                }
                \Yii::$app->session->setFlash('success','分类修改成功');
                return $this->redirect(['index']);
            }
        }
        //显示添加页面
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['models'=>$models,'categories'=>$categories]);
    }
    //删除
    public function actionDel($id){
        $models=GoodsCategory::find()->select('parent_id')->asArray()->all();
        foreach ($models as $model){
            //var_dump($id);
            //var_dump($model['parent_id']);exit;
            if($id!==$model['parent_id']){
                GoodsCategory::deleteAll(['id'=>$id]);//删除所有sex为1的记录
                \Yii::$app->session->setFlash('success','分类删除成功');
                return $this->redirect(['goods-category/index']);
            }else{
                throw new HttpException(404,'分类存在子分类');
            }
        }
        //var_dump($models->parent_id);exit;


    }
}