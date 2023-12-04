<?php
namespace app\common\model;

use think\Model;

class Order extends Model
{
    protected $table = 'orders';

    public static function getOrderAmount($merchant_id, $period, $filter)
    {
        $condition = [];
        $condition[] = ['merchant_id', '=', $merchant_id];
        if (is_array($period)) {
            $condition[] = ['created_at', 'between', $period];
        } else if ($period == 'today') {
            $condition[] = ['created_at', 'between', [date('Y-m-d 00:00:00'), date('Y-m-d 00:00:00', strtotime('+1 day'))]];
        } else if ($period == 'yestoday') {
            $condition[] = ['created_at', 'between', [date('Y-m-d 00:00:00', strtotime('-1 day')), date('Y-m-d 00:00:00')]];
        }

        $condition = array_merge($condition, $filter);

        return self::where($condition)->sum('order_amount');
    }

    public static function getOrderCount($merchant_id, $period, $filter)
    {
        $condition = [];
        $condition[] = ['merchant_id', '=', $merchant_id];
        if (is_array($period)) {
            $condition[] = ['created_at', 'between', $period];
        } else if ($period == 'today') {
            $condition[] = ['created_at', 'between', [date('Y-m-d 00:00:00'), date('Y-m-d 00:00:00', strtotime('+1 day'))]];
        } else if ($period == 'yestoday') {
            $condition[] = ['created_at', 'between', [date('Y-m-d 00:00:00', strtotime('-1 day')), date('Y-m-d 00:00:00')]];
        }

        $condition = array_merge($condition, $filter);

        return self::where($condition)->count();
    }

}
