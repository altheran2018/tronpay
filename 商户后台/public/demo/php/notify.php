<?php
/**
 * 支付成功异步回调接口
 *
 * 当用户支付成功后，支付平台会把订单支付信息异步请求到本接口(最多5次)
 *
 * @date 2023年9月1日
 * @copyright Tronpay
 */
require_once 'api.php';

/**
 * 回调数据
 * @var array(
 *       'trade_id' // 交易号
         'order_id', // 商户订单号
         'currency', // 订单币种
         'amount', // 订单金额
         'actual_amount', // 实际需要支付的usdt金额(USDT)
         'token', // 收款钱包
         'block_transaction_id', // 区块交易号
         'signature', // 签名
         'status', // 订单状态 1：等待支付，2：支付成功，3：已过期
 *   )
 */

$secret = 'A4mauT8hR6VdGr0qTH'; // 商户密钥

$data = $_POST;
if(!isset($data['signature']) || !isset($data['trade_id'])){
    echo 'failed';exit;
 }

// 验证签名
$signature = tronpayApi::tronpay_sign($data, $secret);
if($data['signature'] != $signature){
    //签名验证失败
    echo 'failed';exit;
}

//商户订单号
$order_id = $data['order_id'];

if($data['status'] == 2){
    /************商户业务处理******************/
    //TODO:此处处理订单业务逻辑,支付平台会多次调用本接口(防止网络异常导致回调失败等情况)
    //     请避免订单被二次更新而导致业务异常！！！
    //     if(订单未处理){
    //         处理订单....
    //      }

    //....
    //....
    /*************商户业务处理 END*****************/

}else{
    //处理未支付的情况

}

//以下是处理成功后输出，当支付平台接收到此消息后，将不再重复回调当前接口
echo 'ok';
exit;
?>
