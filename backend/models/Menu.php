<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property integer $fid
 * @property string $name
 * @property string $describe
 * @property string $url
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fid','name'],'required'],
            [['fid'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['describe', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fid' => '分类',
            'name' => '名称',
            'describe' => '描述',
            'url' => '路径',
        ];
    }
    //查询顶级分类
    public static function getMenuOptions(){
        return ArrayHelper::merge([0=>'顶级菜单'],ArrayHelper::map(Menu::find()->where(['=','fid',0])->asArray()->all(),'id','name'));
    }
    //获取子菜单  Menu[]
    public function getChildren()
    {
        return $this->hasMany(self::className(),['fid'=>'id']);
    }
}
