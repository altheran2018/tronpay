<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use app\common\model\MerchantParam as merchantParamModel;

class Setting extends Base
{
    public function index()
    {
        $google = new \PHPGangsta_GoogleAuthenticator();

        $merchant = session('merchant');
        $merchant_param_info = merchantParamModel::where(['merchant_id'=>$merchant['id']])->find();

        if ($this->request->isAjax()) {
            $type = input('type');

            if ($type == 'notice') {
                $row = input('row/a');
                $merchant_param_info->notify_email = $row['notify_email'];
                $merchant_param_info->order_complete = isset($row['order_complete']) ? 1 : 0;
                $merchant_param_info->balance_not_enough = isset($row['balance_not_enough']) ? 1 : 0;
                $merchant_param_info->login_success = isset($row['login_success']) ? 1 : 0;
                $merchant_param_info->save();

                return json(['code'=>1, 'msg'=>'保存成功']);
            } else if ($type == 'security') {
                $secret = input('secret');
                $code = input('code');
                
                $checkResult = $google->verifyCode($secret, $code, 0);
                if ($checkResult) {
                    $merchant_param_info->google_secret = $secret;
                    $merchant_param_info->save();

                    return json(['code'=>1, 'msg'=>'Google二次验证绑定成功']);
                } else {
                    return json(['code'=>0, 'msg'=>'安全码不正确']);
                }
            } else if ($type == 'google_unlink') {
                $code = input('code');
                
                $checkResult = $google->verifyCode($merchant_param_info['google_secret'], $code, 0);
                if ($checkResult) {
                    $merchant_param_info->google_secret = '';
                    $merchant_param_info->save();

                    return json(['code'=>1, 'msg'=>'Google二次验证解绑成功']);
                } else {
                    return json(['code'=>0, 'msg'=>'安全码不正确']);
                }
            } else if ($type == 'callback') {
                $row = input('row/a');
                $merchant_param_info->notify_url = $row['notify_url'];
                $merchant_param_info->save();

                return json(['code'=>1, 'msg'=>'保存成功']);
            } else if ($type == 'whitelist') {
                $row = input('row/a');
                $merchant_param_info->white_list = $row['white_list'];
                $merchant_param_info->save();

                return json(['code'=>1, 'msg'=>'保存成功']);
            } else if ($type == 'apiparam') {
                $row = input('row/a');
                $merchant_param_info->secret = $row['secret'];
                $merchant_param_info->save();

                return json(['code'=>1, 'msg'=>'保存成功']);
            }
        }

        $secret = '';
        $qrcode_url = '';
        if ($merchant_param_info['google_secret'] == '') {
            $username = $merchant['username'].'@tronpay';
            $app_name = 'Tronpay';
            
            $secret = $google->createSecret();
            $qrcode_url = $google->getQRCodeGoogleUrl($username, $secret, $app_name);
        }
        

        return view('', [
            'merchant_param_info' => $merchant_param_info,
            'secret' => $secret,
            'qrcode_url' => $qrcode_url,
        ]);
    }

    public function google_unlink(){
        $merchant = session('merchant');
        merchantParamModel::where(['merchant_id'=>$merchant['id']])->update(['google_secret'=>'']);

        return json(['code'=>1, 'msg'=>'解绑成功']);
    }

    
}
