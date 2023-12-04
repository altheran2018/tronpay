<?php
declare (strict_types = 1);

namespace app\merchant\controller;

use think\App;
use think\facade\View;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use app\common\model\Merchant;


/**
 * 商户后台公共控制器
 */
class Base
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $no_need_login = [];

    protected $need_token = [];

    /**
     * 布局模板
     * @var string
     */
    protected $layout = 'default';

    protected $_error = '';

    protected $logined = false; //登录状态

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    public function initialize()
    {
        $controller_name = strtolower($this->request->controller());
        $action_name = strtolower($this->request->action());

        $path = str_replace('.', '/', $controller_name) . '/' . $action_name;

        // 定义是否Addtabs请求
        !defined('IS_ADDTABS') && define('IS_ADDTABS', (bool)input("addtabs"));

        // 定义是否Dialog请求
        !defined('IS_DIALOG') && define('IS_DIALOG', (bool)input("dialog"));

        // 定义是否AJAX请求
        !defined('IS_AJAX') && define('IS_AJAX', $this->request->isAjax());

        // 检测是否需要验证登录
        if (!in_array($action_name, $this->no_need_login)) {
            //检测是否登录
            if (!$this->isLogin()) {
                $url = session('referer');
                $url = $url ? $url : $this->request->url();
                
                if ($this->request->isAjax()) {
                    echo '{"code":0,"msg":"请登录后操作","data":"","url":"'.(string)url('index/login').'","wait":3}';
                    exit;
                }
                $this->redirect((string)url('index/login', ['url' => $url]));
            }

            // 非选项卡时重定向
            if (!$this->request->isPost() && !IS_AJAX && !IS_ADDTABS && !IS_DIALOG && input("ref") == 'addtabs') {
                $url = preg_replace_callback("/([\?|&]+)ref=addtabs(&?)/i", function ($matches) {
                    return $matches[2] == '&' ? $matches[1] : '';
                }, $this->request->url());
                
                    if (stripos($url, $this->request->server('SCRIPT_NAME')) === 0) {
                        $url = substr($url, strlen($this->request->server('SCRIPT_NAME')));
                    }
                    //$url = (string)url($url);
                
                $this->redirect((string)url('index/index', ['referer' => $url]));
                exit;
            }
        }

        if (IS_AJAX) {
            try {
                $data = input('row/a') ? input('row/a') : input('post.');
                $class = 'app\\merchant\\validate\\' . $this->request->controller();
                $v = new $class();
                $v->scene($action_name);
                $v->failException(true)->check($data);
            } catch (ValidateException $e) {
                echo '{"code":0,"msg":"'.$e->getMessage().'"}';
                exit;
            }
        }

        //if (IS_AJAX && in_array($action_name, $this->need_token)) {
        //    $result = $this->request->checkToken('__token__');
        //    if (!$result) {
        //        echo '{"code":0,"msg":"令牌数据无效"}';
        //        exit;
        //    }
        //}

        // 配置信息
        $config = [   
            'language' => 'zh-cn',
        ];

        View::assign('title', config('merchant.title'));
        View::assign('config', $config);
        View::assign('merchant', session('merchant'));
    }

    /**
     * 检测是否登录
     *
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->logined) {
            return true;
        }
        $merchant = session('merchant');
        if (!$merchant) {
            return false;
        }
        $m_info = Merchant::find($merchant['id']);
        if (!$m_info) {
            return false;
        }
        //校验安全码，可用于判断关键信息发生了变更需要重新登录
        if (!isset($merchant['safe_code']) || $this->getEncryptSafecode($m_info) !== $merchant['safe_code']) {
            //$this->logout();
            return false;
        }
        //判断是否同一时间同一账号只能在一个地方登录
        if (config('merchant.login_unique')) {
            if ($m_info['token'] != $merchant['token']) {
                //$this->logout();
                return false;
            }
        }
        //判断管理员IP是否变动
        if (config('merchant.login_ip_check')) {
            if (!isset($merchant['login_ip']) || $merchant['login_ip'] != $this->request->ip()) {
                //$this->logout();
                return false;
            }
        }
        $this->logined = true;
        return true;
    }

    /**
     * 自动登录
     * @return boolean
     */
    public function autologin()
    {
        $keeplogin = cookie('keep_login');
        if (!$keeplogin) {
            return false;
        }
        list($id, $keeptime, $expiretime, $key) = explode('|', $keeplogin);
        if ($id && $keeptime && $expiretime && $key && $expiretime > time()) {
            $merchant_info = Merchant::find($id);
            if (!$merchant_info || !$merchant_info->token) {
                return false;
            }
            //token有变更
            if ($key != $this->getKeeploginKey($merchant_info, $keeptime, $expiretime)) {
                return false;
            }
            $ip = $this->request->ip();
            //IP有变动
            if ($merchant_info->login_ip != $ip) {
                return false;
            }
            session('merchant', $merchant_info->toArray());
            session('merchant.safe_code', $this->getEncryptSafecode($merchant_info));
            //刷新自动登录的时效
            $this->keepLogin($merchant_info, $keeptime);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 刷新保持登录的Cookie
     *
     * @param int $keeptime
     * @return  boolean
     */
    public function keepLogin($merchant_info, $keeptime = 0)
    {
        if ($keeptime) {
            $expiretime = time() + $keeptime;
            $key = $this->getKeeploginKey($merchant_info, $keeptime, $expiretime);
            cookie('keep_login', implode('|', [$merchant_info['id'], $keeptime, $expiretime, $key]), $keeptime);
            return true;
        }
        return false;
    }

    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt     密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }

    /**
     * 获取自动登录Key
     * @param $params
     * @param $keeptime
     * @param $expiretime
     * @return string
     */
    public function getKeeploginKey($params, $keeptime, $expiretime)
    {
        $key = md5(md5((string)$params['id']) . md5((string)$keeptime) . md5((string)$expiretime) . $params['token'] . config('merchant.token_key'));
        return $key;
    }

    /**
     * 获取加密后的安全码
     * @param $params
     * @return string
     */
    public function getEncryptSafecode($params)
    {
        return md5(md5($params['username']) . md5(substr($params['password'], 0, 6)) . config('merchant.token_key'));
    }

    /**
     * 自定义重定向
     * @param mixed ...$args
     */
    public function redirect(...$args)
    {
        throw new HttpResponseException(redirect(...$args));
    }

}
?>
