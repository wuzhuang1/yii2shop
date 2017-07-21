
<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-21
 * Time: 15:46
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($models,'name');
echo $form->field($models,'parent_id');
echo $form->field($models,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();