<?php
declare (strict_types = 1);

namespace app\api\validate;

use think\Validate;

class Transaction extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' => ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'merchant_id'          => 'require|number',
        'order_id'             => 'require|max:32|alphaDash',
        'currency'             => 'require|in:CNY,USD',
        'amount'               => ['require', 'min' => 0.01, 'max' => 99999, 'regex' => '/^\d+(.\d{1,2})?$/'],
        'notify_url'           => 'url',
        'redirect_url'         => 'require|url',
        'signature'            => 'require|length:32',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'merchant_id.require'       =>'商户ID不能为空',
        'merchant_id.number'        =>'商户ID只能为数字',
        'order_id.require'          =>'商户订单号不能为空',
        'order_id.max'              =>'商户订单号最大长度为32位',
        'order_id.alphaDash'        =>'商户订单号只能为字母和数字,下划线_及破折号-的组合',
        'currency.require'          =>'充值币种不能为空',
        'currency.in'               =>'充值币种不合法',
        'amount.require'            =>'支付金额不能为空',
        'amount.min'                =>'支付金额最少为0.01',
        'amount.max'                =>'支付金额最大为99999',
        'amount.regex'              =>'支付金额有误(必须为整数或小数且小数位后不能超过2位)',
        //'notify_url.require'        =>'通知地址不能为空',
        'notify_url.url'            =>'通知地址格式有误',
        'redirect_url.require'      =>'跳转地址不能为空',
        'redirect_url.url'          =>'跳转地址格式有误',
        'signature.require'         =>'签名不能为空',
        'signature.length'          =>'签名长度有误',

    ];

    protected $scene = [
        'create' => ['merchant_id', 'order_id', 'currency', 'amount', 'notify_url', 'redirect_url', 'signature'],
        'notify' => [''],
        'notify2' => ['']

    ];
}
