<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m170802_142300_create_cart_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'gid'=>$this->integer(10)->comment('商品id'),
            'amount'=>$this->integer(10)->comment('商品数量'),
            'member_id'=>$this->integer(10)->comment('用户id'),
            /*goods_id	int	商品id
            amount	int	商品数量
            member_id	int	用户id*/
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');
    }
}
