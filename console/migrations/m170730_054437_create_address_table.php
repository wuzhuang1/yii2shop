<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170730_054437_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'fid'=>$this->integer()->comment('用户id'),
            'name'=>$this->string(20)->comment('收货人姓名'),
            'sid'=>$this->integer(20)->comment('省id'),
            'cid'=>$this->integer(20)->comment('市ID'),
            'xid'=>$this->integer(20)->comment('县ID'),
            'site'=>$this->string(255)->comment('详细地址'),
            'tal'=>$this->integer(20)->comment('电话号码'),
            'status'=>$this->integer(2)->comment('状态（1=》默认地址 0=》非默认）'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
