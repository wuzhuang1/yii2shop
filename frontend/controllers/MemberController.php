<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\City;
use frontend\models\LoginFrom;
use frontend\models\Member;
use frontend\models\Order;
use yii\captcha\CaptchaAction;
use yii\web\Cookie;
use yii\web\Request;

class MemberController extends \yii\web\Controller
{
    public $layout=false;
    public $enableCsrfValidation=false;
    //注册
    public function actionRegist()
    {
        $model=new Member();
        $request=new Request();
        //if($request->isPost){}
        if($model->load($request->post()) && $model->validate()){
            //var_dump($model);exit;
            //使用哈希加密
            $password_hash = \Yii::$app->security->generatePasswordHash($model->password);
            //赋值
            $model->password_hash=$password_hash;
            $model->created_at=time();
            $model->status=1;
            //提交
            $model->save();
        }
        return $this->render('regist',['model'=>$model]);
    }
    //登录
    public function actionLogin(){
        //$member=new Member();
        $model=new LoginFrom();
        $request=new Request();
        //接收数据并验证
        if($model->load($request->post()) && $model->validate() && $model->login()){
            //提示信息
            \Yii::$app->session->setFlash('success','登录成功');
            //跳转页面
            return $this->redirect(['member/address']);
        }
        return $this->render('login',['model'=>$model]);
    }
    //添加并展示收货地址
    public function actionAddress(){
        $model=new Address();
        $address=$model->find()->all();
        //$city=new City();
        if($model->load(\yii::$app->request->post()) && $model->validate()){
            $sid=$model->getName($model->sid);
            $cid=$model->getName($model->cid);
            $xid=$model->getName($model->xid);
            $model->sid=$sid->name;
            $model->cid=$cid->name;
            $model->xid=$xid->name;
            $model->fid=\yii::$app->user->identity->id;
            //var_dump($model->fid);exit;
            if($model->status){
                $model->status=1;
            }else{
                $model->status=0;
            }
            $model->save();
            return $this->redirect(['member/address']);
        }
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }
    //删除收货地址
    public function actionDel($id){
        Address::findOne(['id'=>$id])->delete();
        return $this->redirect(['member/address']);
    }
    //修改收货地址
    public function actionEdit($id){
        $model=Address::findOne(['id'=>$id]);
        $address=$model->find()->all();
        if($model->load(\yii::$app->request->post()) && $model->validate()){
            if($model->status){
                $model->status=1;
            }else{
                $model->status=0;
            }
            $model->save();
            return $this->redirect(['member/address']);
        }
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }
    //三级联动
    public function actionCity(){
        $pid = isset($_GET['id'])?$_GET['id']-0:0;
        $city=City::find()->where(['parent_id'=>$pid])->asArray()->all();

        return json_encode($city);
    }
    //验证码
    public function actions()
    {
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                //验证码的长度
                'minLength'=>5,
                'maxLength'=>5,
            ]
        ];
    }
    //显示主页
    public function actionIndex(){
        $models=GoodsCategory::find()->all();
        return $this->render('index',['models'=>$models]);
    }
    //商品详情页
    public function actionGoods($id){
        $goods=Goods::findOne(['id'=>$id]);
        $goods_intro=GoodsIntro::findOne(['goods_id'=>5]);
        $goods_gallery=GoodsGallery::find()->where(['goods_id'=>5])->asArray()->all();
        return $this->render('goods',['goods'=>$goods,'goods_intro'=>$goods_intro,'goods_gallery'=>$goods_gallery]);
    }
    //商品列表
    public function actionList($id){
        $models=GoodsCategory::find()->where(['tree'=>1])->all();
        $category=GoodsCategory::findOne(['id'=>$id]);
        //var_dump($category->depth);exit;
        if($category->depth==2){
            //当id是三级分类是就直接查询goods表
            $goods=Goods::find()->where(['goods_category_id'=>$id])->asArray()->all();
        }else{
            //获取三级分类的id
            $ids=$category->leaves()->asArray()->column();
            //var_dump($ids);exit;
            $goods=Goods::find()->where(['in','goods_category_id',$ids])->asArray()->all();
        }
        return $this->render('list',['models'=>$models,'goods'=>$goods]);

    }
    //添加商品到购物车
    //将数据保存到cookie或者数据表
    public function actionShopVehicle($gid,$amount){
        //判断用户是否登陆
        if(\yii::$app->user->isGuest){
            //未登录用户
            //判断cookie是否有相同的gid
            //准备数据
            $cookies=\yii::$app->request->cookies;//读取cookie
            $cart=$cookies->get('cart');
            //判断是否有这个cookie
            if($cart==null){
                //没有就直接设置
                $carts=[$gid=>$amount];
            }else{
                //反序列化
                $carts = unserialize($cart->value);
                //var_dump($carts[5]);exit;
                //判断是否有值
                if(isset($carts[$gid])){
                    //如果有就直接累加
                    $carts[$gid] += $amount;
                }else{
                    //如果没有直接赋值
                    $carts[$gid] = $amount;
                }
            }
            //var_dump(serialize($cart));exit;
            //将数据保存到cookie
            $cookies=\yii::$app->response->cookies;//在yii里面response是写入
            $cookie=new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),//系列化
                'expire'=>7*24*3600+time()//设置过期时间
            ]);
            $cookies->add($cookie);
            //记住不能exit;不然cookie不会出来
            //var_dump(7*24*3600+time());
            //var_dump(serialize($cart));
        }else{
            //登陆用户
            $cart=new Cart();
            $carts=$cart->findOne(['gid'=>$gid]);
            //var_dump()
            //判断是否有值
            if($carts!==null){
                $carts->amount+=$amount;
                $carts->save();
            }else{
                //获取当前用户id
                //获取用户id
                $fid = \Yii::$app->user->identity->id;
                $cart->gid=$gid;
                $cart->amount=$amount;
                $cart->member_id=$fid;
                //将数据保存到数据库
                $cart->save();
            }
            //提示信息
            //跳转到购物车展示页面
        }
        return $this->redirect(['shop-index']);
    }
    //从cookie或者数据取出数据展示到页面
    public function actionShopIndex(){
        //判断用户是否登陆
        if(\yii::$app->user->isGuest){
            //未登录用户
            //取出cookie中的所有信息
            $cookies = \Yii::$app->request->cookies;
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [];
            }else{
                $carts = unserialize($cart->value);
            }
            //获取商品数据
            $models = Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();
        }else{
            //登陆用户
            //取出该用户所有购物商品信息
            $fid = \Yii::$app->user->identity->id;
            $cart=Cart::find()->where(['member_id'=>$fid])->asArray()->all();
            //var_dump($cart);
            $carts=[];
            $c=[];
            foreach ($cart as $a){
                $carts[$a['gid']]=$a['amount'];
                $c[]=$a['gid'];
            };
            //var_dump($carts);exit;
            //var_dump($c);
            /*foreach ($carts as $cc){
                var_dump($cc);
            }
            exit;*/
            $models = Goods::find()->where(['in','id',$c])->asArray()->all();
            //var_dump($models);exit;
        }
        return $this->render('flow1',['models'=>$models,'carts'=>$carts]);


    }
    //修改购物商品信息
    public function actionShopEdit(){
		$gid = \Yii::$app->request->post('gid');
        $amount = \Yii::$app->request->post('amount');
        //数据验证

        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$gid=>$amount];
            }else{
                $carts = unserialize($cart->value);
                if(isset($carts[$gid])){
                    //购物车中已经有该商品，更新数量
                    $carts[$gid] += $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$gid] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
            return 'success';
        }else{
			$carts=Cart::findOne(['gid'=>$gid]);
			if($carts!==null){
                $carts->amount+=$amount;
                $carts->save();
            }else{
                //获取当前用户id
                //获取用户id
                $fid = \Yii::$app->user->identity->id;
                $carts->gid=$gid;
                $carts->amount=$amount;
                $carts->member_id=$fid;
                //将数据保存到数据库
                $carts->save();
            }
		}
    }
    //删除商品
    public function actionShopDel($gid){
        if(\yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cart = $cookies->get('cart');
            $carts = unserialize($cart->value);
            var_dump($carts[$gid]);//exit;
            $carts[$gid] = 0;
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
            //return 'success';
        }else{
            Cart::findOne(['gid'=>$gid])->delete();
        }
        return $this->redirect('shop-index');
    }
    //订单
    public function actionOrder(){
        //判断用户是否登录
        if(\yii::$app->user->isGuest==false){
            $fid = \Yii::$app->user->id;
            //去查询收货人地址信息
            $addresss=Address::find()->where(['fid'=>$fid])->asArray()->all();
            //去购物车表根据用户ID查询表中数据
            $carts=Cart::find()->where(['member_id'=>$fid])->asArray()->all();
            //根据购物车的gid查询商品信息
            $a=[];
            $car=[];
            foreach ($carts as $cart){
                $a[]=$cart['gid'];
                $car[$cart['gid']]=$cart['amount'];

            }
            //echo $a[0];
            //var_dump($a);exit;
            $goodss=Goods::find()->where(['in','id',$a])->asArray()->all();
            $order=new Order();
            //查询所有支付方式
            $paymentmethods=$order->paymentmethod;
            //查询所有物流方式
            $expressmodes=$order->expressmode;
            if(\yii::$app->request->isPost) {//判断提交方式
                //var_dump($request);exit;
                $address_id = $_POST['address_id'];
                var_dump($address_id);exit;
            }
            //显示页面
            return $this->render('flow2',['addresss'=>$addresss,'car'=>$car,'goodss'=>$goodss,'paymentmethods'=>$paymentmethods,'expressmodes'=>$expressmodes]);
        }else{
            return $this->redirect('login');
        }
    }
    //短信测试
    public function actionTestSms()
    {
        //$className = 'Aliyun\Core\Profile\DefaultProfile';
        //$classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
        //$classFile = \Yii::getAlias('@Aliyun/Core/Profile/DefaultProfile.php');
        //var_dump($classFile);exit;

        /*// 加载区域结点配置
        Config::load();
        //此处需要替换成自己的AK信息
        $accessKeyId = "LTAIF4JlJQ0cGxTa";//参考本文档步骤2
        $accessKeySecret = "RtXBSHEEF4PzyCVrUtz2mn8FkmOT9B";//参考本文档步骤2
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";
        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new DefaultAcsClient($profile);
        $request = new SendSmsRequest;
        //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $request->setPhoneNumbers("15281015559");
        //必填-短信签名
        $request->setSignName("伍先生茶馆");
        //必填-短信模板Code
        $request->setTemplateCode("SMS_80145053");
        //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParam("{\"code\":\"12345\"}");
        //选填-发送短信流水号
        //$request->setOutId("1234");
        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        var_dump($acsResponse);*/
        //$sms = new AliyunSms();
        //$sms->setPhoneNumbers()->setSignName()->setTemplateCode()->send();
        //$code = rand(1000,9999);
        $code='1+1=?';
        $tel = '18357639145';
        $res = \Yii::$app->sms->setPhoneNumbers($tel)->setTemplateParam(['code'=>$code])->send();
        //将短信验证码保存redis（session，mysql）
        \Yii::$app->session->set('code_'.$tel,$code);
        //验证

        $code2 = \Yii::$app->session->get('code_'.$tel);
        if($code == $code2){

        }
        //\Yii::$app->session->set('tel',$tel);
        //var_dump($res);
        /*$demo = new SmsDemo(
            "yourAccessKeyId",
            "yourAccessKeySecret"
        );

        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "短信签名", // 短信签名
            "SMS_0000001", // 短信模板编号
            "12345678901", // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>"12345",
                "product"=>"dsd"
            ),
            "123"
        );
        print_r($response);*/
    }

    public function actionUser(){
        //可以通过 Yii::$app->user 获得一个 User实例，
        //$user = \Yii::$app->user;

        // 当前用户的身份实例。未认证用户则为 Null 。
        $identity = \Yii::$app->user->identity;
        var_dump($identity);

        // 当前用户的ID。 未认证用户则为 Null 。
        $id = \Yii::$app->user->id;
        var_dump($id);
        // 判断当前用户是否是游客（未认证的）
        $isGuest = \Yii::$app->user->isGuest;
        var_dump($isGuest);
        $fid = \Yii::$app->user->identity->id;
        var_dump($fid);
    }

}
