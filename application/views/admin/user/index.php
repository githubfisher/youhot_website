<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$js = array('admin/js/jquery/jquery.validate.js', 'admin/js/app.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
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

    table.dataTable td.dt-center {
        font-size: 12px;
    }

    .dt-center.user-edit, .user-role {
        color: #0B97D4 !important;
    }

    table#list-table tbody td.user-desc-td {
        padding-top: 20px
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


</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>用户管理</h3></div>
        <div class="col-sm-8">
            <h3 class="text-right">
                <div class="user-btn btn btn-purple">+ 添加用户</div>
            </h3>
        </div>

    </div>
    <a href="/admin/user">默认列表</a>&nbsp;&nbsp;&nbsp;
    <a href="/admin/user?t=2">设计师列表</a>&nbsp;&nbsp;&nbsp;
    <a href="/admin/user?t=2&st=1">设计师列表(已推荐)</a>
    <div class="m-content">
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">

        </table>
    </div>
</div>

<script type="text/html" id="user-add-tmpl">
    <div class="btn-content">
        <form action="" id="add-user-form" class="form-horizontal">
            <input type="hidden" name="rt" value="json">

            <div class="form-group">
                <label for="inputun" class="col-sm-2 control-label require">用户名</label>

                <div class="col-sm-10">
                    <input type="text" name="username" value="" placeholder="手机号或者邮箱" id="inputun" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <label for="inputpw" class="col-sm-2 control-label require">密码</label>

                <div class="col-sm-10">
                    <input type="password" name="password" value="" placeholder="密码" id="inputpw" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <label for="formfield6" class="col-sm-2 control-label require">角色</label>

                <div class="col-sm-10">
                    <select name="usertype" id="formfield6" class="form-control">
                        <option value=<?= USERTYPE_USER ?>>用户</option>
                        <option value=<?= USERTYPE_DESIGNER ?>>设计师</option>
                        <option value=<?= USERTYPE_BUYER ?>>买手</option>
                        <option value=<?= USERTYPE_ADMIN ?>>管理员</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" name="sumit" class="sumit-btn btn btn-purple" value="保存" style="width:8em;margin-right:1em">
                    <input type="button" data-dismiss="modal" class="sumit-btn btn btn-light-purple" style="width:8em;" value="取消">
                </div>
            </div>
        </form>
    </div>
</script>
<script>


    $(document).ready(function () {

        function format_usertype(status) {
            var str;
            switch (status) {
                case "<?=USERTYPE_ADMIN?>":
                    str = "管理员";
                    break;
                case "<?=USERTYPE_BUYER?>":
                    str = "买手";
                    break;
                case "<?=USERTYPE_DESIGNER?>":
                    str = "设计师";
                    break;
                default:
                    str = "用户";
                    break;

            }
            return str;
        }

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


        var g_source_url = CONFIG.user.list_url + "?rt=json";

        var name = 't';
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        var tval = 0;
        if (r != null){
            tval = unescape(r[2]);
        } 
        name = 'st';
        reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        r = window.location.search.substr(1).match(reg);
        var tval_st = 0;
        if (r != null){
            tval_st = unescape(r[2]);
        } 
        name = 'lm';
        reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        r = window.location.search.substr(1).match(reg);
        var lm = 5000;
        if (r != null){
            lm = unescape(r[2]);
        }

        var oTable = $('#list-table').DataTable({
            "processing": true,
            stateSave: true,
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
                    "lm": lm,
                    "t":tval,
                    "st":tval_st
                }
            },
            "columns": [
                {"data": "userid", 'title': 'id'},
                {"data": null, 'title': "用户", className: "user-desc-td dt-head-center", orderable: false},
                {"data": "username", 'title': "用户名", className: "user-desc-td dt-head-center", orderable: false},
                {"data": "usertype", 'title': '角色', className: "dt-center", orderable: false},
                {"data": "regtime", 'title': '注册时间', className: "dt-center"},
                {"data": "bp_lastlogin", 'title': '最新登录', className: "dt-center"},
                {"data": null, 'title': '操作', "defaultContent": "", className: "dt-center dt-op", orderable: false}
            ],
            "order": [[0, "desc"]],
            "columnDefs": [
//username add link
                {
                    'render': function (data, type, row) {
                        var str = ""//"<input type=checkbox name='pid' value='" + data.id + "'>";
//                        str += '<a href="' + CONFIG.user.preview_url + '?user_id=' + data.id + '"  target="_blank" class="btn btn-link">';
                        if (data.facepic != null) {
                            str += '<img class="img-circle" src="' + data.facepic + '"  >';
                        } else {
                            str += '<img class="img-circle" src="/static/admin/images/touxiang.png" >';
                        }
                        str += data.nickname;
                        if (data.isblocked == '1') {
                            str += '<sup>封</sup>';
                        }

                        return str;
                    },
                    "targets": [1]
                },
		{
                    'render': function (data, type, row) {
			    
                        return data.substr(0, 3)+'****'+data.substr(7, 4);
                    },
                    "targets": [2]
                },
                {
                    'render': function (data, type, row) {
                        return format_usertype(data);
                    },
                    "targets": [3]
                },

                {
                    'render': function (data, type, row) {
//                            console.log(row);
                        var pub_str = "";
                        var edit_str = "";
                        edit_str = '<a href="' + CONFIG.user.admin_edit_url + row.userid + '/edit"  class="user-edit" data-pid=' + row.userid + '><span  aria-hidden="true"></span><span class="glyphicon-class">修改</span></a>';
                        <?php if($role & ADMIN_ROLE_USER_PRIVILEGE):?>
//                        pub_str = '<a href="' + CONFIG.user.admin_setrole_url + '" class="user-role " data-pid="' + row.userid + '" data-role="' + row.role + '"><span >权限设置</span></a>';
                        <?php endif;?>

                        var del_str = '<a href="' + CONFIG.user.admin_edit_url + row.userid + '/delete" class="user-op" data-toggle="confirmation"><span >删除</span></a>';
                        if (row.isblocked == '1') {
                            del_str += '<a href="' + CONFIG.user.admin_edit_url + row.userid + '/unblock" class="user-op" data-toggle="confirmation" style="margin-left:1em"><span >解封</span></a>';
                        } else {

                            del_str += '<a href="' + CONFIG.user.admin_edit_url + row.userid + '/block" class="user-op" data-toggle="confirmation" style="margin-left:1em"><span >封禁</span></a>';
                        }
                        if( tval==2 ){
                            if( row.istop>'0' ){
                                del_str += '<a href="' + CONFIG.user.admin_edit_url + row.userid + '/totop?untop=1" class="user-op" data-toggle="confirmation" style="margin-left:1em"><span >[ 取消推荐 ]</span></a>';
                            }else{
                                del_str += '<a href="' + CONFIG.user.admin_edit_url + row.userid + '/totop" class="user-op" data-toggle="confirmation" style="margin-left:1em"><span >[ 推荐 ]</span></a>';
                            }
                        }
                        var str = edit_str + pub_str + del_str;

                        return str;
                    }
                    , "targets": [6]
                },

                {
                    "visible": false, "targets": [0]
                }


            ],
            "language": {
                "zeroRecords": "暂无数据",
                "info": '',//" 记录：_START_ - _END_ ，共 _TOTAL_ 条 ",
                "infoEmpty": "搜索无结果",
                "infoFiltered": "( 共 _MAX_ 条)",
                "search": "",
                "sSearchPlaceholder": "用户ID"
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
                                } else if (_url.indexOf('totop') > 0) {
                                    var op_row = oTable.row($(ele).parents('tr'));
                                    if( data.top ){
                                        op_row.data().istop = 1;
                                    }else{
                                        op_row.data().istop = 0;
                                    }
                                    op_row.invalidate().draw('page');
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
            },


        });


        $('#list-table').on('click', '.user-role', function (e) {
            var self = $(this);
            alert("暂无功能");
            return false;
            $.post(self.attr('href'), {rt: 'json', user_id: $(this).attr("data-pid"), presale_days: $(this).attr('data-presale-days')}, function (data) {
                if (data.res == 0) {
                    self.parent('td').prev().html('已下架');
                    self.fadeOut();
                } else {
                    showErrorMessage(data.hint);
                }

            }, 'json');
            return false;

        });

        $('.user-btn').click(function (e) {
//            $(".btn-content").slideToggle(200)
            $('#layout-modal').find('.modal-title').html('添加用户');
            $('#layout-modal').find('.modal-body').children().remove();
            $('#layout-modal').find('.modal-body').append($('#user-add-tmpl').html());
            $('#layout-modal').find('.modal-footer').remove();
            $('#layout-modal').modal('show');

            $('#add-user-form').validate({
                focusInvalid: false,
                ignore: "",
                rules: {
                    username: {
                        required: true,
                        minlength: 2,
                        maxlength: 40,
                        email_phone: true
                    },
                    password: {
                        required: true,
                        minlength: 4,
                        maxlength: 16,
                        alpha_dash: true
                    }
                },
                submitHandler: function (form) {
                    //update user info
                    $.post('/admin/user/create', $(form).serialize(), function (data) {
                        if (data && data.res == 0) {
                            showSuccess('保存成功');
                            $('#layout-modal').modal('hide');
                            oTable.ajax.reload();
                        } else {
                            showErrorMessage(data.hint);
                        }
                        $(".btn-content").css("display", "none")
                    }, 'json');

                    return false;
                }
            });

            return false;
        })

    });

</script>

