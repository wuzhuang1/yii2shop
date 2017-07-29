<h1>菜单列表</h1>
<?=\yii\bootstrap\Html::a('添加',['menu/add'],['class'=>'btn btn-info'])?>
<table class="table">
    <tr>
        <th>ID</th>
        <th>父ID</th>
        <th>名称</th>
        <th>路径/路由</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):;?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->fid?></td>
        <td><?=$model->name?></td>
        <td><?=$model->url?></td>
        <td><?=$model->describe?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-info'])?>
            <?=\yii\bootstrap\Html::a('删除',['menu/del','id'=>$model->id],['class'=>'btn btn-info'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
