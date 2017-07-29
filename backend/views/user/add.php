<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-24
 * Time: 11:57
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($models,'username');
echo $form->field($models,'password_hash')->passwordInput();
echo $form->field($models,'password')->passwordInput();
echo $form->field($models,'email');
echo $form->field($models,'roles',['inline'=>true])->checkboxList(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(),'name','description'));
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();