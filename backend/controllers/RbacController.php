<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

class RbacController extends \yii\web\Controller
{
    //public $authManager=\yii::$app->authManager;
    //权限添加
    public function actionAddPermission()
    {
        $model=new PermissionForm();
        $model->scenario=PermissionForm::SCENARIO_ADD;
        if($model->load(\yii::$app->request->post()) && $model->validate()){
            $authManager=\yii::$app->authManager;
            //创建一个权限
            $AddPermission=$authManager->createPermission($model->name);
            $AddPermission->description=$model->description;
            //将权限保存到数据库
            $authManager->add($AddPermission);
            \yii::$app->session->setFlash('success','权限添加成功');
            return $this->redirect(['index-permission']);
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //权限列表
    public function actionIndexPermission(){
        $authManager=\yii::$app->authManager;
        $models=$authManager->getPermissions();
        return $this->render('index-permission',['models'=>$models]);
    }
    //权限修改
    public function actionEditPermission($name)
    {
        $authManger=\yii::$app->authManager;
        $permission=$authManger->getPermission($name);
        if($permission==null){
            throw new NotFoundHttpException('权限不存在');
        }
        $model=new PermissionForm();
        if(\yii::$app->request->isPost){
            if($model->load(\yii::$app->request->post()) && $model->validate()){
                //赋值
                $permission->name=$model->name;
                $permission->description=$model->description;
                //更新权限
                $authManger->update($name,$permission);
                \yii::$app->session->setFlash('success','权限修改成功');
                return $this->redirect(['index-permission']);
            }
        }else{
            $model->name=$permission->name;
            $model->description=$permission->description;
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //权限的删除
    public function actionDelPermission($name){
        $authManger=\yii::$app->authManager;
        $permission=$authManger->getPermission($name);
        $authManger->remove($permission);
        return $this->redirect(['index-permission']);
    }
    //角色添加
    public function actionAddRole(){
        $model = new RoleForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //创建和保存角色
            $authManager = \Yii::$app->authManager;
            $role = $authManager->createRole($model->name);
            $role->description = $model->description;
            //保存角色
            $authManager->add($role);
            //给角色赋予权限
            //var_dump($model);exit;
            if(is_array($model->permissions)){
                //遍历权限
                foreach ($model->permissions as $permissionName){
                    //将权限转换成对象
                    $permission = $authManager->getPermission($permissionName);
                    //判断添加权限是否存在
                    if($permission){$authManager->addChild($role,$permission);}
                }
            }
            //提示信息
            \Yii::$app->session->setFlash('success','角色添加成功');
            return $this->redirect(['index-role']);
        }
        return $this->render('add-role',['model'=>$model]);
}
    //修改角色
    public function actionEditRole($name)
    {
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($name);
        $model = new RoleForm();
        //表单权限多选回显
        //获取角色的权限
        $permissions = $authManager->getPermissionsByRole($name);
        $model->name = $role->name;
        $model->description = $role->description;
        $model->permissions = ArrayHelper::map($permissions,'name','name');
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $role->description = $model->description;
            $authManager->update($name,$role);
            //给角色赋予权限
            $authManager->removeChildren($role);
            if(is_array($model->permissions)){
                foreach ($model->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    if($permission) $authManager->addChild($role,$permission);
                }
            }
            \Yii::$app->session->setFlash('success','角色修改成功');
            return $this->redirect(['index-role']);

        }

        return $this->render('add-role',['model'=>$model]);
    }
    //显示角色列表
    public function actionIndexRole()
    {
        $models = \Yii::$app->authManager->getRoles();
        return $this->render('index-role',['models'=>$models]);
    }
    //删除角色
    public function actionDelRole($name){
        $authManger=\yii::$app->authManager;
        $role=$authManger->getRole($name);
        //var_dump($role);exit;
        $authManger->remove($role);
        return $this->redirect(['index-role']);
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
