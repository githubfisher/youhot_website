<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/modules/jeditable/jquery.jeditable.js?' . STATIC_ADMIN_VERSION . '.js');
//$this->layout->load_css();
$this->layout->placeholder('title', '订单管理');
?>
<style>
    table.dataTable th {
        text-align: left;
    }

    .editable {
        min-height: 24px;
        min-width: 30px;
        border: 1px dashed #efefef;
        cursor: pointer;
    }

    .to-extend {
        height: 75px;
        overflow: hidden;
        cursor: pointer;
        text-overflow: ellipsis;
    }

    tbody tr td.dt-center {
        /*padding-top: 20px;*/
    }

    tbody tr td.dt-head-center img {
        display: block;
        width: 20%;
        height: 50px;
        float: left;
    }

    tbody tr td.dt-head-center span {
        padding-left: 16px;
        display: block;
        width: 80%;
        height: auto;
        font-size: 12px;
        color: #1E1E1E;
        float: left;
        padding-top: 5px
    }

    tbody tr td.dt-center span {
        font-size: 12px;
    }

    .dt-center div a {
        color: #1E1E1E
    }

    tbody tr td.dt-head-center {
        padding-top: 10px;
        line-height: normal;
        margin-right: 0;
        text-align: left;
    }

    #list-table tr > td.dt-op a {
       /* color: #0B97D4 */
    }


</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>订单管理</h3></div>
    </div>
    <div class="m-content">
        <ul class="nav nav-tabs" style="margin-bottom: 20px;">
            <li role="presentation" class="active" data-status="0"><a href="#">全部</a></li>
            <!-- <li role="presentation" data-status="<?= ORDER_STATUS_PRE_PAID ?>"><a href="#">已预订</a></li>
            <li role="presentation" data-status="<?= ORDER_STATUS_LAST_PAY_START ?>"><a href="#">待收尾款</a></li>
            <li role="presentation"><a href="#" data-status="--><? //=ORDER_STATUS_LAST_PAID?><!--">待生产</a></li>-->
	    <li role="presentation" data-status="<?= ORDER_STATUS_LAST_PAID ?>"><a href="#">待发货</a></li>

            <li role="presentation" data-status="<?= ORDER_STATUS_SHIP_START ?>"><a href="#">已发货</a></li>
            <li role="presentation" data-status="<?= ORDER_STATUS_SHIP_RECEIVED ?>"><a href="#">已收货</a></li>
            <li role="presentation" data-status="<?= ORDER_STATUS_END_FAIL ?>"><a href="#">已关闭</a></li>
            <li role="presentation" data-status="<?= ORDER_STATUS_END_SUCCEED ?>"><a href="#">已完成</a></li>
        </ul>
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">

        </table>
    </div>
</div>

