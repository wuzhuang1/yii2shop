<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;

class MenuController extends \yii\web\Controller
{
    //显示列表
    public function actionIndex()
    {
        $models=Menu::find()->all();
        return $this->render('index',['models'=>$models]);
    }
    //添加
    public function actionAdd(){
        $models=new Menu();
        //$menu=Menu::find()->where(['fid'=>'0'])->all();
        //var_dump($menu);exit;
        if($models->load(\yii::$app->request->post()) && $models->validate()){
            $models->save();
            \yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['index']);
        }
        return $this->render('add',['models'=>$models]);
        //'menu'=>$menu
    }
    //修改
    public function actionEdit($id){
        $models=Menu::findOne(['id'=>$id]);
        if($models->load(\yii::$app->request->post()) && $models->validate()){
            $models->save();
            \yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['index']);
        }
        return $this->render('add',['models'=>$models]);
    }
    //删除
    public function actionDel($id){
        $model=Menu::findOne(['fid'=>$id]);
        if($model==null){
            Menu::findOne(['id'=>$id])->delete();
            \yii::$app->session->setFlash('success','删除成功');
        }else{
            \yii::$app->session->setFlash('success','删除失败');
        }
        return $this->redirect(['index']);
    }
    //权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }


}
