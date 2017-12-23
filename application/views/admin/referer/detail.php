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
        <div class="col-sm-7"><h3>推荐详情 ID：<?php echo $referer;?><!-- 手机：<?= $arr[0]['username']; ?>--></h3></div>
	<input type="hidden" id="referer" value="<?php echo $referer;?>" />
	<div class="col-sm-5" style="margin-top:30px;float:right;"><span class="text-right"><span style="line-height:2.4;">开始日期：<input type="text" id="start-date" value="<?= $start; ?>" style="width:80px;height:30px;"/> 截止日期：<input type="text" id="end-date" value="<?= $end; ?>" style="width:80px;height:30px;"/></span><a href="javascript:void(0);" class="product-add btn btn-purple pull-right" onclick="sub();">提交</a></span></div>
        <!-- <div class="col-sm-8"><h3 class="text-right"><a href="/admin/product" class="product-add btn btn-purple pull-right">+ 添加新商城</a></h3></div>-->
    </div>
    <div class="m-content">
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        </table>
    </div>
</div>
<div class="container">
    <div class="row" style="overflow:auto;">
        <div class="col-md-12 col-md-offset-01">
            <table class="table text-center" style="width:150%;max-width:200%;">
                <thead>
                    <tr style="font-weight:bold;">
                        <!--<td>推荐人</td>-->
                        <td>订单号</td>
			<td>订单状态</td>
                        <td>商品</td>
                        <!--<td>商品标题</td>-->
                        <td>商品单价</td>
                        <td>商品数量</td>
                        <td>购买用户</td>
                        <td>父订单</td>
			<td>总订单状态</td>
			<td>总运费</td>
			<td>总税费</td>
			<td>商城优惠</td>
			<td>优惠券</td>
			<td>支付方式</td>
			<td>支付金额</td>
			<td>支付时间</td>
			<td>支付单号</td>
			<!--<td></td>-->
                    </tr>
                </thead>
                <tbody class=".table-striped">
                <?php
		    $status_obj = [
        		ORDER_STATUS_DELETE =>  "已删除",
        		ORDER_STATUS_INIT =>  "预定(未付款)",
        		ORDER_STATUS_PRE_PAID =>  "预定(已付款)",
        		ORDER_STATUS_LAST_PAY_START =>  "付尾款(开始)",
        		ORDER_STATUS_LAST_PAID => "付尾款(已付)",
        		ORDER_STATUS_SHIP_START =>  "已发货",
        		ORDER_STATUS_SHIP_RECEIVED => "已收货",
        		ORDER_STATUS_END_SUCCEED => "结束(成功)",
        		ORDER_STATUS_END_FAIL => "结束(失败)",
    		    ];
                    foreach($arr as $item){
                ?>
                    <tr>
                        <!--<td><?php echo $item["username"];?></td>-->
			<td><?= $item['order_id']; ?></td>
			<td><?= $status_obj[$item['status']]; ?></td>
			<td><img class="" src="<?= $item['product_cover_image']; ?>?x-oss-process=image/resize,h_50,limit_0" width="50px" height="50px"></td>
			<!--<td><?= $item['product_title']; ?></td>-->
			<td><?= $item['product_price']; ?></td>
			<td><?= $item['product_count']; ?></td>
			<td><?= $item['nickname']; ?></td>
			<td><?= $item['pid']; ?></td>
			<?php if ($item['pid'] > 0) { ?>
				<td><?= $status_obj[$item['pstatus']]; ?></td>
			<?php } else { ?>
				<td><?= $status_obj[$item['status']]; ?></td>
			<?php } ?>
			<td><?= $item['freight']; ?></td>
			<td><?= $item['tax']; ?></td>
			<td><?= $item['promotion_value']; ?></td>
			<td><?= $item['last_pay_coupon_value']; ?></td>
			<td><?= $item['last_payment']; ?></td>
			<td><?= $item['last_paid_money']; ?></td>
			<td><?= $item['last_paid_time']; ?></td>
			<td><?= $item['last_paid_payinfo']; ?></td>
			<!--<td><?= $item['']; ?></td>-->
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
    url = '/admin/referer/detail?referer=' + $("#referer").val() + '&start=' + $("#start-date").val() + '&end=' + $("#end-date").val();
    window.location.href = url;
}
</script>
