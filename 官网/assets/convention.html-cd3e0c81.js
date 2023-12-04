import{_ as e,o as i,c as n,a as d}from"./app-6aa94e3f.js";const a={},r=d(`<h2 id="接口约定" tabindex="-1"><a class="header-anchor" href="#接口约定" aria-hidden="true">#</a> 接口约定</h2><h3 id="密钥" tabindex="-1"><a class="header-anchor" href="#密钥" aria-hidden="true">#</a> 密钥</h3><p>接口文档中提到的 <code>商户密钥（Secret）</code>, 可在 <code>商户后台-&gt;设置-&gt;接入参数</code> 中查看.</p><h3 id="签名算法md5" tabindex="-1"><a class="header-anchor" href="#签名算法md5" aria-hidden="true">#</a> 签名算法MD5</h3><p>签名生成的通用步骤如下：</p><p>第一步，将所有非空参数值的参数按照参数名ASCII码从小到大排序（字典序），使用URL键值对的格式（即key1=value1&amp;key2=value2…）拼接成 <code>待加密参数</code>。</p><p>重要规则：</p><ul><li>参数名ASCII码从小到大排序（字典序）；</li><li>如果参数的值为空不参与签名；</li><li>参数名区分大小写； 第二步， <code>待加密参数</code> 最后拼接上 <code>商户密钥（Secret）</code> 得到待签名字符串，并对 <code>待签名字符串</code> 进行MD5运算，再将得到的 <code>MD5字符串</code> 所有字符转换为 <code>小写</code> ，得到签名 <code>signature</code> 。 注意： <code>signature</code> 的长度为32个字节。</li></ul><p>举例：</p><p>假设传送的参数如下：</p><div class="language-text line-numbers-mode" data-ext="text"><pre class="language-text"><code>merchant_id : 100001
order_id : 202309010805181234
currency : CNY
amount : 100
notify_url : http://example.com/notify
redirect_url : http://example.com/redirect
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>假设商户后台设定的 <code>商户密钥（Secret）</code> 为：A4mauT8hR6VdGr0qTH</p><p>第一步：对参数按照key=value的格式，并按照参数名ASCII字典序排序如下：</p><p><code>amount=100&amp;currency=CNY&amp;merchant_id=100001&amp;notify_url=http://example.com/notify&amp;order_id=202309010805181234&amp;redirect_url=http://example.com/redirect</code></p><p>第二步：拼接API密钥并加密：</p><p><code>MD5(&#39;amount=100&amp;currency=CNY&amp;merchant_id=100001&amp;notify_url=http://example.com/notify&amp;order_id=202309010805181234&amp;redirect_url=http://example.com/redirectA4mauT8hR6VdGr0qTH&#39;)</code></p><p>最终得到最终发送的数据：</p><div class="language-text line-numbers-mode" data-ext="text"><pre class="language-text"><code>merchant_id : 100001
order_id : 202309010805181234
currency : CNY
amount : 100
notify_url : http://example.com/notify
redirect_url : http://example.com/redirect
signature : 7673f4d5bd3d411f8d96530b667258fa
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="php加密示例" tabindex="-1"><a class="header-anchor" href="#php加密示例" aria-hidden="true">#</a> PHP加密示例</h3><div class="language-text line-numbers-mode" data-ext="text"><pre class="language-text"><code>	function tronpaySign(array $parameter, string $signKey)
    {
        ksort($parameter);
        reset($parameter); 
        $sign = &#39;&#39;;
        $urls = &#39;&#39;;
        foreach ($parameter as $key =&gt; $val) {
            if ($val == &#39;&#39;) continue;
            if ($key != &#39;signature&#39;) {
                if ($sign != &#39;&#39;) {
                    $sign .= &quot;&amp;&quot;;
                    $urls .= &quot;&amp;&quot;;
                }
                $sign .= &quot;$key=$val&quot;; 
                $urls .= &quot;$key=&quot; . urlencode($val); 
            }
        }
        $sign = md5($sign . $signKey);//密码追加进入开始MD5签名
        return $sign;
    }
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div>`,20),c=[r];function l(s,t){return i(),n("div",null,c)}const o=e(a,[["render",l],["__file","convention.html.vue"]]);export{o as default};
