<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use app\common\model\Order;
use app\common\model\Merchant as merchantModel;

class Dashboard extends Base
{

    public function index()
    {
        $merchant = session('merchant');
        $merchant_info = merchantModel::where(['id'=>$merchant['id']])->find();

        // 今日成交金额与笔数
        $today_deal_amount = Order::getOrderAmount($merchant['id'], 'today', [['status', '=', 2]]);
        $today_deal_count = Order::getOrderCount($merchant['id'], 'today', [['status', '=', 2]]);

        // 累计成交金额与笔数
        $all_deal_amount = Order::getOrderAmount($merchant['id'], 'all', [['status', '=', 2]]);
        $all_deal_count = Order::getOrderCount($merchant['id'], 'all', [['status', '=', 2]]);

        $echart_column = [];
        for ($i=0; $i<=date('H'); $i++) {
            $echart_column[] = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
        }

        $echart_data = [];
        for ($i=0; $i<=date('H'); $i++) {
            $echart_data[] = Order::getOrderAmount($merchant['id'], [date('Y-m-d 00:00:00'), date('Y-m-d ').str_pad($i, 2, '0', STR_PAD_LEFT).':00:00'], [['status', '=', 2]]);
        }

        return view('', [
            'merchant_info' => $merchant_info,
            'today_deal_amount' => $today_deal_amount,
            'today_deal_count' => $today_deal_count,
            'all_deal_amount' => $all_deal_amount,
            'all_deal_count' => $all_deal_count,
            'echart_column' => $echart_column,
            'echart_data' => $echart_data,
        ]);
    }

    
}
