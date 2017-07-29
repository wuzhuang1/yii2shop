<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-21
 * Time: 8:04
 */
namespace backend\controllers;
use yii\web\Controller;
use backend\filters\RbacFilter;
use backend\models\Article;
use backend\models\ArticleDetail;
use backend\models\ArticleSearchForm;

use yii\data\Pagination;


class ArticleController extends Controller{
    public function actionIndex()
    {
        $model = new ArticleSearchForm();
        //$keywords = \Yii::$app->request->get('keywords');
        //搜索sql   where name like %四川%
        $query = Article::find();
        $model->load(\Yii::$app->request->get());
        if($model->name){
            $query->andWhere(['like','name',$model->name]);
        }
        if($model->intro){
            $query->andWhere(['like','intro',$model->intro]);
        }
        /*if($keywords){
            $query->where(['like','name',$keywords]);
        }*/

        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'pageSize'=>2
        ]);
        $articles = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['articles'=>$articles,'pager'=>$pager,'model'=>$model]);
    }
    /*
     * 添加文章
     */
    public function actionAdd()
    {
        $article = new Article();
        $article_detail = new ArticleDetail();
        if($article->load(\Yii::$app->request->post())
            && $article_detail->load(\Yii::$app->request->post())
            && $article->validate()
            && $article_detail->validate()){
            $article->save();
            $article_detail->article_id = $article->id;
            $article_detail->save();

            \Yii::$app->session->setFlash('success','文章添加成功');
            return $this->redirect(['index']);
        }
//        $categories = ArticleCategory::find()->asArray()->where(['status'=>1])->all();
        /*$options = [];
        foreach ($categories as $cate){
            $options[$cate['id']] = $cate['name'];
        }*/
//        $options = ArrayHelper::map($categories,'id','name');
        return $this->render('add',['article'=>$article,'article_detail'=>$article_detail]);
    }
    /*
     * 修改文章
     */
    public function actionEdit($id)
    {
        $article = Article::findOne(['id'=>$id]);
        $article_detail = $article->detail;
        if($article->load(\Yii::$app->request->post())
            && $article_detail->load(\Yii::$app->request->post())
            && $article->validate()
            && $article_detail->validate()){
            $article->save();
            //$article_detail->article_id = $article->id;
            $article_detail->save();

            \Yii::$app->session->setFlash('success','文章修改成功');
            return $this->redirect(['index']);
        }

        return $this->render('add',['article'=>$article,'article_detail'=>$article_detail]);
    }

    /*
     * 查看文章
     */
    public function actionView($id)
    {
        $model = Article::findOne($id);

        return $this->render('view',['model'=>$model]);
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    'imageUrlPrefix'  => \Yii::$app->request->hostInfo,//图片访问路径前缀
                    'imagePathFormat' => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}', //上传保存路径
                    'imageRoot' => \Yii::getAlias('@webroot'),
                ],
            ]
        ];
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