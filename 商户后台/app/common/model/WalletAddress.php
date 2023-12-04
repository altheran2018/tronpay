<?php
namespace app\common\model;

use think\Model;
use app\common\model\Order as orderModel;

class WalletAddress extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
    protected $updateTime = 'update_time';

    public static function getTotalAmount($merchant_id, $token){
        return orderModel::where(['merchant_id'=>$merchant_id, 'token'=>$token, 'status'=>2])->sum('order_amount');
    }

    public static function getLastTrade($merchant_id){
        $last_trade = orderModel::where(['merchant_id'=>$merchant_id, 'status'=>2])->order('id desc')->find();
        
        if ($last_trade) {
            return $last_trade->toArray();
        } else {
            return false;
        }

    }

}
