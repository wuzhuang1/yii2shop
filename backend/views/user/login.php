<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-24
 * Time: 16:53
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'automatic')->checkbox([1=>'自动登录']);
echo \yii\bootstrap\Html::submitButton('登录',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();