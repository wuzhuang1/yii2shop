<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    //添加品牌
    public function actionAdd()
    {
        $model = new Brand();
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
