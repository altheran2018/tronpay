<?php 
class tronpayApi{
    public static function http_post($url, $data){
        if(!function_exists('curl_init')){
            throw new Exception('php未安装curl组件',500);
        }
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if($httpStatusCode != 200){
            throw new Exception("invalid httpstatus:{$httpStatusCode},response:$response,detail_error:".$error,$httpStatusCode);
        }
         
        return $response;
    }

    public static function tronpay_sign(array $parameter, string $signKey)
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
    
}
?>