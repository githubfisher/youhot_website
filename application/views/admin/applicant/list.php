<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/modules/jeditable/jquery.jeditable.js');
$js = array('admin/js/jquery/jquery.validate.js', 'admin/js/app.js');
$this->layout->load_js($js);
//$this->layout->load_css();
$this->layout->placeholder('title', '用户管理');
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

    .img-circle {
        width: 33px;
        height: 33px;
        margin-right: 12px;
    }

    .dt-center.user-edit, .user-role {
        color: #0B97D4 !important;
        display: none
    }

    .user-delete > span > span {
        color: #EC1379;
        display: none;
    }

    #list-table_wrapper > .row > .col-sm-5 > #list-table_info {
        padding-left: 32px
    }

    .list-table_paginate.list-table_paginate, ul.pagination {
        padding-right: 5%
    }

    .dt-center a {
        color: #1E1E1E
    }

    .user-btn {
        width: 100px;
        height: 40px;
        float: right;
        text-align: center;
        line-height: 40px;
        border-radius: 4px;
        background: #0B97D4;
        font-size: 18px;
        color: #fff;

        cursor: pointer;
    }

    .btn-content {
        width: 200px;
        height: 200px;
        padding: 10px;
        display: none;
        position: absolute;
        left: -200px;
        background: #A3D5D2;
        top: 0;
        z-index: 1
    }

    .btn-content input {
        width: 120px;
        line-height: 30px;
        color: #000;
        font-size: 12px;
        border-radius: 4px;
        border: 1px solid #ccc;
        height: 28px;
    }

    .btn-content form div {
        width: 100%;
        margin-top: 10px;
        color: #fff;

        font-size: 14px;
        height: 30px;
    }

    .btn-content-box {
        width: 100px;
        height: 40px;
        position: relative;
        float: right;
        margin-right: 40px;
    }

    .btn-content select {
        width: 72%;
        height: 30px;
        color: #000;
        margin-top: 10px;
        border-radius: 4px;
    }

    .btn-content select option {
        color: #000
    }

    .sumit-btn {
        width: 100% !important;
        height: 30px;
        background: #0B97D4;
        color: #fff;
        margin: 0 auto;
    }

    .dt-center a {
        color: #0B97D4
    }

    .dt-center a:hover {
        color: #00b4f5
    }
</style>
<div class="m-center-right">
    <div class="m-center-top">
            <div class="col-sm-4"><h3>认证管理</h3></div>

    </div>
    <div class="m-content">
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">

        </table>
    </div>
</div>

