define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'recharge/index',
                    add_url: 'recharge/pay',

                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'change', title: '充值金额（USDT）'},
                        {field: 'balance', title: '账户余额（USDT）'},
                        {field: 'add_time', title: '添加时间', formatter: Table.api.formatter.datetime, sortable: true},
                        {
                            field: 'operate', title: '操作', table: table, events: Table.api.events.operate, buttons: [{
                                name: 'detail',
                                text: '详情',
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                url: 'recharge/detail'
                            }],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ],
                search: false,
                commonSearch: false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        pay: function () {
            $(document.body).on("click", ".usual_amount", function (e) {
                $('#amount').val($(this).text());
            });

            //Form.api.bindevent($("form[role=form]"));
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                
            }, function(data, ret){
                
            }, function(success, error){
                $.ajax({
                    url: "recharge/pay",
                    type: 'post',
                    dataType: 'json',
                    data: {amount: $('#amount').val()},
                    success: function (ret) {
                        if (ret.hasOwnProperty("code")) {
                            var data = ret.hasOwnProperty("data") && ret.data != "" ? ret.data : "";
                            if (ret.code === 1) {
                                parent.Layer.closeAll();
                                parent.Layer.open({
                                    type: 2,
                                    closeBtn: 1,
                                    title: "请扫码完成支付",
                                    area: ['450px','90%'],
                                    content: ret.data.payment_url,
                                    cancel: function(){
                                        
                                    }
                                });
                            } else {
                                Backend.api.toastr.error(ret.msg);
                            }
                        }
                    }, error: function (e) {
                        Backend.api.toastr.error(e.message);
                    }
                });

                return false;
            });
        }
    };
    return Controller;
});
