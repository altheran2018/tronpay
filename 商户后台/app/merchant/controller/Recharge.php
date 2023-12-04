<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use think\facade\Db;
use think\facade\Log;
use app\common\model\Merchant as merchantModel;
use app\common\model\AccountDetail as accountDetailModel;

class Recharge extends Base
{
    protected $no_need_login = ['notify'];

    public function index()
    {
        $merchant = session('merchant');

        if ($this->request->isAjax()) {
            $sort = input('sort') ?? 'id';
            $order = input('order') ?? 'desc';
            $offset = input('offset') ?? 0;
            $limit = input('limit') ?? 10;

            $condition = [];
            $condition[] = ['merchant_id', '=', $merchant['id']];
            $condition[] = ['type', '=', 'recharge'];

            $total = accountDetailModel::where($condition)->count();
            $rows = accountDetailModel::where($condition)->limit($offset, $limit)->order($sort.' '.$order)->select()->toArray();

            return json(['total'=>$total, 'rows'=>$rows]);
        }

        $merchant_info = merchantModel::find($merchant['id'])->toArray();

        return view('', [
            'merchant_info' => $merchant_info,
        ]);
    }

    public function detail()
    {
        $merchant = session('merchant');

        $ids = input('ids');
        $row = accountDetailModel::where(['id'=>$ids, 'merchant_id'=>$merchant['id']])->find();
        if ($row) $row = $row->toArray();

        if ($row) {
            return view('', [
                'row' => $row
            ]);
        }
    }

    public function pay(){
        $merchant = session('merchant');

        if ($this->request->isAjax()) {
            $merchant_id = 100001;
            $order_id = 'R'.date('YmdHis').mt_rand(1000, 9999);
            $product_name = '手续费充值';
            $user_flag = $merchant['id'];
            $currency = 'USD';
            $order_amount = input('amount');
            $amount = $order_amount * config('epusdt.cny_to_usd_rate');
            $notify_url = request()->domain().'/merchant/recharge/notify';
            $redirect_url = request()->domain().'/merchant/recharge/success';

            // 获取商户收款钱包地址
            $merchant_wallet_info = Db::name('wallet_address')->where(['merchant_id'=>$merchant_id, 'status'=>1])->find();
            
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
                    Db::table('orders')->where(['trade_id'=>$response['data']['trade_id']])->update(['merchant_id'=>$merchant_id, 'product_name'=>$product_name, 'user_flag'=>$user_flag, 'currency'=>$currency, 'order_amount'=>$order_amount, 'notify_url2'=>$notify_url, 'rate'=>0]);
                    
                    return json(['code'=>1, 'msg'=>'成功', 'data'=>$response['data']]);
                } else {
                    return json(['code'=>0, 'msg'=>$response['message']]);
                }
            }
        }

        return view('', [
            
        ]);
    }

    public function notify()
    {
        $params = file_get_contents('php://input');
        Log::record($params);

        $params = json_decode($params, true);
        if (!$params) exit;

        $order_info = Db::table('orders')->where(['order_id'=>$params['order_id']])->find();
        if ($order_info) {
            if ($order_info['callback_confirm'] == 1) {
                echo 'ok';
                exit;
            }

            Db::startTrans();
            try {
                $merchant_info = merchantModel::where(['id'=>$order_info['user_flag']])->lock(true)->find();

                $last_account_detail = accountDetailModel::where(['merchant_id'=>$order_info['user_flag']])->order('id desc')->find();

                //写入账户明细
                $insert_data = [];
                $insert_data['merchant_id'] = $order_info['user_flag'];
                $insert_data['type'] = 'recharge';
                $insert_data['change'] = $params['amount'];
                $insert_data['balance'] = $last_account_detail['balance'] + $params['amount'];
                accountDetailModel::create($insert_data);
                
                //更新商户余额
                $merchant_info->balance += $params['amount'];
                $merchant_info->save();

                Db::commit();

                echo 'ok';
                exit;
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }   
        }
    }

    public function success()
    {
        echo '充值成功，请关闭窗口';
    }

    
}
