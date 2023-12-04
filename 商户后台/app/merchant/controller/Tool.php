<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use think\facade\Db;
use app\common\model\Merchant as merchantModel;
use GuzzleHttp\Client;

class Tool extends Base
{
    public function gentoken()
    {
        //$uri = 'https://api.trongrid.io';// mainnet
        $uri = 'https://api.shasta.trongrid.io';// shasta testnet
        $api = new \Tron\Api(new Client(['base_uri' => $uri]));

        $trxWallet = new \Tron\TRX($api);
        $addressData = $trxWallet->generateAddress();
        // $addressData->privateKey
        // $addressData->address

        $config = [
            'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',// USDT TRC20
            'decimals' => 6,
        ];
        $trc20Wallet = new \Tron\TRC20($api, $config);
        $addressData = $trc20Wallet->generateAddress();
    }

    public function paytest()
    {
        $merchant = session('merchant');
        $merchant_info = merchantModel::find($merchant['id'])->toArray();

        if ($this->request->isAjax()) {
            $order_id = 'T'.date('YmdHis').mt_rand(1000, 9999);
            $product_name = '订单测试';
            $user_flag = '';
            $currency = input('currency');
            $order_amount = $amount = input('amount');
            if ($currency == 'USD') {
                $amount = $order_amount * config('epusdt.cny_to_usd_rate');
            }
            $notify_url = urldecode(input('notify_url'));
            $redirect_url = urldecode(input('redirect_url'));

            // 获取商户收款钱包地址
            $merchant_wallet_info = Db::name('wallet_address')->where(['merchant_id'=>$merchant_info['id'], 'status'=>1])->find();
            if (!$merchant_wallet_info) return json(['code'=>0, 'msg'=>'商户没有有效的收款钱包地址']);
            
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
                return json(['code'=>0, 'msg'=>'服务间调用出错']);
            } else {
                if ($response['status_code'] == 200) {
                    Db::table('orders')->where(['trade_id'=>$response['data']['trade_id']])->update(['merchant_id'=>$merchant_info['id'], 'product_name'=>$product_name, 'user_flag'=>$user_flag, 'currency'=>$currency, 'order_amount'=>$order_amount,'notify_url2'=>$notify_url, 'rate'=>$merchant_info['rate']]);
                    
                    return json(['code'=>1, 'msg'=>'成功', 'data'=>$response['data']]);
                } else {
                    return json(['code'=>0, 'msg'=>$response['message']]);
                }
            }
        }

        return view('', [
            
        ]);
    }

    public function plugin()
    {
        if ($this->request->isAjax()) {
            echo '{
                "total":1,
                "rows":[
                    {
                        "id":1,
                        "plugin_name":"PHP DEMO",
                        "author":"Tronpay",
                        "price":"免费",
                        "version":"1.0.0",
                        "add_time":"2023-06-26 10:55:03",
                        "name":"download",
                        "download_url":"/demo/php.zip"
                    }
                ]
            }';
            exit;
        }

        return view('', [
            
        ]);
    }

    
}
