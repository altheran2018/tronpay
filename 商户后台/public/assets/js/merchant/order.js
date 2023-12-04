define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Table, Form, Echarts, undefined, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/index',

                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'actual_amount', title: '支付金额'},
                        {field: 'amount', title: '订单金额'},
                        {field: 'order_id', title: '订单编号'},
                        {field: 'token', title: '收款地址'},
                        {field: 'product_name', title: '产品名称'},
                        {field: 'user_flag', title: '用户编号'},
                        {field: 'created_at', title: '创建时间', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'status', title: '订单状态', searchList: {'1':'等待支付','2':'支付成功','3':'已过期'}, formatter: Table.api.formatter.status},
                        {field: 'updated_at', title: '更新时间', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {
                            field: 'operate', title: '操作', table: table, events: Table.api.events.operate, buttons: [{
                                name: 'detail',
                                text: '详情',
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                url: 'order/detail'
                            }],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        detail: function () {
            Form.api.bindevent($("form.edit-form"));
        },
        statics: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            var myChart2 = Echarts.init(document.getElementById('echart2'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                      type: 'cross',
                      label: {
                        backgroundColor: '#6a7985'
                      }
                    }
                },
                legend: {
                    data: ['所有','已完成']
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: column
                },
                yAxis: {},
                series: [{
                    name: '所有',
                    type: 'line',
                    smooth: false,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: echart_data_amount_all
                },
                {
                    name: '已完成',
                    type: 'line',
                    smooth: false,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: echart_data_amount_complete
                }]
            };

            var option2 = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                      type: 'cross',
                      label: {
                        backgroundColor: '#6a7985'
                      }
                    }
                },
                legend: {
                    data: ['所有','已完成']
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                grid: [{
                    left: 'left',
                    top: 'top',
                    right: '10',
                    bottom: 30
                }],
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: column
                },
                yAxis: {},
                series: [{
                    name: '所有',
                    type: 'line',
                    smooth: false,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: echart_data_count_all
                },
                {
                    name: '已完成',
                    type: 'line',
                    smooth: false,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: echart_data_count_complete
                }]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
            myChart2.setOption(option2);

            $(window).resize(function () {
                myChart.resize();
                myChart2.resize();
            });

            $(document).on("click", ".btn-refresh", function () {
                setTimeout(function () {
                    myChart.resize();
                    myChart2.resize();
                }, 0);
            });

        }
    };
    return Controller;
});
