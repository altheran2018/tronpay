import{_ as s,r as a,o as e,c as r,b as t,d as o,e as i,a as d}from"./app-6aa94e3f.js";const c={},l=d(`<h2 id="订单创建" tabindex="-1"><a class="header-anchor" href="#订单创建" aria-hidden="true">#</a> 订单创建</h2><h3 id="接口地址" tabindex="-1"><a class="header-anchor" href="#接口地址" aria-hidden="true">#</a> 接口地址</h3><div class="language-text line-numbers-mode" data-ext="text"><pre class="language-text"><code>POST /api/transaction/create
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div></div></div><blockquote><p>入参示例</p></blockquote><div class="language-text line-numbers-mode" data-ext="text"><pre class="language-text"><code>{
	merchant_id : 100001
	order_id : 202309010805181234
	currency : CNY
	amount : 100
	notify_url : http://example.com/notify
	redirect_url : http://example.com/redirect
	signature : 7673f4d5bd3d411f8d96530b667258fa
}
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="请求参数" tabindex="-1"><a class="header-anchor" href="#请求参数" aria-hidden="true">#</a> 请求参数</h3><table><thead><tr><th>名称</th><th>位置</th><th>类型</th><th>必选</th><th>中文名</th><th>说明</th></tr></thead><tbody><tr><td>body</td><td>body</td><td>object</td><td>否</td><td></td><td></td></tr><tr><td>» merchant_id</td><td>body</td><td>string</td><td>是</td><td>商户ID</td><td></td></tr><tr><td>» order_id</td><td>body</td><td>string</td><td>是</td><td>请求支付订单号</td><td></td></tr><tr><td>» currency</td><td>body</td><td>string</td><td>是</td><td>订单币种</td><td>人民币为<code>CNY</code> 美元为<code>USD</code></td></tr><tr><td>» amount</td><td>body</td><td>number</td><td>是</td><td>订单金额</td><td>小数点保留后2位，最少0.01</td></tr><tr><td>» notify_url</td><td>body</td><td>string</td><td>是</td><td>异步回调地址</td><td></td></tr><tr><td>» redirect_url</td><td>body</td><td>string</td><td>否</td><td>同步跳转地址</td><td></td></tr><tr><td>» signature</td><td>body</td><td>string</td><td>是</td><td>签名</td><td>见接口约定</td></tr></tbody></table><blockquote><p>返回示例</p></blockquote><blockquote><p>成功</p></blockquote><div class="language-json line-numbers-mode" data-ext="json"><pre class="language-json"><code><span class="token punctuation">{</span>
  <span class="token property">&quot;status_code&quot;</span><span class="token operator">:</span> <span class="token number">200</span><span class="token punctuation">,</span>
  <span class="token property">&quot;message&quot;</span><span class="token operator">:</span> <span class="token string">&quot;success&quot;</span><span class="token punctuation">,</span>
  <span class="token property">&quot;data&quot;</span><span class="token operator">:</span> <span class="token punctuation">{</span>
    <span class="token property">&quot;trade_id&quot;</span><span class="token operator">:</span> <span class="token string">&quot;202308221692680418158858&quot;</span><span class="token punctuation">,</span>
    <span class="token property">&quot;order_id&quot;</span><span class="token operator">:</span> <span class="token string">&quot;202308221300188505&quot;</span><span class="token punctuation">,</span>
    <span class="token property">&quot;currency&quot;</span><span class="token operator">:</span> <span class="token string">&quot;CNY&quot;</span><span class="token punctuation">,</span>
    <span class="token property">&quot;amount&quot;</span><span class="token operator">:</span> <span class="token number">50</span><span class="token punctuation">,</span>
    <span class="token property">&quot;actual_amount&quot;</span><span class="token operator">:</span> <span class="token number">7.143</span><span class="token punctuation">,</span>
    <span class="token property">&quot;token&quot;</span><span class="token operator">:</span> <span class="token string">&quot;TGPzzSDzuWchyxEGRgBGuL2ZUm7yJMNXM8&quot;</span><span class="token punctuation">,</span>
    <span class="token property">&quot;expiration_time&quot;</span><span class="token operator">:</span> <span class="token number">1692681018</span><span class="token punctuation">,</span>
    <span class="token property">&quot;payment_url&quot;</span><span class="token operator">:</span> <span class="token string">&quot;http://example.com/pay/checkout-counter/202308221692680418158858&quot;</span>
  <span class="token punctuation">}</span><span class="token punctuation">,</span>
  <span class="token property">&quot;request_id&quot;</span><span class="token operator">:</span> <span class="token string">&quot;b1344d70-ff19-4543-b601-37abfb3b3686&quot;</span>
<span class="token punctuation">}</span>
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="返回结果" tabindex="-1"><a class="header-anchor" href="#返回结果" aria-hidden="true">#</a> 返回结果</h3>`,11),p=t("thead",null,[t("tr",null,[t("th",null,"状态码"),t("th",null,"状态码含义"),t("th",null,"说明"),t("th",null,"数据模型")])],-1),u=t("td",null,"200",-1),h={href:"https://tools.ietf.org/html/rfc7231#section-6.3.1",target:"_blank",rel:"noopener noreferrer"},b=t("td",null,"成功",-1),m=t("td",null,"Inline",-1),v=d('<h3 id="返回数据结构" tabindex="-1"><a class="header-anchor" href="#返回数据结构" aria-hidden="true">#</a> 返回数据结构</h3><p>状态码 <strong>200</strong></p><table><thead><tr><th>名称</th><th>类型</th><th>解释</th><th>说明</th></tr></thead><tbody><tr><td>» status_code</td><td>integer</td><td>请求状态</td><td>请参考下方<a href="#status-code%E8%BF%94%E5%9B%9E%E7%8A%B6%E6%80%81%E7%A0%81%E5%8F%8A%E5%90%AB%E4%B9%89">status_code返回状态码及含义</a></td></tr><tr><td>» message</td><td>string</td><td>消息</td><td></td></tr><tr><td>» data</td><td>object</td><td>返回数据</td><td></td></tr><tr><td>»» trade_id</td><td>string</td><td>交易号</td><td></td></tr><tr><td>»» order_id</td><td>string</td><td>请求支付订单号</td><td></td></tr><tr><td>»» currency</td><td>string</td><td>订单币种</td><td>人民币为<code>CNY</code> 美元为<code>USD</code></td></tr><tr><td>»» amount</td><td>float</td><td>请求支付金额</td><td>保留2位小数</td></tr><tr><td>»» actual_amount</td><td>float</td><td>实际需要支付的金额</td><td>USDT,保留四位小数</td></tr><tr><td>»» token</td><td>string</td><td>钱包地址</td><td></td></tr><tr><td>»» expiration_time</td><td>integer</td><td>过期时间</td><td>时间戳秒</td></tr><tr><td>»» payment_url</td><td>string</td><td>收银台地址</td><td></td></tr><tr><td>» request_id</td><td>string</td><td>true</td><td></td></tr></tbody></table><h3 id="status-code返回状态码及含义" tabindex="-1"><a class="header-anchor" href="#status-code返回状态码及含义" aria-hidden="true">#</a> status_code返回状态码及含义</h3><table><thead><tr><th>状态码</th><th>说明</th></tr></thead><tbody><tr><td>201</td><td>数据库错误</td></tr><tr><td>202</td><td>服务间调用出错</td></tr><tr><td>203</td><td>系统错误</td></tr><tr><td>422</td><td>参数缺失或错误（请看具体message信息）</td></tr><tr><td>10001</td><td>该商户不存在</td></tr><tr><td>10002</td><td>该商户尚未通过审核</td></tr><tr><td>10003</td><td>该商户已被禁用</td></tr><tr><td>10004</td><td>商户账户余额不足</td></tr><tr><td>10005</td><td>该商户配置缺失</td></tr><tr><td>10006</td><td>签名错误</td></tr><tr><td>10007</td><td>商户没有有效的收款钱包地址</td></tr><tr><td>10008</td><td>订单交易已存在，请勿重复创建</td></tr></tbody></table>',5);function k(_,q){const n=a("ExternalLinkIcon");return e(),r("div",null,[l,t("table",null,[p,t("tbody",null,[t("tr",null,[u,t("td",null,[t("a",h,[o("OK"),i(n)])]),b,m])])]),v])}const y=s(c,[["render",k],["__file","create.html.vue"]]);export{y as default};