<script>
    var status_obj = {
        "<?=ORDER_STATUS_DELETE?>": "已删除",
        "<?=ORDER_STATUS_INIT?>": "预定(未付款)",
        "<?=ORDER_STATUS_PRE_PAID?>": "预定(已付款)",
        "<?=ORDER_STATUS_LAST_PAY_START?>": "待付款",
        "<?=ORDER_STATUS_LAST_PAID?>": "待发货",
        "<?=ORDER_STATUS_SHIP_START?>": "已发货",
        "<?=ORDER_STATUS_SHIP_RECEIVED?>": "已收货",
        "<?=ORDER_STATUS_END_SUCCEED?>": "已完成",
        "<?=ORDER_STATUS_END_FAIL?>": "已关闭",
        "<?=ORDER_STATUS_CANCEL_UNPAY?>": "已取消（未支付）",
        "<?=ORDER_STATUS_CANCEL_PAY?>": "已取消（已支付）",
    }
    function format_status(status) {
        var str = '未知';
        return (status in status_obj) ? status_obj[status] : str;
    }

    $(document).ready(function () {

        $(".nav-tabs ").on('click', 'li', function () {
            $(this).addClass('active').siblings().removeClass('active');
            var _v = $(this).data('status');
            oTable.column(6).search(_v).draw();
            return false;
        });
        var product_id = get_param_value('product_id');
        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            var _setting = $.extend(originalOptions.data, {"of": originalOptions.data.start, "lm": originalOptions.data.length});
            delete _setting.start;
            delete _setting.length;
            if ('columns' in _setting) {
                for (var i = 0, j = _setting.columns.length; i < j; i++) {
                    var row = _setting.columns[i];
//                console.log(row);
                    if (row['searchable'] && row['search']['value'] != null) {
                        _setting[row['data']] = row['search']['value'];
                    }
                }
                delete _setting.columns;
            }
            options.data = $.param(_setting);
        });

        var g_source_url = CONFIG.order.seller_list_url + "?rt=json";

        if (product_id != null) {
            g_source_url += "&product_id=" + product_id;
        }

        var oTable = $('#list-table').DataTable({
            "processing": true,
            "serverSide": true,   //@todo 多的时候需要实现翻页
            "pageLength": 10,
            "lengthChange": false,
            "rowId": 'order_id',
            "dom": "<'row'<'col-sm-6'f><'col-sm-6'l>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "ajax": $.fn.dataTable.pipeline({
                url: g_source_url,
                pages: 5,

            }),
            "columns": [
                {"data": "create_time", 'title': 'create_time', searchable: false},
                {"data": null, 'title': "订单信息", className: "order-desc-td dt-head-center", width: "15%"},
                {"data": null, 'title': "订单号", className: "order-attr-td dt-center", width: "25%", searchable: true},
                {"data": "buyer_userid", 'title': '买家', width: "10%", className: "dt-center", searchable:true},
                {"data": "create_time", 'title': '下单时间', width: "10%", className: "dt-center editable input", searchable: false},
                {"data": "memo", 'title': '备注', width: "10%", className: "dt-center editable input", searchable: false},
                {"data": "status", 'title': '状态', width: "10%", className: "dt-center editable select status", searchable: true},
                {"data": null, 'title': '编辑', width: "10%", className: "dt-center dt-op", searchable: false},
                {"data": null, 'title': '海外订单', width: "10%", className: "order-attr-td dt-center", searchable: false},
            ],
            "ordering": false,
            "order": [[0, "desc"]],
            "columnDefs": [
//username add link
                {
                    'render': function (data, type, row) {
                        var str = "";
			if (data.product_title == '' || data.product_title == null) {
                            str += '<span style=\"font-size:16px;\">多商品订单</span>';
                        } else {
                            if (data.product_cover_image != null) {
                                str += '<img class="" style="width:100%;height:80px;" src="' + data.product_cover_image + '" >';
                            }
                        }
                        return str;
                    },
                    "targets": [1]
                },
                {
                    'render': function (data, type, row) {
			var str = '<span>' + data.order_id + '</span>';
			return str;
                    },
                    "targets": [2]
                },
                {
                    'render': function (data, type, row) {
                        var str = row.buyer_nickname;
                        return str;
                    },
                    "targets": [3]
                },
                {
                    'render': function (data, type, row) {
                        return format_status(data);
                    },
                    "targets": [6] //status
                },
                {
                    'render': function (data, type, row) {
                        var str;
                        str = '<div style="padding: 15px 5px; color:white;"><a href="/admin/order/detail/' + row.order_id + '" class="product-add btn btn-purple">详情</a></div>';
                       // str += '<div><a href="/admin/order/edit/' + row.order_id + '">编辑</a></div>';
			if (data.status == 30) {
                           str += '<div><a href="javascript:void(0)" onclick="finish(\'' + row.order_id + '\')" class="product-add btn btn-orange">完成</a></div>';
			}
                        return str;
                    }
                    , "targets": [7]
                },
		{
                    'render': function (data, type, row) {
			if (data.overseas == '' || data.overseas == null) {
			    var str = '<span>未下单</span>';
			} else {
			    var str = '<span>已下单</span>';
			}
                        return str;
                    },
                    'targets': [8]
                },

                {
                    "visible": false, "targets": [0]
                }


            ],
            "language": {
                "zeroRecords": "暂无数据",
                "info": " 记录：_START_ - _END_ ，共 _TOTAL_ 条 ",
                "infoEmpty": "搜索无结果",
                "infoFiltered": "( 共 _MAX_ 条)",
                "search": "搜索",
                "sSearchPlaceholder": "订单编号/订单内容"

            }
            ,

            drawCallback: function (settings) {
                var submitHandler = function (config, element) {
                    var c = oTable.cell($(element));
                    var idx = c.index().column;
                    var keyname = oTable.column(idx).dataSrc();
                    config.name = keyname;
                    config.submitdata['order_id'] = oTable.row($(element).parent()).id();
                    if (config.type == "select") {
                        $(element).data('v', $(':input:first', this).find(":selected").text());
                    } else {
                        $(element).data('v', $(':input:first', this).val());
                    }
                    return false;
                }

                $('td.editable.select').editable(CONFIG.order.update_url + "?rt=json", $.extend({}, CONFIG.EDITABLE_OPTIONS, {
                    type: "select",
                    data: function (value, settings) {
                        console.log(value);
                        return status_obj;
                    },
                    "submitHandle": submitHandler,
                }));
		
                $('td.editable.input').editable(CONFIG.order.update_url + "?rt=json", $.extend({}, CONFIG.EDITABLE_OPTIONS, {
                    "submitHandle": submitHandler
                }));

                $('.editable').hover(
                    function () {
                        $(this).append("<span class='icon icon-edit></span>")
                    },
                    function () {
                        $(this).children("span:last").remove();
                    }
                );
		
            },


        });


        $('#list-table').on('click', '.buyer-info', function (e) {
            var self = $(this);

            $.post(self.attr('href'), {rt: 'json'}, function (data) {
                if (data.res == 0) {
                    showSuccess(data.toString());
                } else {
                    showErrorMessage(data.hint);
                }

            }, 'json');
            return false;

        });


    })
    ;
    function finish(id)
    {
	var url = '/order/update';
	$.post(url, {rt: 'json', order_id:id, status:31}, function (data) {
            if (data.res == 0) {
                showSuccess('操作成功');
            } else {
               showErrorMessage('操作失败');
            }

        }, 'json');
        return false;
    }
</script>

