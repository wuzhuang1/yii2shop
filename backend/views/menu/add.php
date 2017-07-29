<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($models,'name');
echo $form->field($models,'fid')->dropDownList(\backend\models\Menu::getMenuOptions(),['prompt'=>'=请选择品牌=']);
echo $form->field($models,'url')->dropDownList(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getPermissions(),'name','name'),['prompt'=>'=请选择路由=']);
echo $form->field($models,'describe');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
