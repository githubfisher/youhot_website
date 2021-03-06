<?php
$this->layout->placeholder('title', '尺码图片');
?>
<style>
    table.dataTable th {
        text-align: left;
    }

    .editable {
        min-height: 24px;
        min-width: 30px;
        border: 1px dashed #efefef;
        cursor: pointer;
    }

    .to-extend {
        height: 75px;
        overflow: hidden;
        cursor: pointer;
        text-overflow: ellipsis;
    }

    .img-thumbnail {
        margin-right: 16px
    }

    .dt-head-center.btn-link {
        color: #1E1E1E
    }

    .dt-head-center {
        width: 50%;
    }

    .dt-head-center a {
        color: #1E1E1E !important;
        font-size: 12px;
    }

    .product-delete {
        color: #EC1379;
    }

    .product-publish, .glyphicon-class {
        color: #0B97D4
    }

    div.dataTables_wrapper div div.dataTables_filter {
        text-align: left;
    }

    #userfile{
    	display:none;
    }
    #sub-label{
    	display:none;
    }
</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>码图管理</h3></div>
        <div class="col-sm-8">
            <h5 style="float: left;padding-top: 20px;">尺码图命名规则：分类名#品牌名；空格用英文下划线 _ 代替； & 符号用 @ 符号代替。例如：Duffels_@_Totes#DevaCurl</h5>
            <h3 class="text-right" style="float: right;">
                <?php echo form_open_multipart('/admin/size/create');?>
                <label for="userfile" class="product-add btn btn-purple pull-right" onclick="userfile.click()" id="pick-img">
                    <input type="file" name="userfile" id="userfile" onchange="change();">
                    上传尺码图
                </label>
                <label class="product-add btn btn-purple pull-right" for="sub" id="sub-label" onclick="exchange();">
                    <input name="subdo" id="subdo" type="submit" value="上传"/>
                </label>
            </h3>
        </div>
    </div>
    <div class="m-content">
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        	<thead>
                <tr role="row">
                    <th class="sorting_disabled product-desc-td">尺码表</th>
                    <th class="sorting_disabled dt-center">分类</th>
                    <th class="sorting_disabled dt-center">品牌</th>
                    <th class="sorting_disabled dt-center">操作员</th>
                    <th class="sorting_disabled dt-center">上传时间</th>
                    <th class="sorting_disabled dt-center">操作</th>
                </tr>
            </thead>
            <?php foreach ($list as $item): ?>
            	<tr class="odd" role="row">
            		<td class=" product-desc-td"><img src="<?= $item['url'] ?>@50h" width="300" height="50"></td>
            		<td class=" dt-center"><?= $item['category'] ?></td>
            		<td class=" dt-center"><?= $item['brand'] ?></td>
            		<td class=" dt-center"><?= $item['username'] ?></td>
            		<td class=" dt-center"><?= date('Y-m-d H:i:s',$item['create_at']) ?></td>
            		<td class=" dt-center">
                        <a href="/admin/size/edit?id=<?= $item['id'] ?>">编辑</a> <a href="/admin/size/delete?id=<?= $item['id'] ?>&url=<?= $item['url'] ?>" style="color:red;">删除</a>
                        <?php if ($item['is_default'] == 0): ?> <a href="/admin/size/set_default?id=<?= $item['id'] ?>">设为默认</a>
                        <?php endif; ?>
                    </td>
            	</tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<script type="text/javascript">  
	function change()
	{
		document.getElementById("subdo").click();
	}
</script>
