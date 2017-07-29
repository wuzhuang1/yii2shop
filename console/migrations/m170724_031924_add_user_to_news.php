<?php

use yii\db\Migration;

class m170724_031924_add_user_to_news extends Migration
{
    public function up()
    {
        $this->addColumn('user','last_login_time',$this->integer(12)->comment('最后登录时间'));
        $this->addColumn('user','last_login_ip',$this->integer(20)->comment('最后登录ip'));
    }

    public function down()
    {
        echo "m170724_031924_add_user_to_news cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
