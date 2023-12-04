<?php
namespace app\merchant\controller;

use think\App;
use think\facade\Db;
use think\facade\View;
use think\captcha\facade\Captcha;
use app\common\model\Merchant as merchantModel;
use app\common\model\AccountDetail as accountDetailModel;
use app\common\model\MerchantParam as merchantParamModel;

class Index extends Base
{
    protected $no_need_login = ['register', 'captcha', 'login', 'googleauth', 'logout'];
    protected $need_token = ['register', 'login', 'googleauth'];

    /**
     * 构造方法
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $app->config->set(['layout_on'=>false], 'view');

        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }

    public function index()
    {
        //if (input('referer') != '') {
            //View::assign('ref_id', 8);
            //View::assign('ref_menu', '订单列表');
            //View::assign('ref_icon', 'dashboard');
        //}

        return view('', [
            
        ]);
    }

    public function register()
    {
        if ($this->request->isAjax()) {
            $username = input('username');
            $password = input('password');
            $email = input('email');
            $captcha = input('captcha');

            //验证码
            if(!Captcha::check($captcha))
            {
                return json(['code'=>0, 'msg'=>'验证码不正确']);
            }

            $exist = merchantModel::where(['username' => $username])->whereOr(['email' => $email])->find();
            if ($exist) {
                return json(['code'=>0, 'msg'=>'商户名或邮箱已存在']);
            }

            Db::startTrans();
            try {
                //写入商户数据
                $insert_data = [];
                $insert_data['username'] = $username;
                $insert_data['nickname'] = $username;
                $salt = mt_rand(1000, 9999);
                $insert_data['password'] = $this->getEncryptPassword($password, $salt);
                $insert_data['salt'] = $salt;
                $insert_data['avatar'] = '/assets/img/avatar.png';
                $insert_data['email'] = $email;
                $insert_data['status'] = 1;
                $insert_data['balance'] = 5;
                $insert_data['rate'] = 2.5;
                $merchant = merchantModel::create($insert_data);

                //写入赠送金额日志
                $insert_data = [];
                $insert_data['merchant_id'] = $merchant->id;
                $insert_data['type'] = 'system';
                $insert_data['change'] = 5;
                $insert_data['balance'] = 5;
                accountDetailModel::create($insert_data);

                //与入初始商户配置
                $insert_data = [];
                $insert_data['merchant_id'] = $merchant->id;
                $insert_data['notify_email'] = $email;
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $secret = substr(str_shuffle(str_repeat($pool, ceil(18 / strlen($pool)))), 0, 18);
                $insert_data['secret'] = $secret;
                merchantParamModel::create($insert_data);

                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return json(['code'=>0, 'msg'=>$e->getMessage()]);
            }

            return json(['code'=>1, 'msg'=>'注册成功！请登录', 'data'=>['url'=>(string)url('index/login')]]);
        }

        return view('', [
            
        ]);
    }

    public function captcha()
    {
        return Captcha::create();    
    }

    public function login()
    {
        $url = input('get.url');
        $url = $url ?: (string)url('index/index');

        if ($this->request->isAjax()) {
            $username = input('post.username');
            $password = input('post.password');
            $keeplogin = input('post.keeplogin');

            $merchant_info = merchantModel::where(['username' => $username])->find();
            if (!$merchant_info) {
                return json(['code'=>0, 'msg'=>'该商户不存在']);
            }
            if ($merchant_info['status'] == -1) {
                return json(['code'=>0, 'msg'=>'该商户已被禁用']);
            }
            if (config('merchant.login_failure_retry') && $merchant_info->loginfailure >= 10 && time() - $merchant_info->update_time < 86400) {
                return json(['code'=>0, 'msg'=>'请1天后尝试登录']);
            }
            if ($merchant_info->password != $this->getEncryptPassword($password, $merchant_info->salt)) {
                $merchant_info->login_failure++;
                $merchant_info->save();
                return json(['code'=>0, 'msg'=>'密码错误']);
            }

            $merchant_param_info = merchantParamModel::where(['merchant_id'=>$merchant_info['id']])->find();
            if ($merchant_param_info['google_secret'] != '') {
                return json(['code'=>1, 'msg'=>'正在进入Google二次验证...', 'data'=>['url'=>(string)url('index/googleauth', ['merchant_id'=>$merchant_info['id'], 'keeplogin'=>$keeplogin,'url'=>$url])]]);
            }

            $merchant_info->login_failure = 0;
            $merchant_info->login_time = time();
            $merchant_info->login_ip = $this->request->ip();
            $merchant_info->token = uniqid();
            $merchant_info->save();
            session('merchant', $merchant_info->toArray());
            session('merchant.safe_code', $this->getEncryptSafecode($merchant_info));
            $this->keepLogin($merchant_info, $keeplogin);
            //dump(session('merchant'));exit;
            
            return json(['code'=>1, 'msg'=>'登录成功', 'data'=>['url'=>$url]]);
        }

        if ($this->autologin()) {
            session('referer', null);
            $this->redirect($url);
        }

        return view('', [
            'keep_login_hours' => config('merchant.keep_login_hours'),
        ]);
    }

    public function googleauth()
    {
        $merchant_id = input('merchant_id');
        $keeplogin = input('keeplogin');
        $url = input('get.url');
        $url = $url ?: (string)url('index/index');

        $google = new \PHPGangsta_GoogleAuthenticator();

        if ($this->request->isAjax()) {
            $merchant_info = merchantModel::find($merchant_id);
            if (!$merchant_info) return json(['code'=>0, 'msg'=>'该商户不存在']);
            if ($merchant_info['status'] == -1) return json(['code'=>0, 'msg'=>'该商户已被禁用']);

            $merchant_param_info = merchantParamModel::where(['merchant_id'=>$merchant_id])->find();
            if (!$merchant_param_info) return json(['code'=>0, 'msg'=>'该商户配置缺失']);
            if ($merchant_param_info) {
                $code = input('code');

                $checkResult = $google->verifyCode($merchant_param_info['google_secret'], $code, 0);
                if ($checkResult) {
                    $merchant_info->login_failure = 0;
                    $merchant_info->login_time = time();
                    $merchant_info->login_ip = $this->request->ip();
                    $merchant_info->token = uniqid();
                    $merchant_info->save();
                    session('merchant', $merchant_info->toArray());
                    session('merchant.safe_code', $this->getEncryptSafecode($merchant_info));
                    $this->keepLogin($merchant_info, $keeplogin);

                    return json(['code'=>1, 'msg'=>'登录成功', 'data'=>['url'=>$url]]);
                } else {
                    return json(['code'=>0, 'msg'=>'安全码不正确']);
                }
            }
        }

        return view('', [
            
        ]);
    }

    public function logout()
    {
        $merchant = session('merchant');
        if (!$merchant) {
            $this->redirect((string)url('index/index'));
        }

        $merchant_info = merchantModel::find(intval($merchant['id']));
        if ($merchant_info) {
            $merchant_info->token = '';
            $merchant_info->save();
        }
        $this->logined = false; //重置登录状态
        session('merchant', null);
        cookie('keep_login', null);
        cookie('merchant_info', null);
        
        $this->redirect((string)url('index/index'));
    }

    
}
