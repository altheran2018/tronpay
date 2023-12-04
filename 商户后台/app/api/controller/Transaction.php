<?php
namespace app\api\controller;

use app\ApiErrorDesc;
use app\BaseController;
use think\facade\Db;
use think\facade\Log;
use app\common\model\Merchant as merchantModel;
use app\common\model\AccountDetail as accountDetailModel;

class Transaction extends BaseController
{
    public function create()
    {
        $merchant_id = input('merchant_id');
        $order_id = input('order_id');
        $product_name = input('product_name');
        $user_flag = input('user_flag');
        $currency = input('currency');
        $order_amount = $amount = input('amount');
        if ($currency == 'USD') {
            $amount = $order_amount * config('epusdt.cny_to_usd_rate');
        } 
        $notify_url = urldecode(input('notify_url'));
        $redirect_url = urldecode(input('redirect_url'));
        $signature = input('signature');

        //商户信息
        $merchant_info = Db::name('merchant')->find($merchant_id);
        if (!$merchant_info) abort(ApiErrorDesc::TRANSACTION['MERCHANT_NOT_EXIST']); // 商户不存在
        if ($merchant_info['status'] == 0) abort(ApiErrorDesc::TRANSACTION['MERCHANT_NOT_AUDIT']); // 商户未通过审核
        if ($merchant_info['status'] == -1) abort(ApiErrorDesc::TRANSACTION['MERCHANT_DENYED']); // 商户被禁用
        if ($merchant_info['balance'] < 0) abort(ApiErrorDesc::TRANSACTION['MERCHANT_BALANCE_NOT_ENOUGH']); // 商户余额不足

        $merchant_param_info = Db::name('merchant_param')->where(['merchant_id'=>$merchant_id])->find();
        if (!$merchant_param_info) abort(ApiErrorDesc::TRANSACTION['MERCHANT_PARAM_INVALID']); // 商户配置缺失

        // 验证签名
        $params = input('post.');
        $tronpay_sign = tronpaySign($params, $merchant_param_info['secret']);

        if ($signature != $tronpay_sign) abort(ApiErrorDesc::TRANSACTION['SIGNATURE_ERROR']); // 签名错误

        // 通知地址如果为空，则取商户设置中的通知地址
        if ($notify_url == '') $notify_url = $merchant_param_info['notify_url'];

        // 获取商户收款钱包地址
        $merchant_wallet_info = Db::name('wallet_address')->where(['merchant_id'=>$merchant_id, 'status'=>1])->find();
        if (!$merchant_wallet_info) abort(ApiErrorDesc::TRANSACTION['MERCHANT_WALLET_ADDRESS_INVALID']); // 商户没有有效的收款钱包地址

        $exist = Db::table('orders')->where(['order_id'=>$order_id])->find();
        if ($exist) abort(ApiErrorDesc::TRANSACTION['ORDER_ALREADY_EXIST']); // 订单交易已存在

        // 变更wallet_address中的地址为当前商户的钱包地址
        Db::table('wallet_address')->where(['id'=>1])->update(['token'=>$merchant_wallet_info['wallet_address']]);
        Db::table('wallet_address')->where(['id'=>1])->lock(true)->find();

        // 往EPUSDT接口正式提交订单
        $e_notify_url = request()->domain().'/api/transaction/notify';
        $post_data = json_decode('{
            "order_id": "'.$order_id.'",
            "amount": '.$amount.',
            "notify_url": "'.$e_notify_url.'",
            "redirect_url": "'.$redirect_url.'"
        }', true);
        $post_data['signature'] = tronpaySign($post_data, config('epusdt.api_secret'));

        $response = httpPost(config('epusdt.api_url'), json_encode($post_data));
        if (!$response) {
            abort(ApiErrorDesc::COMMON['CURL_ERROR']);
        } else {
            if ($response['status_code'] == 200) {
                Db::table('orders')->where(['trade_id'=>$response['data']['trade_id']])->update(['merchant_id'=>$merchant_id, 'product_name'=>$product_name, 'user_flag'=>$user_flag, 'currency'=>$currency, 'order_amount'=>$order_amount, 'notify_url2'=>$notify_url,'rate'=>$merchant_info['rate']]);
                
                return $this->output('订单创建成功', $response['data']);
            } else {
                abort([ApiErrorDesc::COMMON['EPUSDT_ERROR'][0], $response['message']]);
            }
        }
    }

