<?php
namespace app;


class ApiErrorDesc
{
    /**
     * error info const array
     */

    /**
     * API通用错误码
     */
    const COMMON = [
        'MYSQL_HANDLE_ERROR'  => [201, 'MYSQL数据操作错误'],
        'CURL_ERROR'          => [202, '服务间调用出错'],
        'EPUSDT_ERROR'        => [203, 'EPUSDT_ERROR'],
    ];

    const TRANSACTION = [
        'MERCHANT_NOT_EXIST'                 => [10001, '该商户不存在'],
        'MERCHANT_NOT_AUDIT'                 => [10002, '该商户尚未通过审核'],
        'MERCHANT_DENYED'                    => [10003, '该商户已被禁用'],
        'MERCHANT_BALANCE_NOT_ENOUGH'        => [10004, '商户账户余额不足'],
        'MERCHANT_PARAM_INVALID'             => [10005, '该商户配置缺失'],
        'SIGNATURE_ERROR'                    => [10006, '签名错误'],
        'MERCHANT_WALLET_ADDRESS_INVALID'    => [10007, '商户没有有效的收款钱包地址'],
        'ORDER_ALREADY_EXIST'                => [10008, '订单交易已存在，请勿重复创建'],
        
    ];

    
}
