<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '商品管理');
?>
<style>
    .container .row{
        width:84%;
    }
    .col-md-offset-01{
        margin-left: 2%;
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
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-01">
            <table class="table text-center">
                <thead>
                    <tr style="font-weight:bold;">
                        <td>ID</td>
                        <td>商品描述</td>
                        <td>价格</td>
                        <td>库存</td>
                        <td>分类</td>
                        <td>状态</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody class=".table-striped">
                <?php
                    $count = 0;
                    foreach($arr as $item){
                    $count++;
                ?>
                    <tr>
                        <td><?php echo $item["id"];?></td>
                        <?php if (strlen($item["title"]) > 40) { ?>
                            <td><a href=""><?php echo substr($item["title"], 0, 40);?> ...</a></td>
                        <?php } else { ?>
                            <td><a href=""><?php echo $item["title"];?></a></td>
                        <?php } ?>
                        
                        <td>￥<?php echo $item["price"];?></td>
                        <td><?php echo $item["inventory"];?></td>
                        <td><?php echo $item["name"];?></td>
                        <?php if ($item['status'] == PRODUCT_STATUS_DRAFT) { ?>
                            <td>未审核</td>
                        <?php } else if ($item['status'] == PRODUCT_STATUS_PUBLISHED) { ?>
                            <td>已上架</td>
                        <?php } else { ?>
                            <td>已下架</td>
                        <?php } ?>
                        <td>
                            <table>
                                <tr>
                                    <td width="20%"><a href="/admin/product/<?php echo $item["id"];?>/edit">编辑</a></td>
                                    <td width="20%"><a href="/admin/order?product_id=<?php echo $item["id"];?>">订单</a></td>
                                    <?php if ($item['status'] == PRODUCT_STATUS_DRAFT) { ?>
                                        <td width="20%"><a href="javascript:void(0);" onclick="sply('<?php echo $item["id"];?>');">审核</a></td>
                                    <?php } else if ($item['status'] == PRODUCT_STATUS_PUBLISHED) { ?>
                                        <td width="20%"><a href="javascript:void(0);" onclick="unpublish('<?php echo $item["id"];?>');">下架</a></td>
                                    <?php } else { ?>
                                        <td width="20%"><a href="javascript:void(0);" onclick="publish('<?php echo $item["id"];?>');">上架</a></td>
                                    <?php } ?>
                                    <td width="20%"><a href="javascript:void(0);" id="del-btn<?php echo $item["id"];?>" style="color:red;" onclick="delete_tip('<?php echo $item["id"];?>');">删除</a></td>
                                    <div class="popover fade left in" id="confirmation<?php echo $item["id"];?>" style="top: 50px; left: 782.383px; display: none;">    <div class="arrow" style="top: 50%;"></div>
                                        <h3 class="popover-title">确定删除该商品?</h3>
                                        <div class="popover-content">
                                            <a data-apply="confirmation" class="btn-purple btn btn-sm" href="javascript:void(0);" target="_self" onclick="delete('<?php echo $item["id"];?>');"><i class="glyphicon glyphicon-ok"></i> 确认</a>
                                            <a data-dismiss="confirmation" class="btn btn-sm btn-default" onclick="delete_untip('<?php echo $item["id"];?>');"><i class="glyphicon glyphicon-remove"></i> 取消</a>
                                        </div>
                                    </div>
                                </tr>
                            </table>
                        </td>
                    </tr>

                <?php }?>
                <tr>
                    <td colspan="5">
                        <nav>
                            <?php echo $links; ?>
                        </nav>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
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
        return '<span>' + str + '</span>';
    }


    $(document).ready(function () {
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

        // product add
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

    // 写到这里了 ，加弹出框， 写ajax请求
    function delete_tip(id)
    {
        var top= $("#del-btn"+id).offset().top;   //获取div的高度
        var left = $("#del-btn"+id).offset().left;       //获取div的宽度
        var d_top = 274;
        if (window.screen.width >= 1920) {
            var d_left = 565;
        }  else if (window.screen.width >= 1280) {
            var d_left = 460;
        } else if (window.screen.width >= 1180) {
            var d_left = 440;
        } else {
            var d_left = 380;
        }
        var c_top = top-d_top;
        var c_left = left-d_left;
        $("#confirmation"+id).css("top", c_top+'px');
        $("#confirmation"+id).css("left", c_left+'px');
        $("#confirmation"+id).fadeIn(100);
    }
    function delete_untip(id)
    {
        $("#confirmation"+id).fadeOut(100);
    }
    function sply(id)
    {
        $.post("/admin/product/to_audit", {rt: 'json',product_id:id}, function (data) {
            if (data.res == 0) {
                showSuccess();
                $(self).parents('tr').fadeOut();
            } else {
                showErrorMessage(data.hint);
            }

        }, 'json');
        return false;
    }
    function unpublish(id)
    {
        $.post("/admin/product/unpublish", {rt: 'json',product_id:id}, function (data) {
            if (data.res == 0) {
                showSuccess();
                $(self).parents('tr').fadeOut();
            } else {
                showErrorMessage(data.hint);
            }

        }, 'json');
        return false;
    }
    function publish(id)
    {
        $.post("/admin/product/publish", {rt: 'json', product_id:id, presale_days:0}, function (data) {
            if (data.res == 0) {
                showSuccess();
                $(self).parents('tr').fadeOut();
            } else {
                showErrorMessage(data.hint);
            }

        }, 'json');
        return false;
    }
</script>
