<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '优惠活动管理');
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
        <div class="col-sm-4"><h3>优惠活动管理</h3></div>
         <div class="col-sm-8"><h3 class="text-right"><a href="/admin/store/sales_add?id=<?php echo $sales_id; ?>" class="product-add btn btn-purple pull-right">+ 添加新活动</a></h3></div>
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
                        <td>类型</td>
                        <td>活动名称</td>
                        <td>描述</td>
                        <td>状态</td>
                        <td>排序</td>
                        <td>开始时间</td>
                        <td>结束时间</td>
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
                        <?php if ($item['type'] == 1) { ?>
                                <td>满额立减</td>
                            <?php } elseif ($item['type'] == 2) { ?>
                                <td>满额折扣</td>
                            <?php } elseif ($item['type'] == 3) { ?>
                                <td>满件折扣</td>
                            <?php } elseif ($item['type'] == 4) { ?>
                                <td>满件立减</td>
                            <?php } elseif ($item['type'] == 5) { ?>
                                <td>满额免邮(商城运费)</td>
                            <?php } elseif ($item['type'] == 6) { ?>
                                <td>满额直邮</td>
                            <?php } elseif ($item['type'] == 7) { ?>
                                <td>买一送一(同款)</td>
                            <?php } elseif ($item['type'] == 8) { ?>
                                <td>买一送低(减免最低价商品)</td>
                            <?php } elseif ($item['type'] == 9) { ?>
				<td>同款第二件5折</td>	
                        <?php } ?>
                        <td><a href="http://<?php echo $item["id"];?>" target="_blank"><?php echo $item["name"];?></a></td>
		        <?php if (strlen($item['description']) < 20) { ?>
                            <td><?php echo $item["description"];?></td>
		  	<?php } else { ?>
			    <td><?php echo $item['description']; ?></td>
			<?php } ?>
                        <?php if ($item['status'] == 1) { ?>
                            <td>启用中...</td>
                        <?php } else { ?>
                            <td>停用</td>
                        <?php } ?>
                        <td><?php echo $item["order"];?></td>
			<?php if ($item['start_at'] > 0) { ?>
                            <td><?php echo date('Y-m-d H:i:s', $item["start_at"]);?></td>
                        <?php } else { ?>
			    <td></td>
                        <?php } ?>
			<?php if ($item['end_at'] > 0) { ?>
                            <td><?php echo date('Y-m-d H:i:s', $item["end_at"]);?></td>
                        <?php } else { ?>
			    <td></td>
                        <?php } ?>
                        <td>
                            <a href="/admin/store/sales_edit?id=<?php echo $item["id"];?>">编辑</a>
			    <?php if ($item['status'] == 1) { ?>
			        <a href="javascript:void(0);" onclick="update('<?php echo $item["id"];?>', 0);">暂停使用</a>
			    <?php } else { ?>
			        <a href="javascript:void(0);" onclick="update('<?php echo $item["id"];?>', 1);">重新启用</a>
			    <?php } ?>
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
function update(id, sta)
    {
        var update_url = '/admin/store/sales_status';
        $.post(update_url,{id,status:sta},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
</script>
