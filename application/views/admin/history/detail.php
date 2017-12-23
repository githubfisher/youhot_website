<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '搜索历史');
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
        <div class="col-sm-4"><h3>搜索历史详情    关键字：<?php echo $keywords;?></h3></div>
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
                        <td>用户ID</td>
                        <td>用户名</td>
                        <td>用户昵称</td>
                        <td>搜索次数</td>
                    </tr>
                </thead>
                <tbody class=".table-striped">
                <?php
                    $count = 0;
                    foreach($arr as $item){
                    $count++;
                ?>
                    <tr>
                        <td><?php echo $item["userid"];?></td>
                        <td title="<?php echo $item['username'] ?>"><?php echo substr($item["username"], 0, 3);?> **** <?php echo substr($item["username"], -4);?></td>
                        <td><?php echo $item["nickname"];?></td>
                        <td><?php echo $item["sum"];?></td>
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