    public function notify()
    {
        $params = file_get_contents('php://input');
        Log::record($params);

        $params = json_decode($params, true);

        $order_info = Db::table('orders')->where(['order_id'=>$params['order_id']])->find();
        if ($order_info) {
            // 扣除手续费 start
            if ($order_info['rate'] > 0 && $order_info['charged'] == 0) {
                Db::startTrans();
                try {
                    $amount = $order_info['order_amount'];
                    if ($order_info['currency'] == 'CNY') $amount = $amount * config('epusdt.cny_to_usd_rate');
                    $amount = round($amount * ($order_info['rate'] / 100), 4);

                    $merchant_info = merchantModel::where(['id'=>$order_info['merchant_id']])->lock(true)->find();

                    $last_account_detail = accountDetailModel::where(['merchant_id'=>$order_info['merchant_id']])->order('id desc')->find();

                    //写入账户明细
                    $insert_data = [];
                    $insert_data['merchant_id'] = $order_info['merchant_id'];
                    $insert_data['type'] = 'trade';
                    $insert_data['relate_id'] = $order_info['id'];
                    $insert_data['change'] = -$amount;
                    $insert_data['balance'] = $last_account_detail['balance'] - $amount;
                    accountDetailModel::create($insert_data);
                    
                    //更新商户余额
                    $merchant_info->balance -= $amount;
                    $merchant_info->save();

                    Db::table('orders')->where(['order_id'=>$params['order_id']])->update(['charged'=>1]);

                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                } 
            }
            // 扣除手续费 end

            $merchant_param_info = Db::name('merchant_param')->where(['merchant_id'=>$order_info['merchant_id']])->find();

            $params['currency'] = $order_info['currency'];
            $params['amount'] = $order_info['order_amount'];
            $params['block_transaction_id'] = $order_info['block_transaction_id'];
            $params['signature'] = tronpaySign($params, $merchant_param_info['secret']);
            //md5('actual_amount='.$order_info['actual_amount'].'&amount='.$order_info['order_amount'].'&block_transaction_id='.$order_info['block_transaction_id'].'&currency='.$order_info['currency'].'&order_id='.$order_info['order_id'].'&status='.$order_info['status'].'&token='.$order_info['token'].'&trade_id='.$order_info['trade_id'].$merchant_param_info['secret']);

            $response = httpPost($order_info['notify_url2'], json_encode($params), false);
            echo $response;
            exit;
        }
    }

    public function notify2()
    {
        $params = '{"trade_id":"202310101696921161558447","order_id":"R202310101459213395","amount":0.07,"actual_amount":0.01,"token":"TGPzzSDzuWchyxEGRgBGuL2ZUm7yJMNXM8","block_transaction_id":"","signature":"c525123a96f81c7b1748d0eda51af963","status":2}';

        $params = json_decode($params, true);

        $order_info = Db::table('orders')->where(['order_id'=>$params['order_id']])->find();
        if ($order_info) {
            // 扣除手续费 start
            if ($order_info['rate'] > 0 && $order_info['charged'] == 0) {
                Db::startTrans();
                try {
                    $amount = $order_info['order_amount'];
                    if ($order_info['currency'] == 'CNY') $amount = $amount * config('epusdt.cny_to_usd_rate');
                    $amount = round($amount * ($order_info['rate'] / 100), 2);

                    $merchant_info = merchantModel::where(['id'=>$order_info['merchant_id']])->lock(true)->find();

                    $last_account_detail = accountDetailModel::where(['merchant_id'=>$order_info['merchant_id']])->order('id desc')->find();

                    //写入账户明细
                    $insert_data = [];
                    $insert_data['merchant_id'] = $order_info['merchant_id'];
                    $insert_data['type'] = 'trade';
                    $insert_data['relate_id'] = $order_info['id'];
                    $insert_data['change'] = -$amount;
                    $insert_data['balance'] = $last_account_detail['balance'] - $amount;
                    accountDetailModel::create($insert_data);
                    
                    //更新商户余额
                    $merchant_info->balance -= $amount;
                    $merchant_info->save();

                    Db::table('orders')->where(['order_id'=>$params['order_id']])->update(['charged'=>1]);

                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                } 
            }
            // 扣除手续费 end

            $merchant_param_info = Db::name('merchant_param')->where(['merchant_id'=>$order_info['merchant_id']])->find();

            $params['currency'] = $order_info['currency'];
            $params['amount'] = $order_info['order_amount'];
            $params['block_transaction_id'] = $order_info['block_transaction_id'];
            $params['signature'] = tronpaySign($params, $merchant_param_info['secret']);
            //md5('actual_amount='.$order_info['actual_amount'].'&amount='.$order_info['order_amount'].'&block_transaction_id='.$order_info['block_transaction_id'].'&currency='.$order_info['currency'].'&order_id='.$order_info['order_id'].'&status='.$order_info['status'].'&token='.$order_info['token'].'&trade_id='.$order_info['trade_id'].$merchant_param_info['secret']);

            $response = httpPost($order_info['notify_url2'], json_encode($params), false);
            echo $response;
            exit;
        }
    }
}
