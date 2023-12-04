<?php
namespace app\merchant\validate;

use think\Validate;

class Recharge extends Validate
{
    protected $rule =   [
        'amount'               => ['require', 'min' => 0.01, 'max' => 99999, 'regex' => '/^\d+(.\d{1,2})?$/'],
    ];
    
    protected $message  =   [
        'amount.require'            =>'支付金额不能为空',
        'amount.min'                =>'支付金额最少为0.01',
        'amount.max'                =>'支付金额最大为99999',
        'amount.regex'              =>'支付金额有误(必须为整数或小数且小数位后不能超过2位)',
    ];

    protected $scene = [
        'index' => [''],
        'pay' => ['amount'],
        
    ]; 
    
}