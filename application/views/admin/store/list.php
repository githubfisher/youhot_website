<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '商城管理');
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
        <div class="col-sm-4"><h3>商城管理</h3></div>
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
                        <td>商城名称</td>
                        <td>所在国家</td>
                        <td>货币</td>
                        <td>快递直邮</td>
                        <td>一号仓</td>
                        <td>官网</td>
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
                        <td><a href="http://<?php echo $item["url"];?>" target="_blank"><?php echo $item["name"];?></a></td>
                        <td><?php echo $item["country"];?></td>
                        <td><?php echo $item["currency"];?></td>
                        <?php if ($item['direct_mail'] == 1) { ?>
                            <td>支持</td>
                        <?php } else { ?>
                            <td>不支持</td>
                        <?php } ?>
			<?php if ($item['number_one'] == 1) { ?>
                            <td>支持</td>
                        <?php } else { ?>
                            <td>不支持</td>
                        <?php } ?>
                        <td><?php echo $item["url"];?></td>
                        <td>
                            <a href="/admin/store/edit?id=<?php echo $item["id"];?>">编辑</a>
			    <a href="/admin/store/sales?id=<?php echo $item["id"];?>">优惠活动</a>
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
