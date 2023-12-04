<?php
namespace app\merchant\validate;

use think\Validate;

class Setting extends Validate
{
    protected $rule =   [
        'type' => 'require|in:notice,security,google_unlink,callback,whitelist,apiparam',
        
    ];
    
    protected $message  =   [
        'type.require' => 'type不能为空',
        'type.in' => 'type参数不合法',
        
    ];

    protected $scene = [
        'index' => ['type'],
        
    ]; 
    
}