<script>

    function format_status(status) {
        var str = '未知';
        switch (status) {
            case "<?=Applicant::APPLY_STATUS_APPROVED?>":
                str = "<span class='text-gray'>已通过</span>";
                break;
            case "<?=Applicant::APPLY_STATUS_NOT_APPROVED?>":
                str = "<span class='text-green'>未审核</span>";
                break;

            default:
                break;
        }
        return str;
    }


    $(document).ready(function () {
//
//    var date_begin = get_param_value('begin');
//    var date_end = get_param_value('end');
//    if(date_begin == null) date_begin = "";
//    if(date_end == null) date_end= "";
//
//    $("input[name=date-begin]").val(date_begin);
//    $("input[name=date-end]").val(date_end);
//
//    var follower = get_param_value('p');
//    if(follower == null){follower = 'admin_gm';}
//    $("#follower-select").val(follower);


        var g_source_url = CONFIG.applicant.list_url + "?rt=json";

        var oTable = $('#list-table').DataTable({
            "processing": true,
//            "serverSide": true,   //@todo 多的时候需要实现翻页
            "pageLength": 10,
            "dom": "<'row'<'col-sm-6'f><'col-sm-6'l>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "lengthChange": false,
            "ajax": {
                "url": g_source_url
                , "dataSrc": "list"
                , "data": {
                    "lm": 200
                }
            },
            "rowId": 'app_id',
            "columns": [
                {"data": "app_id", 'title': 'id'},
                {"data": "brand_name", 'title': "品牌名", className: "user-desc-td dt-center",orderable:false},
                {"data": "name", 'title': '姓名', className: "dt-center",orderable:false},
                {"data": "telephone", 'title': '手机', className: "dt-center",orderable:false},
                {"data": "status", 'title': '状态', className: "dt-center",orderable:false},
                {"data": "create_time", 'title': '注册时间', className: "dt-center"},
                {"data": "userid", 'title': '注册id', className: "dt-center editable",orderable:false},
                {"data": null, 'title': '操作', "defaultContent": "", className: "dt-center dt-op",orderable:false}
            ],
            "order": [[0, "desc"]],
            "columnDefs": [
//username add link
                {
                    'render': function (data, type, row) {
                        return format_status(data);
                    }
                    , "targets": [4]
                },

                //后面添加的
                {
                    'render': function (data, type, row) {
                        var pub_str = "";
                        var edit_str = "";
                        if (row.status !=<?=Applicant::APPLY_STATUS_APPROVED?>)
                            edit_str = '<a href="' + CONFIG.applicant.approve_url + '/' + row.app_id + '"  class="app-approve" data-uid=' + row.userid + '><span  aria-hidden="true"></span><span class="glyphicon-class">通过</span></a>';
                        var str = edit_str;
                        return str;
                    }
                    , "targets": [7]
                },
//                后面添加的


                {
                    "visible": false, "targets": [0]
                }


            ],
            "language": {
                "zeroRecords": "暂无数据",
                "info": " 记录：_START_ - _END_ ，共 _TOTAL_ 条 ",
                "infoEmpty": "搜索无结果",
                "infoFiltered": "( 共 _MAX_ 条)",
                "search": "",
                "sSearchPlaceholder": "姓名"
            },

            "fnInitComplete": function () {
            },
            drawCallback: function (settings) {
//                var oo = $('#list-table').DataTable();
//                console.log('this:');
//                console.log(this.api());
//                if(oTable){
//                    console.log('oTable:');
//                    console.log(oTable.rows());
//                    console.log(oTable.rows().data());
//                }
//                console.log(settings);
//                console.log(oo.row(0).data());
                $('td.editable', this).editable(CONFIG.applicant.save_url, $.extend({}, defaultConfig, {name: 'userid'}));
//                $('td.editable', nRow).on('click', function() {
//                    console.log('Col Clicked.', this, aData, iDisplayIndex, iDisplayIndexFull);
//                    if (!$(this).data('eidt-init')) {
//                        var config = $.extend({}, defaultConfig);
//                        $(this).editable(CONFIG.user.save_url, config).data('eidt-init', 1);
//                    }
//                });


            }


        });


        var defaultConfig = {
            'submit': '确定',
            'cancel': '取消',
            'indicator': '提交中，请稍候...',
            'tooltip': '点击可进行编辑',
            'placeholder': '点击编辑',
            'submitdata': {},
            'event': 'click',
            'onblur': "ignore",
            'width': '100%',
            'height': '32px',
            ajaxoptions: {
                dataType: 'json'
            },
//            'data': function (value, config) {
//                var v = $('<p></p>').html(value).text();
//                return v;
//            },
            'onsubmit': function (config, element) {

                config.submitdata['applicant_id'] = oTable.row($(element).parent()).id();
//                config.submitdata['userid'] = $(':input:first', this).val();
                $(element).data('v', $(':input:first', this).val());

                //增加一个扩展
                if (config.submitHandle && typeof(config.submitHandle) == 'function') {
                    config.submitHandle.apply(this, [config, element]);
                }
            },

            callback: function (value, settings) {
                var v = $('<p></p>').text($(this).data('v')).html().replace(/\n/g, '<br>\n');

                $(this).html(v);

                if (value.res == 0) {
                    showSuccess();
                } else {
                    showErrorMessage(value.hint);
                    $(this).html(this.revert);
                }
            }

        };

//
//        $('#list-table').find('.editable').editable(CONFIG.applicant.admin_edit_url, $.extend({},defaultConfig, {
//            'type': 'text',
//        }));


        $('#list-table').on('click', '.app-approve', function (e) {
            var self = $(this);

            if (self.data('uid') < 1) {
                showErrorMessage("请先确认申请用户的userid");
                return false;
            }
            $.post(self.attr('href'), {rt: 'json', applicant_id: $(this).attr("data-pid")}, function (data) {
                if (data.res == 0) {
                    self.fadeOut();
                    showSuccess();
                } else {
                    showErrorMessage(data.hint);
                }

            }, 'json');
            return false;

        });


    });

</script>

