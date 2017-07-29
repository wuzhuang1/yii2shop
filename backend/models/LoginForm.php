<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-24
 * Time: 22:17
 */
namespace backend\models;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $automatic;

    public function rules()
    //[['automatic'],'required']
    {
        return [
            [['username', 'password'], 'required'],
            ['automatic','boolean']

        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'automatic' => '是否自动登录'
        ];
    }

    public function Login()
    {
        $user = User::findOne(['username' => $this->username]);
        //判断用户是否存在
        if ($user) {
            //判断用户输入密码是否正确
            //var_dump($user->password_hash);
            //var_dump($this->password);exit;

            if (\Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                //判断用户是否开启自动登录
                //if($this->automatic){

                //}
                \Yii::$app->user->login($user,$this->automatic?3*24*3600:0);
                return true;
            } else {
                //密码错误.提示错误信息
                $this->addError('password', '密码错误');
            }
        } else {
            //用户不存在输出
            $this->addError('username', '用户名不存在');
        }
        return false;

    }
}