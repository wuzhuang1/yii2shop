<?php

namespace frontend\models;

use \yii\db\ActiveRecord;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property double $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends ActiveRecord
{
    //支付方式
    public $paymentmethod=[
        1=>['name'=>'在线支付','explain'=>'即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        2=>['name'=>'货到付款','explain'=>'送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        3=>['name'=>'上门自取','explain'=>'自提时付款，支持现金、POS刷卡、支票支付'],
        4=>['name'=>'邮局汇款','explain'=>'通过快钱平台收款 汇款后1-3个工作日到账']
    ];
    //送货方式
    public $expressmode=[
        1=>['name'=>'顺丰','price'=>40.00,'explain'=>'速度快，服务好，安全性高'],
        2=>['name'=>'圆通','price'=>30.00,'explain'=>'速度快，服务好，安全性一般'],
        3=>['name'=>'EMS','price'=>20.00,'explain'=>'速度一般，服务一般，安全性高'],
        4=>['name'=>'邮政','price'=>10.00,'explain'=>'速度一般，服务一般，安全性一般'],
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           //[['expressmode','address_id','paymentmethod'],'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式id',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态（0已取消1待付款2待发货3待收货4完成）',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
        ];
    }
}
