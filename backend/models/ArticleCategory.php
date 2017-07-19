<?php
/**
 * Created by PhpStorm.
 * User: 升值中
 * Date: 2017-07-19
 * Time: 15:17
 */
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord
{
    public static function getStatusOptions($hidden_del = true)
    {
        $options = [
            -1 => '删除', 0 => '隐藏', 1 => '正常'
        ];
        if ($hidden_del) {
            unset($options['-1']);
        }
        return $options;
    }
    public static function tableName()
    {
        //注意事项
        return 'article_category';
    }
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required'],
            //指定上传文件的验证规则 extensions文件的扩展名（开启php fileinfo扩展）
            // skipOnEmpty 为空跳过该验证
            //['imgFile','file','extensions'=>['jpg','png','gif']],
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            //[['logo'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            //'logo' => 'LOGO',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}