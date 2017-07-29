<h1>角色列表</h1>
<?=\yii\bootstrap\Html::a('添加',['add-role'],['class'=>'btn btn-info'])?>
<table class="table">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['edit-role','name'=>$model->name],['class'=>'btn btn-info'])?>
                <?=\yii\bootstrap\Html::a('删除',['del-role','name'=>$model->name],['class'=>'btn btn-info'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>