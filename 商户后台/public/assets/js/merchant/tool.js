define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        paytest: function () {
            //Form.api.bindevent($("form[role=form]"));
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                
            }, function(data, ret){
                
            }, function(success, error){
                $.ajax({
                    url: "tool/paytest",
                    type: 'post',
                    dataType: 'json',
                    data: {currency: $('#currency').val(),amount: $('#amount').val(),notify_url: $('#notify_url').val(),redirect_url: $('#redirect_url').val()},
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
        },
        plugin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'tool/plugin',

                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'plugin_name', title: '名称'},
                        {field: 'author', title: '作者'},
                        {field: 'price', title: '价格'},
                        {field: 'version', title: '版本号'},
                        {field: 'add_time', title: '添加时间', formatter: Table.api.formatter.datetime, sortable: true},
                        {
                            field: 'id',
                            title: '操作',
                            align: 'center',
                            table: table,
                            formatter: Controller.api.formatter.operate
                        }
                    ]
                ],
                pagination: false,
                search: false,
                commonSearch: false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            formatter: {
                
                operate: function (value, row, index) {
                    return Template("operatetpl", {item: row, index: index});
                },
                
            }
        }
    };
    return Controller;
});
