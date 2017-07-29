<?php
/* @var $this yii\web\View */
?>
<h1>user/index</h1>
<?=\yii\bootstrap\Html::a('添加',['user/add'],['class'=>'btn btn-sm btn-info'])?>
<table class="table-responsive table-bordered table-view table">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>邮箱</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):;?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->username?></td>
        <td><?=$model->email?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['edit','id'=>$model->id],['class'=>'btn btn-sm btn-info'])?>
            <?=\yii\bootstrap\Html::a('删除',['del','id'=>$model->id],['class'=>'btn btn-sm btn-info'])?>
            <?=\yii\bootstrap\Html::a('修改密码',['dei','id'=>$model->id],['class'=>'btn btn-sm btn-info'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
