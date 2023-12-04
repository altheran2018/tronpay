<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use app\common\model\WalletAddress as walletAddressModel;

class Token extends Base
{
    protected $need_token = ['add'];

    public function index()
    {
        $merchant = session('merchant');

        if ($this->request->isAjax()) {
            $sort = input('sort') ?? 'id';
            $order = input('order') ?? 'desc';

            $condition = [];
            $condition[] = ['merchant_id', '=', $merchant['id']];
            $condition[] = ['status', '<>', -1];

            $total = walletAddressModel::where($condition)->count();
            $rows = walletAddressModel::where($condition)->order($sort.' '.$order)->select()->toArray();
            foreach ($rows as $key=>$val) {
                $rows[$key]['total_amount'] = walletAddressModel::getTotalAmount($merchant['id'], $val['wallet_address']);
                $last_trade_info = walletAddressModel::getLastTrade($merchant['id']);
                $rows[$key]['last_payment_at'] = $last_trade_info ? $last_trade_info['updated_at'] : '';
                $rows[$key]['ismenu'] = $val['status'];
            }

            return json(['total'=>$total, 'rows'=>$rows]);
        }

        return view('', [
            
        ]);
    }

    public function multi(){
        $merchant = session('merchant');

        $ids = input('ids');
        $params = input('params');
        parse_str($params,$values);

        if (isset($values['ismenu'])){
            if ($values['ismenu'] == 1) {
                walletAddressModel::where([['merchant_id', '=', $merchant['id']], ['status', '<>', -1]])->update(['status'=>0]);
            }

            walletAddressModel::where(['id'=>$ids, 'merchant_id'=>$merchant['id']])->update(['status'=>$values['ismenu']]);
        }
        

        return json(['code'=>1, 'msg'=>'更新成功']);
    }

    public function add(){
        if ($this->request->isAjax()) {
            $merchant = session('merchant');
            $row = input('row/a');

            $insert_data = [];
            $insert_data['merchant_id'] = $merchant['id'];
            $insert_data['wallet_address'] = $row['address'];
            walletAddressModel::create($insert_data);

            return json(['code'=>1, 'msg'=>'添加成功']);
        }

        return view('', [
            
        ]);
    }

    public function del(){
        $merchant = session('merchant');

        $ids = input('ids');

        walletAddressModel::where(['id'=>$ids, 'merchant_id'=>$merchant['id']])->update(['status'=>-1]);

        return json(['code'=>1, 'msg'=>'删除成功']);
    }

    
}
