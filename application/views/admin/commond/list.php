<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->load_js('admin/js/ajaxfileupload.js');
$this->layout->placeholder('title', '单品推荐');
?>
<style>
    .container .row{
        width:84%;
    }
    .col-md-offset-01{
        margin-left: 2%;
    }
    input[type=file]{
			  width:0px;
			  height:0px;
			  opacity: 0;
			}
</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>单品推荐管理</h3></div>
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
			<td>封面</td>
                        <td>ID</td>
                        <td>商品描述</td>
                        <td>价格</td>
                        <td>库存</td>
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
			<td><img src="<?php echo $item['cover_image'] ?>?x-oss-process=image/resize,h_50,limit_0" width="50px" height="50px"></td>
                        <td><?php echo $item["id"];?></td>
                        <?php if (strlen($item["title"]) > 40) { ?>
                            <td><a href="<?php echo $item['m_url']?>" target="_blank"><?php echo substr($item["title"], 0, 40);?> ...</a></td>
                        <?php } else { ?>
                            <td><a href="<?php echo $item['m_url']?>" target="_blank"><?php echo $item["title"];?></a></td>
                        <?php } ?>
                        
                        <td>￥<?php echo $item["price"];?></td>
                        <td><?php echo $item["inventory"];?></td>
                        <?php if ($item['status'] == PRODUCT_STATUS_DRAFT) { ?>
                            <td style="color:yellow">未审核</td>
                        <?php } else if ($item['status'] == PRODUCT_STATUS_PUBLISHED) { ?>
                            <td style="color:green">已上架</td>
                        <?php } else { ?>
                            <td style="color:#8E2323">已下架</td>
                        <?php } ?>
                        <td>
			<label id="label-file" for="file" onclick="setId('<?php echo $item['id']?>')">上传封面</label><input type="file" name="file" id="file" vlaue="" onchange="ajaxFileUpload();"/>
			<a href="javascript:void(0);" onclick="uncommond('<?php echo $item['id']?>');" style="color:red;">取消推荐</a>
			<?php if ($item['status'] != 1) { ?>
			    <div><a href="javascript:void(0);" onclick="onsale('<?php echo $item['id'] ?>')">重新上架</a></div>
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
<input type="hidden" name="commond_id" id="commond_id" value="">
<script>
function setId(id)
{
   document.getElementById('commond_id').value = id;
}
function uncommond(id)
{
    var da = '{"is_commond":0}';
    var url = '/product/commond_product';
    $.post(url,{id:id,data:da},function(data){
	if (data.res == 0) {
	    showSuccess();
	    window.location.reload();
	} else {
	    showErrorMessage('error');
	}
    },'json');

}
function ajaxFileUpload(){
    var data = {
	'product_id':$("#commond_id").val(),
    }
    $.ajaxFileUpload({
	url:"/product/common_cover_upload",
	secureuri:false,
	data:data,
	fileElementId:'file', 
	dataType: 'jsonp',
	success:function(data,msg){
	    showSuccess('上传成功');
	    window.location.reload();	
	},
	error:function(data){
	    showErrorMessage('上传失败，请重试');
	}
    });
}
function onsale(id)
{
    var url = '/admin/product/onsale';
    $.post(url,{id},function(data){
        if (data.res == 0) {
            showSuccess();
        } else {
            showErrorMessage('error');
        }
    },'json');
}
</script>
