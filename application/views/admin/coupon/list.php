<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '优惠券');
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
        <div class="col-sm-4"><h3>优惠券管理</h3></div>
        <div class="col-sm-8"><h3 class="text-right"><a href="/admin/coupon/nnew" class="product-add btn btn-purple pull-right">+ 添加新优惠券</a></h3></div>
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
                        <td>名称</td>
                        <td>类型</td>
                        <td>金额</td>
                        <td>最低消费</td>
			<td>限店铺</td>
			<td>限品类</td>
			<td>排他</td>
			<td>限定</td>
                        <td>总量</td>
			<td>获取</td>
			<td>使用</td>
			<td>分享链接</td>
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
                        <td><?php echo $item["name"];?></td>
			<?php if ($item['type'] == 1) { ?>
			    <td>普通</td>
			<?php } elseif ($item['type'] == 2) { ?>
			    <td>限店铺</td>
			<?php } elseif ($item['type'] == 3) { ?>
			    <td>限品类</td>
			<?php } elseif ($item['type'] == 4) { ?>
			    <td>限新注册</td>
			<?php } else { ?>
                            <td>好友券</td>
			<?php } ?>
                        <td><?php echo $item["value"];?></td>
                        <td><?php echo $item["limit"];?></td>
			<?php if ($item['store'] == 0) { ?>
                            <td>不限</td>
			<?php } else { ?>
                            <td>限店铺</td>
                        <?php } ?>
			<?php if ($item['category'] == 0) { ?>
                            <td>不限</td>
                        <?php } else { ?>
                            <td>限品类</td>
                        <?php } ?>
			<?php if ($item['is_exclusive'] == 0) { ?>
                            <td>否</td>
                        <?php } else { ?>
                            <td>是</td>
                        <?php } ?>
			<td>
			    <?php if ($item['time_limit'] == 0) { ?>
                                不限次<br/>
                            <?php } else { ?>
                                限<?php echo $item['time_limit']; ?>次<br/>
                            <?php } ?>
                            每次<?php echo $item['get_limit']; ?>张<br/>
			    <?php if (empty($item['mobile_limit'])) { ?>
                                不限手机
                            <?php } else { ?>
                                <span style="color:red">限手机</span>
                            <?php } ?>
			</td>
                        <td><?php echo $item["created_sum"];?></td>
                        <td><?php echo $item["geted_sum"];?></td>
                        <td><?php echo $item["used_sum"];?></td>
			<td>
			    <?php if ($item['type'] == COUPON_TYPE_NEWER) {
                                echo 'youhot.com.cn/coupon/showFriendCoupon';
                             } else {
                                echo 'youhot.com.cn/coupon/showc?id='.$item['id'];
                             } ?>
			</td>
                        <td>
                            <a href="/admin/coupon/edit?id=<?php echo $item["id"];?>">管理</a><br/>
			   <input type="text" id="<?php echo $item["id"];?>sum" value="100" style="width:3em"><br/>
                            <a href="javascript:void(0);" onclick="create('<?php echo $item["id"];?>');">发放</a>
                           <!--  <a href="/admin/coupon/export?id=<?php echo $item["id"];?>">导出</a> -->
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
   function create(id)
   {
	var sum = $("#"+id+"sum").val();
	if (parseInt(sum) <= 0) {
	    alert('请输入发放优惠券数量');
	    return true;
	}
        var url = '/admin/coupon/create';
        $.post(url,{id,sum:parseInt(sum)},function (data) {
            if (data.res == 0) {
                showSuccess();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
   }
</script>

