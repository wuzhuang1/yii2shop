<?php

namespace backend\controllers;
use backend\filters\RbacFilter;
use backend\models\Goods;
use backend\models\GoodsSearchForm;
use yii\web\Controller;
use yii\data\Pagination;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use flyok666\uploadifive\UploadAction;
use yii\web\NotFoundHttpException;

class GoodsController extends Controller{
    public function actionIndex(){
        //实列化搜索模型
        $model=new GoodsSearchForm();
        //实列化goods表模型
        $query=Goods::find();
        //提交表单的查询参数
        $model->search($query);
        //分页
        $pager=new Pagination([
            'totalCount'=>$query->count(),
            'pageSize'=>10
        ]);
        //查询语句
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        //显示页面
        return $this->render('index',['models'=>$models,'pager'=>$pager,'model'=>$model]);

    }
    //添加
    public function actionAdd()
    {
        $model = new Goods();
        $introModel = new GoodsIntro();
        if($model->load(\Yii::$app->request->post()) && $introModel->load(\Yii::$app->request->post())){
            //var_dump(11111);exit;

            if($model->validate() && $introModel->validate()){
                //* 处理sn
                 //* 自动生成sn,规则为年月日+今天的第几个商品,比如201704010001
                $day = date('Y-m-d');
                $goodsCount = GoodsDayCount::findOne(['day'=>$day]);
                if($goodsCount==null){
                    $goodsCount = new GoodsDayCount();
                    $goodsCount->day = $day;
                    $goodsCount->count = 0;
                    $goodsCount->save();
                }
                //$goodsCount;
                //字符串长度补全
                //substr('000'.($goodsCount->count+1),-4,4);
                $model->sn = date('Ymd').sprintf("%04d",$goodsCount->count+1);

                $model->save();
                $introModel->goods_id = $model->id;
                $introModel->save();
                $goodsCount->count++;
                $goodsCount->save();

                \Yii::$app->session->setFlash('success','商品添加成功,请添加商品相册');
                return $this->redirect(['goods/index','id'=>$model->id]);
            }
        }
        return $this->render('add',['model'=>$model,'introModel'=>$introModel]);
    }
    //修改
    public function actionEdit($id){
        $model = Goods::findOne(['id'=>$id]);
        $introModel = $model->goodsIntro;
        if($model->load(\Yii::$app->request->post()) && $introModel->load(\Yii::$app->request->post())) {

            if ($model->validate() && $introModel->validate()) {
                $model->save();$introModel->save();
                \Yii::$app->session->setFlash('success','商品修改成功');
                return $this->redirect(['goods/index']);
            }
        }

        return $this->render('add',['model'=>$model,'introModel'=>$introModel]);
    }
    //删除
    public function actionDel($id){
        $model = Goods::findOne(['id'=>$id])->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['index']);
    }

     // 商品相册

    public function actionGallery($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('商品不存在');
        }


        return $this->render('gallery',['goods'=>$goods]);

    }

    /*
     * AJAX删除图片
     */
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }

    }


    public function actions() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "http://admin.yii2shop.com",//图片访问路径前缀
                    "imagePathFormat" => "/upload/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
                ],
            ],
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,//如果文件已存在，是否覆盖
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },//文件的保存方式
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $goods_id = \Yii::$app->request->post('goods_id');
                    if($goods_id){
                        $model = new GoodsGallery();
                        $model->goods_id = $goods_id;
                        $model->path = $action->getWebUrl();
                        $model->save();
                        $action->output['fileUrl'] = $model->path;
                        $action->output['id'] = $model->id;
                    }else{
                        $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
                    }



//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"

                },
            ],
        ];
    }


    //预览商品信息
    public function actionView($id)
    {
        $model = Goods::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('view',['model'=>$model]);

    }
    //权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['goods/index','goods/add','goods/edit','goods/del'],
            ]
        ];
    }
}