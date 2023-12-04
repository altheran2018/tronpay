<?php
namespace app\merchant\validate;

use think\Validate;

class Token extends Validate
{
    protected $rule =   [
        'ids' => 'require|number',
        'address'  => 'require|length:34',
          
    ];
    
    protected $message  =   [
        'ids.require' => 'ID不能为空',
        'ids.number' => 'IDS参数不正确',
        'address.require' => '钱包地址不能为空',
        'address.length'     => '钱包地址长度不对',
         
    ];

    protected $scene = [
        'index' => [''],
        'multi' => ['ids'],
        'add'  =>  ['address'],
        'del' => ['ids'],
    ]; 
    
}