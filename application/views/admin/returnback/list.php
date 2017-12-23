<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '售后管理');
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
        <div class="col-sm-4"><h3>售后单管理</h3></div>
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
                        <td>ID</td>
                        <td>订单号</td>
                        <td>买家ID</td>
                        <td>类型</td>
                        <td>退款金额</td>
			<td>状态</td>
			<td>申请时间</td>
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
                        <td><a href="/admin/order/detail/<?php echo $item["order_id"];?>" target="_blank"><?php echo $item["order_id"];?></a></td>
                        <td><?php echo $item["userid"];?></td>
                        <?php if ($item['type'] == 1) { ?>
                            <td>退款</td>
                        <?php } else { ?>
                            <td>退货</td>
                        <?php } ?>
			<td><?php echo $item["price"];?></td>
                        <?php if ($item['status'] == 0) { ?>
                            <td>待处理</td>
                        <?php } elseif ($item['status'] == 1) { ?>
                            <td>已完成</td>
                        <?php } elseif ($item['status'] == 2) {?>
			    <td>拒绝</td>
			<?php } elseif ($item['status'] == 3) {?>
                            <td>退款中</td>
			<?php } elseif ($item['status'] == 4) {?>
                            <td>退货中</td>
			<?php } ?>
                        <td><?php echo date('Y-m-d H:i:s', $item["create_at"]);?></td>
                        <td>
                            <a href="/admin/returnback/edit?id=<?php echo $item["id"];?>">管理</a>
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
