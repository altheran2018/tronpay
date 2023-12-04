define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'token/index',
                    multi_url: "token/multi",
                    add_url: 'token/add',
                    del_url: "token/del",

                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'wallet_address', title: '收款地址'},
                        {field: 'total_amount', title: '累计收款金额（USDT）'},
                        {field: 'last_payment_at', title: '最近收款时间', formatter: Table.api.formatter.datetime, sortable: true},
                        {field: 'add_time', title: '添加时间', formatter: Table.api.formatter.datetime, sortable: true},
                        {field: 'status', title: '状态', searchList: {'0':'禁用','1':'正常'}, formatter: Table.api.formatter.status},
                        {
                            field: 'ismenu',
                            title: '操作',
                            align: 'center',
                            table: table,
                            formatter: Table.api.formatter.toggle
                        },
                        {
                            field: 'operate',
                            title: '删除',
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
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
        add: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
