<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use app\common\model\Merchant as merchantModel;
use app\common\model\AccountDetail as accountDetailModel;
use app\common\model\Order as orderModel;

class Bill extends Base
{
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
            $condition[] = ['type', '=', 'trade'];

            $total = accountDetailModel::where($condition)->count();
            $rows = accountDetailModel::where($condition)->limit($offset, $limit)->order($sort.' '.$order)->select()->toArray();
            foreach ($rows as $key=>$val) {
                $order_info = orderModel::find($val['relate_id'])->toArray();
                if ($order_info) {
                    $rows[$key]['order_amount'] = $order_info['order_amount'].'('.$order_info['currency'].')';
                    $rows[$key]['rate'] = $order_info['rate'];
                }
            }

            return json(['total'=>$total, 'rows'=>$rows]);
        }

        $merchant_info = merchantModel::find($merchant['id'])->toArray();

        return view('', [
            'merchant_info' => $merchant_info,
        ]);
    }

    
}
