<?php
/**
 * 创建订单
 * @date 2023年9月1日
 * @copyright Tronpay
 */
require_once 'api.php';
$order_id = date('YmdHis').mt_rand(1000, 9999); // 商户订单号
$merchant_id = '100001'; // 商户ID
$secret = 'A4mauT8hR6VdGr0qTH'; // 商户密钥

$amount = $_REQUEST['amount'];

$data = array(
    'merchant_id'    => $merchant_id,
    'order_id'       => $order_id,
    'currency'       => 'USD', // 订单币种 人民币为CNY 美元为USD
    'amount'         => $amount, //订单金额 小数点保留后2位，最少0.01
    'notify_url'     => 'http://example.com/pay/notify', // 回调地址
    'redirect_url'   => 'http://example.com/pay/redirect', // 跳转地址
);

// 生成签名
$data['signature'] = tronpayApi::tronpay_sign($data, $secret);

$url = 'https://pro.tronpay.co/api/transaction/create';

try {
    $response = tronpayApi::http_post($url, json_encode($data));
    /**
     * 支付回调数据
     * @var array(
     *      trade_id, // 支付系统交易号
     *      payment_url // 支付跳转地址
     *  )
     */
    $result = $response ? json_decode($response,true) : null;
    if(!$result){
        throw new Exception('Internal server error', 500);
    }

    if($result['status_code'] != 200){
        throw new Exception($result['message'], $result['status_code']);
    }

    $payment_url = $result['data']['payment_url'];
    header("Location: $payment_url");
    exit;
} catch (Exception $e) {
    echo "errcode:{$e->getCode()},errmsg:{$e->getMessage()}";
    // TODO:创建订单调用异常的情况

}
?>
