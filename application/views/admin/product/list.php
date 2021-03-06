<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
//$this->layout->load_js('admin/modules/jeditable/js/jquery.jeditable.js');
//$this->layout->load_js('admin/modules/jeditable/js/jquery.autogrow.js');
//$this->layout->load_css();
$this->layout->placeholder('title', '商品管理');
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

    .img-thumbnail {
        margin-right: 16px
    }

    .dt-head-center.btn-link {
        color: #1E1E1E
    }

    .dt-head-center {
        width: 50%;
    }

    .dt-head-center a {
        color: #1E1E1E !important;
        font-size: 12px;
    }

    .product-delete {
        color: #EC1379;
    }

    .product-publish, .glyphicon-class {
        color: #0B97D4
    }

    div.dataTables_wrapper div div.dataTables_filter {
        text-align: left;
    }


</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>商品管理</h3></div>
        <div class="col-sm-8"><h3 class="text-right"><a href="/admin/product" class="product-add btn btn-purple pull-right " data-author="<?= $responsible_userid ?>">+ 添加新商品</a></h3></div>

    </div>

    <div class="m-content">
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">

        </table>
    </div>
</div>

<script>

    function format_pstatus(status) {

        var str = '';
        var _class = "label-warning";

        switch (status) {
            case "<?=PRODUCT_STATUS_DRAFT?>":
                str = "草稿";
                _class = "label-info";
                break;
            case "<?=PRODUCT_STATUS_PUBLISHED?>":
                _class = "label-success";
                str = "已上架";
                break;
            case "<?=PRODUCT_STATUS_INAUDIT?>":
                str = "待审核";
                break;
            case "<?=PRODUCT_STATUS_OFFSHELF?>":
                str = "已下架";
                break;
            case "<?=PRODUCT_STATUS_AUDITREFUSE?>":
                str = "审核被拒";
                break;
            case "<?=PRODUCT_STATUS_DELETED?>":
                str = "已删除";
                break;
            default:
                str = '未知';
                _class = "label-warning";
                break;
        }

//        return '<span class="label ' + _class + '">' + str + '</span>';
        return '<span>' + str + '</span>';
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

        var category_meta = {};
        $.getJSON(CONFIG.category.get_list_url, {rt: 'json'}, function (data) {
            if (data.res == 0 && data.list.length > 0) {
                category_meta = _arrange_category(data.list);
            }
        });

        function _arrange_category(data) {
            var _ok_data = {};
            var hyphen = " - ";
            if ($.isArray(data)) {
                for (var i = 0, l = data.length; i < l; i++) {
                    var cat = data[i];
                    _ok_data[cat['id']] = cat['name'];
                    if ("sub_category" in cat) {
                        for (var key in cat['sub_category']) {
                            var _sub_cat = cat['sub_category'][key];
//                            console.log(_sub_cat);
                            _ok_data[_sub_cat['id']] = cat['name'] + hyphen + _sub_cat['name'];
                        }
                    }
                }
            }
            return _ok_data;
        }

        function format_category(data) {
            return (data in category_meta) ? category_meta[data] : data;

        }


        var g_source_url = CONFIG.product.my_list + "?rt=json&userid=<?=$responsible_userid?>";
        var g_can_audit = <?php echo (isset($can_audit) && $can_audit) ? 'true':'false'?>;

        var oTable = $('#list-table').DataTable({
            "processing": true,
//            "serverSide": true,   //@todo 多的时候需要实现翻页
            "pageLength": 20,
            "ordering": false,
            "dom": "<'row'<'col-sm-6'f><'col-sm-6'l>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "lengthChange": false,
            "ajax": {
                "url": g_source_url
                , "dataSrc": function (json) {
                    if (json.res == 0) {
                        return json.list;
                    } else {
                        return [];
                    }
                }
                , "data": {
                    "lm": 200
                }
            },
            "columns": [
                {"data": "id", 'title': 'id'},
                {"data": null, 'title': '商品描述', className: "product-desc-td dt-head-center", width: "34%"},
                {"data": "price", 'title': '价格',  className: "dt-center"},
                {"data": "inventory", 'title': '库存', className: "dt-center"},
//                {"data": "production_days", 'title': '生产周期',  className: "dt-center"},
                {"data": "category", 'title': '分类',  className: "dt-center"},
                {"data": "status", 'title': '状态',  className: "dt-center"},
                {"data": null, 'title': '操作', width: "16%", "defaultContent": "", className: "dt-center dt-op"}
            ],
            "order": [[0, "desc"]],
            "columnDefs": [
//username add link
                {
                    'render': function (data, type, row) {
                        var str = "<input type=checkbox name='pid' value='" + data.id + "'>";
                        str += '<a href="' + CONFIG.product.preview_url + '?rt=mobile&product_id=' + data.id + '"  target="_blank" class="btn btn-link">';
                        if (data.cover_image != null) {
                            str += '<img class="img-thumbnail" src="' + data.cover_image + '@50h_1wh" >';
                        }
                        str += data.title;

                        str += '</a>';

                        return str;
                    },
                    "targets": [1]
                },
                {
                    'render': function (data, type, row) {
                        var str = '';
                        if (data > 0) {
                            str = "￥" + data;
                        }
                        return str;
                    },
                    "targets": [2]
                },
                {
                    'render': function (data, type, row) {
                        return data + '件';
                    },
                    "targets": [3]
                },
//                {
//                    'render': function (data, type, row) {
//                        return data + '天';
//                    },
//                    "targets": [4]
//                },
                {
                    'render': function (data, type, row) {
                        return format_category(data);
                    },
                    "targets": [4]
                },
                {
                    'render': function (data, type, row) {
                        str = format_pstatus(data);
                        return str;
                    },
                    "targets": [5]
                },
                {
                    'render': function (data, type, row) {
                        var op_str = "", audit_str;
                        var edit_str = "";

                        var edit_confirm = '';
                        edit_str = '<a href="' + CONFIG.product.admin_edit_url + row.id + '/edit"  class="product-edit" ' + edit_confirm + '  data-pid=' + row.id + '  data-toggle="confirmation" title="编辑后需要重新提交审核,确认编辑吗?">编辑</a>';
                        if (row.status == <?=PRODUCT_STATUS_DRAFT?>) {
                            //Admin can publish; Author only can submit to audit
                            if (g_can_audit) {
                                op_str = '<a href="' + CONFIG.product.publish_url + '" class="product-op " data-pid=' + row.id + ' data-presale-days=' + row.presale_days + '><span >上架</span></a>';
                            } else {
                                op_str = '<a href="' + CONFIG.product.audit_url + '" class="product-op " data-pid=' + row.id + '><span >提交审核</span></a>';
                            }
                            edit_str = '<a href="' + CONFIG.product.admin_edit_url + row.id + '/edit"  class="product-edit" ' + edit_confirm + '  data-pid=' + row.id + '  data-toggle="confirmation" title="编辑后需要重新提交审核,确认编辑吗?">编辑</a>';

                        } else if (row.status == <?=PRODUCT_STATUS_PUBLISHED?>) {
                            edit_confirm = 'data-toggle="confirmation"';
                            //Both author and admin can unpublish it
                            op_str = '<a href="' + CONFIG.product.unpublish_url + '" class="product-op" data-pid=' + row.id + ' data-presale-days=' + row.presale_days + '><span >下架</span></a>';
                        } else if (row.status == <?=PRODUCT_STATUS_INAUDIT?>) {
                            edit_confirm = 'data-toggle="confirmation"';
                            //Admin can publish
                            if (g_can_audit) {
                                op_str = '<a href="' + CONFIG.product.publish_url + '" class="product-op " data-pid=' + row.id + ' data-presale-days=' + row.presale_days + '><span >上架</span></a>';
                            }
                        }

                        var del_str = '<a href="' + CONFIG.product.delete_url + '?product_id=' + row.id + '" class="product-delete" data-toggle="confirmation" title="确定删除该商品?">删除</a>';
                        var str = edit_str + op_str + del_str;

                        str += "<a href='"+CONFIG.order.admin_index_url+"?product_id="+ row.id +"'>订单</a>";

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
                "info": " 记录：_START_ - _END_ ，共 _TOTAL_ 条 ",
                "infoEmpty": "搜索无结果",
                "infoFiltered": "( 共 _MAX_ 条)",
                "search": "",
                "sSearchPlaceholder": "商品名称"
            },

            "fnInitComplete": function () {
            },
            drawCallback: function (settings) {
                $('[data-toggle="confirmation"]').confirmation({
//                    title:'编辑后需要重新提交审核,确认编辑吗?',
                    placement: 'left',
                    btnOkClass: 'btn-purple btn btn-sm',
                    onConfirm: function (e,ele) {
                        if ($(this).attr('href').indexOf('delete') > -1) {
                            e.preventDefault();
                            console.log(ele);
                            op_del_product.call(ele);
                            return false;
                        } else {
                            return true;
                        }
                    },
//            onCancel:function(){return false;},
                    href: $(this).attr('href'),
                    btnOkLabel: '确认',
                    btnCancelLabel: "取消",
                    href: $(this).attr('href')
                });
            },


        });


        var defaultConfig = {
            'submit': '确定',
            'cancel': '取消',
            'indicator': '提交中，请稍候...',
            'tooltip': '双击可进行编辑',
            'placeholder': '双击可进行编辑',
            'submitdata': {},
            'event': 'dblclick',
            'onblur': "ignore",
            ajaxoptions: {
                dataType: 'json'
            },
            'data': function (value, config) {
                var v = $('<p></p>').html(value).text();
                return v;
            },
            'onsubmit': function (config, element) {
                var key = ['', '', '', '', '', 'status'];

                $(element).data('v', $(':input:first', this).val());
                var index = (!$(element).is('td') ? $(element).parents('td').index() : $(element).index())
                //NOTE:调整位置可能会影响取值，需要注意


                config.submitdata['pid'] = oTable.fnGetData($(element).parents('tr').get(0))['pid'];
                config.submitdata['item'] = key[index];
                config.submitdata['v'] = $(':input:first', this).val();

                //增加一个扩展
                if (config.submitHandle && typeof(config.submitHandle) == 'function') {
                    config.submitHandle.apply(this, [config, element]);
                }
            },

            callback: function (value, settings) {
                var v = $('<p></p>').text($(this).data('v')).html().replace(/\n/g, '<br>\n');

                $(this).html(v);

                var td = this;
                if (!$(td).is('td')) {
                    td = $(td).parents('td').get(0);
                }

                if (value.res == 0) {
                    A.message.success();
                    var aPos = oTable.fnGetPosition(td);
                    oTable.fnUpdate($(this).data('v'), aPos[0], aPos[1]);
                } else {
                    A.message.error(value.hint);
                    $(this).html(this.revert);
                }
            }

        };


        $('#list-table').on('click', '.product-op', function (e) {
            var self = $(this);
            $.post(self.attr('href'), {rt: 'json', product_id: $(this).attr("data-pid"), presale_days: $(this).attr('data-presale-days')}, function (data) {
                if (data.res == 0) {
                    var hint;
                    if (self.attr('href') == CONFIG.product.publish_url) {
                        hint = '已上架';
                    } else if (self.attr('href') == CONFIG.product.unpublish_url) {
                        hint = '已下架';
                    } else if (self.attr('href') == CONFIG.product.audit_url) {
                        hint = '已提交审核';
                    }
                    self.parent('td').prev().html(hint);
                    self.fadeOut();
                    showSuccess();
                } else {
                    showErrorMessage(data.hint);
                }

            }, 'json');
            return false;

        });

        function op_del_product() {
            var self = this;
            $.post($(self).data('href'), {rt: 'json'}, function (data) {
                if (data.res == 0) {
                    showSuccess();
                    $(self).parents('tr').fadeOut();
                } else {
                    showErrorMessage(data.hint);
                }

            }, 'json');
            return false;
        }

//        $('#list-table').on('click', '.product-delete', function (e) {
//            var self = $(this);
//            $.post(self.attr('href'), {rt: 'json', product_id: $(this).attr("data-pid"), presale_days: $(this).attr('data-presale-days')}, function (data) {
//                if (data.res == 0) {
//                    self.parents('tr').fadeOut();
//                } else {
//                    showErrorMessage(data.hint);
//                }
//
//            }, 'json');
//            return false;
//
//        });
        $('.product-add').on('click', '', function (e) {
            var author = $(this).attr('data-author');
            $self = $(this);
            if (author.length < 1) {
                $.getJSON('/user/designers?rt=json', function (data) {
                    if (data.res == 0) {
                        var _designers = data.list;
                        var _options = [];
                        $.each(_designers, function (idx, ele) {
                            _options.push('<option value=' + ele.userid + '>' + ele.nickname + '</option>');
                        })
                        var _html = '<select name="author" class="form-control"><option value="0" selected>请选择设计师</option>' + _options.join('') + '</select>';
                        $(_html).appendTo($("#layout-modal .modal-body"));
                        $("#layout-modal .modal-body").html(_html);
                        $("#layout-modal .modal-title").html("选择设计师");
//                        $("#layout-modal .modal-footer .btn-primary").html("下一步");
                        $("#layout-modal .modal-footer .btn-primary").on('click', function () {
                            $("#layout-modal").modal('hide');
                            $self.trigger('click');
                        });
                        $("#layout-modal").modal('show');
//                        $self.parent().append(_html);
                    } else {
                        showErrorMessage(data.hint + '.请稍后再试');
                    }
                })

                return false;
            }
            if (author.length < 1) {
                alert('设计师信息为空,请检查');
                return false;
            }

            $.post(CONFIG.product.create_url, {"rt": 'json', title: "商品名称", "author": author}, function (data) {
                if (data.res != 0) {
                    showErrorMessage(data.hint);
                } else {
                    location.href = CONFIG.product.admin_edit_url + data.id + '/edit';
                }

            }, 'json');
            return false;

        });

        $("#layout-modal").on('change', 'select[name=author]', function () {
            console.log($(':selected', this).val());
            $("a.product-add").attr('data-author', $(':selected', this).val());
        });

    });


</script>

