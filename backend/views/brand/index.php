<?=\yii\bootstrap\Html::a('添加',['goods/add'],['class'=>'btn btn-sm btn-success'])?>
<table class="table">
    <tr>
        <th>id</th>
        <th>名称</th>
        <th>简介</th>
        <th>图片</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $row):;?>
    <tr>
        <td><?=$row->id?></td>
        <td><?=$row->name?></td>
        <td><?=$row->intro?></td>
        <td><?=$row->id?></td>
        <td><?=$row->status?'显示':'隐藏'?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$row->id],['class'=>'btn btn-sm btn-success'])?>
            <?=\yii\bootstrap\Html::a('删除',['brand/del','id'=>$row->id],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);
