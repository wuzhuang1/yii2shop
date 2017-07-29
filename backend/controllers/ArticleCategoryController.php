<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-19
 * Time: 15:15
 */
namespace backend\controllers;
use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;
use backend\filters\RbacFilter;

class ArticleCategoryController extends Controller{
        public function actionIndex()
        {
            $models=ArticleCategory::find()->where(['>','status',-1]);
            $total=$models->count();
            $perPage=2;
            //Pagination
            $pager = new Pagination([
                'totalCount'=>$total,
                'defaultPageSize'=>$perPage
            ]);

            $model = $models->limit($pager->limit)->offset($pager->offset)->all();

            return $this->render('index',['model'=>$model,'pager'=>$pager]);
        }

    public function actionAdd(){
            $model=new ArticleCategory();
            $request=new Request();
            if($request->isPost) {
                $model->load($request->post());
                if ($model->validate()) {
                    $model->save();
                    return $this->redirect(['article-category/index']);
                }
            }
            return $this->render('add',['model'=>$model]);
    }

    public function actionEdit($id){
        $model=ArticleCategory::findOne(['id'=>$id]);
        $request=new Request();
        if($request->isPost) {
            $model->load($request->Post());
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['article-category/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
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