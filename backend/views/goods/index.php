<?=\yii\bootstrap\Html::a('添加',['goods/add'],['class'=>'btn btn-sm btn-info'])?>
<table class="table">
    <tr>
        <th>id</th>
        <th>商品名称</th>
        <th>货号</th>
        <th>LOGO</th>
        <th>商品分类</th>
        <th>品牌分类</th>
        <th>市场价格</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>状态</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>浏览次数</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):;?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->name?></td>
        <td><?=$model->sn?></td>
        <td><?=$model->logo?></td>
        <td><?=$model->id?></td>
        <td><?=$model->id?></td>
        <td><?=$model->market_price?></td>
        <td><?=$model->shop_price?></td>
        <td><?=$model->stock?></td>
        <td><?=$model->is_on_sale?'是':'否'?></td>
        <td><?=$model->status?'正常':'回手站'?></td>
        <td><?=$model->sort?></td>
        <td><?=date('Y-m-d H:i:s',$model->create)?></td>
        <td><?=$model->view_times?></td>
        <td>
            <?=\yii\bootstrap\Html::a('查看',['goods/check'],['class'=>'btn btn-sm btn-info'])?>
            <?=\yii\bootstrap\Html::a('修改',['goods/edit'],['class'=>'btn btn-sm btn-info'])?>
            <?=\yii\bootstrap\Html::a('删除',['goods/del'],['class'=>'btn btn-sm btn-info'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>