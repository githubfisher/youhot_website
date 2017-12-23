<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '分享详情');
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
        <div class="col-sm-4"><h3>分享详情</h3></div>
        <div class="col-sm-5" style="margin-top:30px;float:right;"><span class="text-right"><span style="line-height:2.4;">开始日期：<input type="text" id="start-date" value="<?= $start; ?>" style="width:80px;"/> 截止日期：<input type="text" id="end-date" value="<?= $end; ?>" style="width:80px;"/></span><a href="javascript:void(0);" class="product-add btn btn-purple pull-right" onclick="sub();">提交</a></span></div>
        <!-- <div class="col-sm-8"><h3 class="text-right"><a href="/admin/product" class="product-add btn btn-purple pull-right">+ 添加新商城</a></h3></div>-->
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
                        <td>序号</td>
                        <td>用户ID</td>
                        <td>(子)订单数</td>
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
                        <td><?= $count;?></td>
                        <td><a href="/admin/referer/detail?referer=<?= $item["referer"];?>&start=<?=$start;?>&end=<?=$end;?>" target="_blank"><?php echo $item["referer"];?></a></td>
                        <td><?= $item["sum"];?></td>
                        <td>
                            <a href="/admin/referer/detail?referer=<?= $item["referer"];?>&start=<?=$start;?>&end=<?=$end;?>" target="_blank">详情</a>
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
function sub()
{
    url = '/admin/referer/index?start=' + $("#start-date").val() + '&end=' + $("#end-date").val();
    window.location.href = url;
}
</script>
