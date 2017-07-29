<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170728_032648_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'fid'=>$this->integer()->comment('父id'),
            'name'=>$this->string(20)->comment('名称'),
            'describe'=>$this->string(255)->comment('描述'),
            'url'=>$this->string(255)->comment('指向路径'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
