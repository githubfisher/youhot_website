<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->load_js('admin/js/ajaxfileupload.js');
$this->layout->placeholder('title', '折扣推荐');
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
        <div class="col-sm-4"><h3>折扣区管理</h3></div>
        <div class="col-sm-8"><h3 class="text-right"><a href="/admin/collection/ct1_new" class="product-add btn btn-purple pull-right">+ 添加新折扣区</a></h3></div>
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
                        <td>封面图</td>
			<td>内页图</td>
                        <td>推荐图</td>
                        <td>标题</td>
			<td>副标题</td>
                        <td>描述/备注</td>
			<td>浏览</td>
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
                        <td><img src="<?php echo $item["cover_image"];?>@50h" width="50" height="50"></td>
			<td><img src="<?php echo $item["content_image"];?>@50h" width="50" height="50"></td>
			<td><img src="<?php echo $item["recommond_image"];?>@50h" width="50" height="50"></td>
			<?php if (strlen($item["title"]) > 10) { ?>
                            <td><?php echo mb_substr($item['title'], 0, 6)."...";?></td>
                        <?php } else { ?>
                            <td><?php echo $item["title"];?></td>
                        <?php } ?>
                        <?php if (strlen($item["subhead"]) > 10) { ?>
                            <td><?php echo mb_substr($item['subhead'], 0, 6)."...";?></td>
                        <?php } else { ?>
                            <td><?php echo $item["subhead"];?></td>
                        <?php } ?>
                        <?php if (strlen($item["description"]) > 10) { ?>
                            <td><?php echo mb_substr($item["description"], 0, 6)."...";?></td>
                        <?php } else { ?>
                            <td><?php echo $item['description'];?></td>
                        <?php } ?>
                        <td><?php echo $item['view_count'];?></td>
                        <?php if ($item['status'] == 1) {?>
                            <td>上架</td>
                        <?php } else { ?>
                            <td>未启用</td>
                        <?php } ?>
                        <td>
                            <label id="label-file" for="file" onclick="setId('<?php echo $item['id']?>')">上传封面</label><input type="file" name="file" id="file" vlaue="" onchange="ajaxFileUpload();"/>
                            <a href="/admin/collection/ct1_edit?id=<?php echo $item["id"];?>">编辑</a>
                            <a href="javascript:void(0);" onclick="del('<?php echo $item["id"];?>')">删除</a>
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
                <input type="hidden" name="collection_id" id="collection_id" value="">
            </table>
        </div>
    </div>
</div>
<script>
function setId(id)
{
   document.getElementById('collection_id').value = id;
}

function ajaxFileUpload(){
    var data = {
        'collection_id':$("#collection_id").val(),
    }
    $.ajaxFileUpload({
        url:"/admin/collection/ct1_upload_cover",
        secureuri:false,
        data:data,
        fileElementId:'file', 
        dataType: 'jsonp',
        success:function(data,msg){
            showSuccess('上传成功');  
        },
        error:function(data){
            showErrorMessage('上传失败，请重试');
        }
    });
}
function del(id)
{
    var url = "/admin/collection/collection_delete"
    $.post(url,{id},function (data) {
        if (data.res == 0) {
            showSuccess();
            window.location.reload();
        } else {
            showErrorMessage(data.hint);
        }
    }, 'json');
}
</script>
