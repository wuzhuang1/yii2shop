<h1>权限列表</h1>
<?=\yii\bootstrap\Html::a('添加权限',['add-permission'],['class'=>'btn btn-success'])?>
<table class="table">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):;?>
    <tr>
        <td><?=$model->name?></td>
        <td><?=$model->description?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改权限',['edit-permission','name'=>$model->name],['class'=>'btn btn-success'])?>
            <?=\yii\bootstrap\Html::a('删除权限',['del-permission','name'=>$model->name],['class'=>'btn btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>