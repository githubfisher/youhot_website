<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/modules/jeditable/jquery.jeditable.js?' . STATIC_ADMIN_VERSION . '.js');
$js = array('admin/js/jquery/jquery.validate.js', 'admin/js/app.js');

$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');

$this->layout->load_js($js);
//$this->layout->load_css();
$this->layout->placeholder('title', '推荐管理');
?>
<script>
    $(function () {

        $('.user-btn').click(function (e) {
            $(".btn-content").slideToggle(200)
            return false;
        })
    })
</script>
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

    table.dataTable td.dt-center {

        font-size: 12px;
    }

    .dt-center.user-edit, .user-role {
        color: #0B97D4 !important;
    }

    .col-sm-12 {
        width: 94%;
        margin-left: 32px
    }

    .m-center-top {
        height: 80px;
        padding-top: 35px
    }

    .m-center-top span {
        margin-left: 34px;
        font-weight: bold
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
</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="m-center-top-left" style="float:left">
            <span style="line-height: 30px;font-size:20px">推荐管理</span><br>
            <span style="font-weight: normal;color:#000000;font-size:14px"></span>
        </div>
        <div class="btn-content-box ">
            <div id="cat-btn-add" class="user-btn">添加
            </div>
            <div id="cat-content" style="padding-left:10px;display:none">
                <form action="/admin/banner/create" id="add-cat-form">
                    <input type="hidden" name="rt" value="json">

                    <div class="form-group">
                        <label for="input1">文字</label>
                        <input type="text" id="input1" name="text" value="" placeholder="文字" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="input2">图片</label>
                        <input type="text" id="input2" name="pic" value="" placeholder="图片" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="input4">类型</label>
                        <select id="input4" name="type" value="" class="form-control">
                            <option value="-1">选择类型</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="input5">url</label>
                        <input type="text" id="input5" name="url" value="" placeholder="url" class="form-control">
                        <span class="desc">商品:pid=112&sell_type=1(预售1,正常销售2);专辑:cid=112;直播:url</span>
                    </div>
                    <div class="form-group"><input type="submit" name="sumit" class="btn btn-primary" value="提交"></div>
                </form>
            </div>
        </div>


    </div>
    <div class="m-content">
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">

        </table>
    </div>
</div>

<script>


    $(document).ready(function () {

        var type_data = <?php echo json_encode([1=>'商品',2=>'专辑',3=>'直播'])?>;

        $('#cat-btn-add').on('click', function (e) {
            $('#layout-modal').modal('show');
        });
        $('#layout-modal').on('show.bs.modal', function (event) {
            //                var button = $(event.relatedTarget) // Button that triggered the modal
            //                var recipient = button.data('whatever') // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.

            var modal = $(this);
            if (modal.find('.modal-body').find("#cat-content").length > 0) {
                return;
            }
            $.each(type_data, function (key, value) {
                $('select[name=type]')
                    .append($("<option></option>")
                        .attr("value", key)
                        .text(value));

            });

            modal.find('.modal-title').html('添加新推荐');
            modal.find('.modal-body').html("").append($('#cat-content').show());
            modal.find('.modal-footer').remove();
        });

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


        var g_source_url = CONFIG.banner.get_list_url + "?rt=json";

        var oTable = $('#list-table').DataTable({
            "processing": true,
            stateSave: true,
//            "serverSide": true,   //@todo 多的时候需要实现翻页
            "pageLength": 10,
            "dom": "<'row'<'col-sm-6'f><'col-sm-6'l>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "lengthChange": false,
            "rowId": 'bid',
            "ajax": {
                "url": g_source_url
                , "dataSrc": "list"
                , "data": {
                    "lm": 200
                }

            },
            "columns": [
                {"data": "bid", 'title': 'id'},
                {"data": "text", 'title': "文字", className: "user-desc-td dt-head-center editable", width: "200px"},
                {"data": "pic", 'title': '图片', width: "100px", className: "dt-center  editable"},
                {"data": "type", 'title': '类型', width: "100px", className: "dt-center"},
                {"data": "url", 'title': 'url', width: "100px", className: "dt-center  editable"},
                {"data": null, 'title': '操作', width: "110px", "defaultContent": "", className: "dt-center dt-op"}
            ],
            "order": [[0, "asc"]],
            "columnDefs": [
//username add link
                {
                    'render': function (data, type, row) {
                        return type_data[data];
                    },
                    "targets": [3]
                },

                {
                    'render': function (data, type, row) {
//                            console.log(row);
                        var edit_str = "";
//                        edit_str = '<a href="' + CONFIG.user.admin_edit_url + row.userid + '/edit"  class="user-edit" data-pid=' + row.userid + '><span  aria-hidden="true"></span><span class="glyphicon-class">修改</span></a>';

                        var del_str = '<a href="' + CONFIG.banner.delete_url + '?bid=' + row.bid + '" class="user-op" data-toggle="confirmation"><span >删除</span></a>';

                        var str = del_str;

                        return str;
                    }
                    , "targets": [-1]
                },
//
//                {
//                    "visible": false, "targets": [0]
//                }


            ],
            "language": {
                "zeroRecords": "暂无数据",
                "info": " 记录：_START_ - _END_ ，共 _TOTAL_ 条 ",
                "infoEmpty": "搜索无结果",
                "infoFiltered": "( 共 _MAX_ 条)",
                "search": "",
//                "sSearchPlaceholder": "用户ID"
            },

            "fnInitComplete": function () {
            },
            drawCallback: function (settings) {
                $('[data-toggle="confirmation"]').confirmation({
                    placement: 'top',
                    onConfirm: function (e, ele) {
                        e.preventDefault();
                        e.stopPropagation();
                        var _url = $(ele).parent().find('[data-apply=confirmation]').attr('href');

                        $.post(_url, {rt: 'json'}, function (data) {
                            if (data.res == 0) {
                                showSuccess();
                                if (_url.indexOf('delete') > 0) {
                                    $(ele).parents('tr').fadeOut();
                                } else {
                                    var bv = (_url.indexOf('unblock') > 0) ? '0' : '1'
                                    var op_row = oTable.row($(ele).parents('tr'));
                                    op_row.data().isblocked = bv;
                                    op_row.invalidate().draw('page');

                                }
                            } else {
                                showErrorMessage(data.hint);
                            }

                        }, 'json');
                        return false;

                    },
//            onCancel:function(){return false;},
                    href: $(this).attr('href'),
                    btnOkLabel: '确认',
                });
                $('td.editable').editable(CONFIG.banner.update_url, $.extend({}, defaultConfig, {
                    "submitHandle": function (config, element) {

                    }
                }));

            },


        });


        var defaultConfig = {
            'submit': '确定',
            'cancel': '取消',
            'indicator': '提交中，请稍候...',
            'tooltip': '点击可进行编辑',
            'placeholder': ' ',
            'submitdata': {},
            'event': 'click',
            'onblur': "ignore",
            'width': '100%',
            'height': '32px',
            ajaxoptions: {
                dataType: 'json'
            },
            'data': function (value, config) {
                console.log(value);
                var v = $('<p></p>').html(value).text();
                return v;
            },
            'onsubmit': function (config, element) {
                console.log($(element).parent());
                var c = oTable.cell($(element));

                var idx = c.index().column;
                var keyname = oTable.column(idx).dataSrc();

                config.submitdata['bid'] = oTable.row($(element).parent()).id();
                config.submitdata[keyname] = $(':input:first', this).val();
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
//        var conf = $.extend({},Admin.config.editable);


//        $('#list-table').on('click', '.user-op', function (e) {
//            var self = $(this);
//            $.post(self.attr('href'), {rt: 'json'}, function (data) {
//                if (data.res == 0) {
//                    self.fadeOut();
//                    oTable.row(self.parents('tr')).remove().draw();
//                } else {
//                    showErrorMessage(data.hint);
//                }
//
//            }, 'json');
//            return false;
//
//        });

        $('form#add-cat-form').validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                text: {
                    required: true,
                    minlength: 2,
                    maxlength: 200,
                },
                type: 'digits',
            },
            submitHandler: function (form) {
                //update user info
                console.log(form);
                $.post(CONFIG.banner.add_url, $(form).serialize(), function (data) {
                    if (data && data.res == 0) {
                        showSuccess('保存成功');
                        oTable.ajax.reload();
                        $('#layout-modal').modal('hide');

                    } else {
                        showErrorMessage(data.hint);
                    }
//                    $(".btn-content").css("display", "none")
                }, 'json');

                return false;
            }
        });

    });

</script>

