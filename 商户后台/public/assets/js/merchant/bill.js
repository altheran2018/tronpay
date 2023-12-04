define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'bill/index',

                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'change', title: '账单金额（USDT）'},
                        {field: 'order_amount', title: '订单金额'},
                        {field: 'balance', title: '账户余额（USDT）'},
                        {field: 'rate', title: '费率（%）'},
                        {field: 'add_time', title: '账单时间', formatter: Table.api.formatter.datetime, sortable: true},
                    ]
                ],
                search: false,
                commonSearch: false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        
    };
    return Controller;
});
