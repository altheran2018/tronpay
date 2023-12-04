<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use app\common\model\Order as orderModel;

class Order extends Base
{

    public function index()
    {
        $merchant = session('merchant');

        if ($this->request->isAjax()) {
            $sort = input('sort') ?? 'id';
            $order = input('order') ?? 'desc';
            $offset = input('offset') ?? 0;
            $limit = input('limit') ?? 10;
            $search = input('search') ?? '';
            $filter = input('filter') ?? '{}';
            $op = input('op') ?? '{}';

            $condition = [];
            $condition[] = ['merchant_id', '=', $merchant['id']];
            if ($search != '') $condition[] = ['order_id', 'like', '%'.$search.'%'];
            $filter = json_decode($filter, true);
            $op = json_decode($op, true);
            if (is_array($filter)) {
                foreach ($filter as $key=>$item) {
                    if ($op[$key] == '=') {
                        $condition[] = [$key, '=', $item];
                    } else if ($op[$key] == 'RANGE') {
                        $condition[] = [$key, 'between', explode(' - ', $item)];
                    }
                }
            }

            $total = orderModel::where($condition)->count();
            $rows = orderModel::where($condition)->limit($offset, $limit)->order($sort.' '.$order)->select()->toArray();

            return json(['total'=>$total, 'rows'=>$rows]);
        }

        return view('', [
            
        ]);
    }

    public function detail()
    {
        $merchant = session('merchant');

        $ids = input('ids');
        $row = orderModel::where(['id'=>$ids, 'merchant_id'=>$merchant['id']])->find();
        if ($row) $row = $row->toArray();

        if ($row) {
            return view('', [
                'row' => $row
            ]);
        }
    }

    public function update()
    {
        $merchant = session('merchant');
        $row = input('row/a');

        $order_info = orderModel::where(['id'=>$row['order_id'], 'merchant_id'=>$merchant['id']])->find();
        if (!$order_info) return json(['code'=>0, 'msg'=>'找不到该订单']);

        $order_info->status = $row['order_status'];
        $order_info->save();
        
        return json(['code'=>1, 'msg'=>'更新成功']);
    }

    public function statics()
    {
        $merchant = session('merchant');

        // 今日成交金额与笔数
        $today_deal_amount = orderModel::getOrderAmount($merchant['id'], 'today', [['status', '=', 2]]);
        $today_deal_count = orderModel::getOrderCount($merchant['id'], 'today', [['status', '=', 2]]);

        // 累计成交金额与笔数
        $all_deal_amount = orderModel::getOrderAmount($merchant['id'], 'all', [['status', '=', 2]]);
        $all_deal_count = orderModel::getOrderCount($merchant['id'], 'all', [['status', '=', 2]]);

        $echart_column = [];
        for ($i=0; $i<=date('H'); $i++) {
            $echart_column[] = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
        }

        $echart_data_amount_all = [];
        $echart_data_amount_complete = [];
        for ($i=0; $i<=date('H'); $i++) {
            $echart_data_amount_all[] = orderModel::getOrderAmount($merchant['id'], [date('Y-m-d 00:00:00'), date('Y-m-d ').str_pad($i, 2, '0', STR_PAD_LEFT).':00:00'], []);
            $echart_data_amount_complete[] = orderModel::getOrderAmount($merchant['id'], [date('Y-m-d 00:00:00'), date('Y-m-d ').str_pad($i, 2, '0', STR_PAD_LEFT).':00:00'], [['status', '=', 2]]);
        }

        $echart_data_count_all = [];
        $echart_data_count_complete = [];
        for ($i=0; $i<=date('H'); $i++) {
            $echart_data_count_all[] = orderModel::getOrderCount($merchant['id'], [date('Y-m-d 00:00:00'), date('Y-m-d ').str_pad($i, 2, '0', STR_PAD_LEFT).':00:00'], []);
            $echart_data_count_complete[] = orderModel::getOrderCount($merchant['id'], [date('Y-m-d 00:00:00'), date('Y-m-d ').str_pad($i, 2, '0', STR_PAD_LEFT).':00:00'], [['status', '=', 2]]);
        }

        return view('', [
            'today_deal_amount' => $today_deal_amount,
            'today_deal_count' => $today_deal_count,
            'all_deal_amount' => $all_deal_amount,
            'all_deal_count' => $all_deal_count,
            'echart_column' => $echart_column,
            'echart_data_amount_all' => $echart_data_amount_all,
            'echart_data_amount_complete' => $echart_data_amount_complete,
            'echart_data_count_all' => $echart_data_count_all,
            'echart_data_count_complete' => $echart_data_count_complete,
        ]);
    }

    
}
