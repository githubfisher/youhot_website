<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '分类管理');
?>
<style>
    .container .row{
        width:84%;
    }
    .col-md-offset-01{
        margin-left: 2%;
    }
    .edit2{
        display: none;
    }
</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>分类管理</h3></div>
        <div class="col-sm-8"><h3 class="text-right"><a href="/admin/category/brand_category_list" class="product-add btn btn-purple pull-right">品牌税重管理</a></h3></div>
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
                        <td>分类名称</td>
                        <td>中文翻译</td>
                        <td>重量（Kg）</td>
                        <td>关税税率</td>
                        <td>是否显示</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody class=".table-striped" style="text-align:left;">
                <?php
		   getTreeData($arr);
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function edit(id, show)
    {
        $("#"+id+"show").val(show);
        $("#"+id+"a").hide();
        $("#"+id+"b").show();
    }
function cancel(id)
    {
        $("#"+id+"b").hide();
        $("#"+id+"a").show();
    }
function sub(id)
    {
        var chinese_name = $("#"+id+"chname").val();
        var tax_rate = $("#"+id+"trate").val();
        var weight = $("#"+id+"wgt").val();
        var update_url = '/admin/category/update_info';
        $.post(update_url,{id, chinese_name, weight, tax_rate},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
</script>
