<?php
namespace app\merchant\controller;

use think\App;
use think\facade\View;
use app\common\model\Merchant as merchantModel;


class General extends Base
{

    public function profile()
    {
        if ($this->request->isAjax()) {
            $merchant = session('merchant');
            $row = input('row/a');

            $merchant_info = merchantModel::where(['id'=>$merchant['id']])->find();
            if (!$merchant_info) return json(['code'=>0, 'msg'=>'找不到该商户，请重新登录']);

            if (isset($row['mobile']) && $row['mobile'] != '') {
                $exist = merchantModel::where([['mobile', '=', $row['mobile']], ['id', '<>', $merchant['id']]])->find();
                if ($exist) return json(['code'=>0, 'msg'=>'该手机号已被他人占用']);
            }
            $merchant_info->mobile = $row['mobile'];
            $merchant['mobile'] = $row['mobile'];

            if (isset($row['email']) && $row['email'] != '') {
                $exist = merchantModel::where([['email', '=', $row['email']], ['id', '<>', $merchant['id']]])->find();
                if ($exist) return json(['code'=>0, 'msg'=>'该邮箱已被他人占用']);

                $merchant_info->email = $row['email'];
                $merchant['email'] = $row['email'];
            }

            if (isset($row['password']) && $row['password'] != '') {
                $merchant_info->password = $this->getEncryptPassword($row['password'], $merchant_info['salt']);
            }

            $merchant_info->nickname = $row['nickname'];
            $merchant['nickname'] = $row['nickname'];

            $merchant_info->save();

            session('merchant', $merchant);
            
            return json(['code'=>1, 'msg'=>'更新成功']);
        }

        return view('', [
            
        ]);
    }

    
}
