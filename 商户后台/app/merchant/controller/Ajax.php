<?php
namespace app\merchant\controller;

use think\App;

class Ajax extends Base
{
    protected $no_need_login = ['lang'];

    public function lang()
    {
        echo 'define({
            "username must be 3 to 30 characters":"商户名只能由3-30位数字、字母、下划线组合",
            "password must be 6 to 30 characters":"密码长度必须在6-30位之间，不能包含空格",
            "today":"今天",
            "yesterday":"昨天",
            "last 7 days":"最近7天",
            "last 30 days":"最近30天",
            "this month":"本月",
            "last month":"上月",
            "custom range":"自定义",
            "choose":"选择",
            "submit":"提交",
            "reset":"重置",
            "all":"全部",
            "first":"首页",
            "previous":"上一页",
            "next":"下一页",
            "last":"末页",
            "go":"跳转",
            "click to search %s":"点击搜索 %s",
            "click to toggle":"点击切换",
            "del":"删除",
            "are you sure you want to delete this item?":"确定删除此项?",
            "ok":"确定",
            "cancel":"取消"
        });';
    }
}