<?php
namespace app\merchant\validate;

use think\Validate;

class Index extends Validate
{
    protected $rule =   [
        'username' => 'require|alphaDash|length:3,20',
        'password'  => 'require|alphaDash|length:6,30',
        'email' => 'require|email',
        'captcha' => 'require|alphaNum|length:4',
        'code' => 'require|number|length:6',
    ];
    
    protected $message  =   [
        'username.require' => '商户名不能为空',
        'username.alphaDash' => '商户名只能由字母和数字，下划线_及破折号-组成',
        'username.length' => '商户名必须是3-30位字符',
        'password.require' => '密码不能为空',
        'password.alphaDash' => '密码格式不对',
        'password.length' => '密码长度不对',
        'email.require' => '邮箱地址不能为空',
        'email.email' => '邮箱地址格式不对',
        'captcha.require' => '验证码不能为空',
        'captcha.alphaNum' => '验证码只能为数字和字母',
        'captcha.length' => '验证码长度不对',
        'code.require' => '安全码不能为空',
        'code.number' => '安全码只能为数字',
        'code.length' => '安全码长度不对',
         
    ];

    protected $scene = [
        'index' => [''],
        'register' => ['username', 'password', 'email', 'captcha'],
        'captcha' => [''],
        'login'  =>  ['username', 'password'],
        'googleauth' => ['code'],
    ]; 
    
}