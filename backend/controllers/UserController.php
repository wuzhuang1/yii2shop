<?php

namespace backend\controllers;

use backend\models\Loginx;
use backend\models\User;
use yii\web\Request;
use backend\models\LoginForm;

class UserController extends \yii\web\Controller
{
    //用户列表
    public function actionIndex()
    {
        $models=User::find()->all();
        return $this->render('index',['models'=>$models]);
    }
    //添加用户
    /*public function actionAdd()
    {
        $models = new User();
        //$model->scenario = User::SCENARIO_ADD;//指定当前期场景

        if($models->load(\Yii::$app->request->post()) && $models->validate()){
            $hash_password = \Yii::$app->getSecurity()->generatePasswordHash('$models->password_hash');
            $models->password_hash = $hash_password;
            $models->created_at=time();
            //$models->auth_key=\yii::$app->security->generateRandomString();
            $models->save();
            $authManager = \Yii::$app->authManager;
            //$authManager->revokeAll($this->id);
            if(is_array($models->roles)){
                foreach ($models->roles as $roleName){
                    $role = $authManager->getRole($roleName);
                    if($role) $authManager->assign($role,$models->id);
                }
            }
            \Yii::$app->session->setFlash('success','用户添加成功');
            return $this->redirect(['index']);
        }

        return $this->render('add',['models'=>$models]);
    }*/
    public function actionAdd(){
        //实列化user表模型
        $models=new User();
        $request=new Request();
        //判断提交方式
        if($request->isPost){
            //接收数据
            $models->load($request->post());
            //var_dump(11);
            //验证
            if($models->validate()) {
                //var_dump(22);
                //判断两次输入的密码是否一致
                if ($models->password_hash == $models->password) {

                    //使用哈希加密
                    $hash_password = \Yii::$app->security->generatePasswordHash($models->password_hash);
                    //这里注意‘’
                    //$ip = \Yii::$app->request->userIP;
                    //赋值
                    $models->password_hash = $hash_password;
                    $models->created_at=time();

                    $models->auth_key=\yii::$app->security->generateRandomString();
                    //提交
                    $models->save();
                    $authManager = \Yii::$app->authManager;
                    //$authManager->revokeAll($this->id);
                    if(is_array($models->roles)){
                        foreach ($models->roles as $roleName){
                            $role = $authManager->getRole($roleName);
                            if($role) $authManager->assign($role,$models->id);
                        }
                    }
                    //跳转
                    return $this->redirect('index');
                }else{
                    //return $this->render('index');
                }

            }else{
                var_dump($models->getErrors());exit;
            }
        }
        return $this->render('add',['models'=>$models]);
    }
    //修改
    public function actionEdit($id){
        //实列化user表模型
        $models = User::findOne(['id'=>$id]);
        $request=new Request();
        //判断提交方式
        if($request->isPost){
            //接收数据
            $models->load($request->post());
            //var_dump(11);
            //验证
            if($models->validate()) {
                //var_dump(22);
                //判断两次输入的密码是否一致
                if ($models->password_hash == $models->password) {
                    //使用哈希加密
                    $hash_password = \Yii::$app->getSecurity()->generatePasswordHash('$models->password_hash');
                    //$ip = \Yii::$app->request->userIP;
                    //赋值
                    $models->password_hash = $hash_password;
                    //提交
                    $models->save();
                    $authManager = \Yii::$app->authManager;
                    $authManager->revokeAll($this->id);
                    if(is_array($models->roles)){
                        foreach ($models->roles as $roleName){
                            $role = $authManager->getRole($roleName);
                            if($role) $authManager->assign($role,$models->id);
                        }
                    }
                    //跳转
                    return $this->redirect('index');
                }else{
                    //return $this->render('index');
                }

            }else{
                var_dump($models->getErrors());exit;
            }
        }

        return $this->render('add',['models'=>$models]);
    }
    //删除
    public function actionDel($id){
        User::findOne(['id'=>$id])->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['index']);

    }
    //登录
    public function actionLogin(){
        //实列化loginform模型
        $model=new LoginForm();
        $request=new Request();
        //判断页面提交方式
        if($request->isPost){
            //把数据传到loginform中处理
            $model->load($request->post());
            //验证数据是否符合规则
            //验证用户名与密码
            //var_dump($model);exit;
            if($model->validate() && $model->login()){
                //登录成功
                //把数据放到session中
                \Yii::$app->session->setFlash('success','登录成功');
                //跳转页面
                return $this->redirect(['user/index']);
            }
        }
        //展示页面
        return $this->render('login',['model'=>$model]);
    }
    //查看登录状态
    public function actionUser(){
        //是否为登陆状态
        $isGuest = \Yii::$app->user->isGuest;
        var_dump($isGuest);
    }
    //注销登录
    public function actionLogout(){
        //注销登陆
        \yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }
    //修改密码
    public function actionDei($id){
        if(!\Yii::$app->user->isGuest){
            $model=new Loginx();
            $user=User::findOne(['id'=>$id]);
            $request=new Request();
            if ($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    if (\Yii::$app->security->validatePassword($model->password_l, $user->password_hash)){
                       if($model->password_l!==$model->password_x){
                           //哈希加密
                           $password_hash = \Yii::$app->security->generatePasswordHash($model->password_x);
                           $user->password_hash=$password_hash;
                           $user->save();
                           return $this->redirect(['user/index']);
                       }
                    }
                }
            }
            return $this->render('dei',['model'=>$model]);
        }else{
            return $this->redirect(['user/login']);
        }
    }
    //权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['user/index','user/add','user/edit','user/del'],
            ]
        ];
    }
}
