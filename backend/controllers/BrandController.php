<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{
    public function actions() {
        return [
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
                'overwriteIfExist' => true,
                /*'format' => function (UploadAction $action) {
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
                },
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
                    $action->output['fileUrl'] = $action->getWebUrl();
                    //$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    //$action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
    public function actionQiniu(){
        $config = [
            'accessKey'=>'SwvTEUHPQQtPQhxbyngMEcB548qVJ7KPs7VVLiwZ',
            'secretKey'=>'Mjng0T3rB8BQfKUvxsqdPy4dbClrtmOPNnmqGMLY',
            'domain'=>'http://otbke7dfe.bkt.clouddn.com/',
            'bucket'=>'yii2shop',
            'area'=>Qiniu::AREA_HUADONG
        ];

        $qiniu = new Qiniu($config);
        $key = 'upload/e7/8f/e78f9492f1846ca65f048a0e26a47358c236686f.png';
        //将图片上传到七牛云
        $qiniu->uploadFile(\Yii::getAlias('@webroot').'/upload/e7/8f/e78f9492f1846ca65f048a0e26a47358c236686f.png',$key);
       //获取图片在七牛云的地址 
        $url = $qiniu->getLink($key);
        //var_dump($url);

    }





    //添加品牌
    public function actionAdd()
    {
        $model = new Brand();
        $request = new Request();
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //实例化文件上传对象
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            //验证数据
            if($model->validate()){
                //处理图片
                //有文件上传
                if($model->imgFile){
                    $d = \Yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    if(!is_dir($d)){
                        mkdir($d);
                    }
                    $fileName = '/upload/'.date('Ymd').'/'.uniqid().'.'.$model->imgFile->extension;
                    //$fileName = 'upload/'.uniqid().'.'.$model->imgFile->extension;
                    //move_uploaded_file()
                    //创建文件夹

                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);

                    $model->logo = $fileName;
                }

                //var_dump(2233);exit;

                $model->save(false);//默认情况下 保存是会调用validate方法  有验证码是，需要关闭验证
                return $this->redirect(['brand/index']);
                //var_dump($model->getErrors());exit;


                //保存并跳转
            }else{
                //验证失败 打印错误信息
                var_dump($model->getErrors());exit;
            }

        }

        return $this->render('add',['model'=>$model]);
    }


    public function actionIndex()
    {
        $models=Brand::find()->where(['>','status',-1]);
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
    //修改
    public function actionEdit($id){
        $model = Brand::findOne(['id'=>$id]);
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            //实例化文件上传对象
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            //验证数据
            if($model->validate()){
                //处理图片
                //有文件上传
                if($model->imgFile){
                    $d = \Yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    if(!is_dir($d)){
                        mkdir($d);
                    }
                    $fileName = '/upload/'.date('Ymd').'/'.uniqid().'.'.$model->imgFile->extension;
                    //$fileName = 'upload/'.uniqid().'.'.$model->imgFile->extension;
                    //move_uploaded_file()
                    //创建文件夹

                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);

                    $model->logo = $fileName;
                }

                //var_dump(2233);exit;

                $model->save(false);//默认情况下 保存是会调用validate方法  有验证码是，需要关闭验证
                return $this->redirect(['brand/index']);
                //var_dump($model->getErrors());exit;


                //保存并跳转
            }else{
                //验证失败 打印错误信息
                var_dump($model->getErrors());exit;
            }

        }

        return $this->render('add',['model'=>$model]);
    }

    public function actionDel($id){

    }
}
