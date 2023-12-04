<?php

use think\exception\HttpException;

// 应用公共文件

/**
 * 抛出HTTP异常
 * @param array $error 错误码与错误信息
 */
function abort(array $error = [])
{
    throw new HttpException($error[0], $error[1]);
}

function tronpaySign(array $parameter, string $signKey)
{
    ksort($parameter);
    reset($parameter); 
    $sign = '';
    $urls = '';
    foreach ($parameter as $key => $val) {
        if ($val == '') continue;
        if ($key != 'signature') {
            if ($sign != '') {
                $sign .= "&";
                $urls .= "&";
            }
            $sign .= "$key=$val"; 
            $urls .= "$key=" . urlencode($val); 
        }
    }
    $sign = md5($sign . $signKey);//密码追加进入开始MD5签名
    return $sign;
}

function httpPost($url, $data, $r_arr = true)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    if(curl_getinfo($curl,CURLINFO_HTTP_CODE) != 200) return false;
    curl_close($curl);

    if($r_arr == false) return $response;

    $response = json_decode($response, true);
    return $response;
}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string  $url    资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $regex = "/^((?:[a-z]+:)?\/\/|data:image\/)(.*)/i";
        $cdnurl = config('app.cdnurl');
        if (is_bool($domain) || stripos($cdnurl, '/') === 0) {
            $url = preg_match($regex, $url) || ($cdnurl && stripos($url, $cdnurl) === 0) ? $url : $cdnurl . $url;
        }
        if ($domain && !preg_match($regex, $url)) {
            $domain = is_bool($domain) ? request()->domain() : $domain;
            $url = $domain . $url;
        }
        return $url;
    }
}