define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'room/history/index',
                    add_url: 'room/history/add',
                    edit_url: 'room/history/edit',
                    del_url: 'room/history/del',
                    multi_url: 'room/history/multi',
                    table: 'history',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'b_id', title: __('B_id')},
                        {field: 'u_id', title: __('U_id')},
                        {field: 'g', title: __('G')},
                        {field: 's', title: __('S')},
                        {field: 'info', title: __('Info')},
                        {field: 'num', title: __('Num'), operate:'BETWEEN'},
                        {field: 'y', title: __('Y'), visible:false, searchList: {"1) unsigne":__('1) unsigne')}},
                        {field: 'y_text', title: __('Y'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});