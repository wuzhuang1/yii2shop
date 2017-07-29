<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-25
 * Time: 16:52
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'password_l')->passwordInput();
echo $form->field($model,'password_x')->passwordInput();
echo $form->field($model,'password_z')->passwordInput();
echo \yii\bootstrap\Html::submitButton('确认修改',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();