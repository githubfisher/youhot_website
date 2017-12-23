<?php
$this->layout->load_js('admin/js/ajaxfileupload.js');
?>
<style>
    .form-group {
        margin-bottom: 23px;
        padding-bottom: 27px;
        border-bottom: 1px solid #f2f2f2;
    }

    .desc-del {
        cursor: pointer;
    }


    .biaoqian input {
        height: 36px;
        line-height: 36px;
    }

    .biaoqian-left {
        width: 5%;
        height: auto;
        line-height: 36px;
        float: left;
        text-align: center;
        margin-left: 15px;
        font-size: 14px;
        color: #111;
        font-weight: bold;
    }

    .biaoqian-right {
        width: 95%;
        height: auto;
        line-height: 36px;
        float: left
    }

    .biaoqian-right .biaoqian-small {
        width: 140px;
        height: 36px;
        line-height: 36px;
        float: left;
        color: #111;
    }

    .biaoqian-small input[type=checkbox] {
        margin-top: 10px;
        width: 15px;
        height: 15px;
        float: left
    }

    .controls ul, li {
        list-style-type: none
    }

    .pull-box {
        width: 100%;
        height: 40px;
    }

    .pull-box-left {
        width: 65px;
        height: 40px;
        float: left;
        line-height: 40px;
    }

    .cropit-image-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        width: 752px;
        height: 802px;
        cursor: move;
    }

    .cropit-image-background {
        opacity: .2;
        cursor: auto;
    }

    .image-size-label {
        margin-top: 10px;
    }

    button[type="submit"] {
        margin-top: 10px;
    }

    /*
     * If the slider or anything else is covered by the background image,
     * use relative or absolute position on it
     */
    input.cropit-image-zoom-input {
        position: relative;
        display: inline;
        width: 60%
    }

    /* Show load indicator when image is being loaded */
    .cropit-image-preview.cropit-image-loading .spinner {
        opacity: 1;
    }

    /* Show move cursor when image has been loaded */
    .cropit-image-preview.cropit-image-loaded {
        cursor: move;
    }

    /* Gray out zoom slider when the image cannot be zoomed */
    .cropit-image-zoom-input[disabled] {
        opacity: .2;
    }

    /* Hide default file input button if you want to use a custom button */
    input.cropit-image-input {
        visibility: hidden;
    }

    /* The following styles are only relevant to when background image is enabled */

    /* Translucent background image */
    .cropit-image-background {
        opacity: .1;
    }

    /* Style the background image differently when preview area is hovered */
    .cropit-image-background.cropit-preview-hovered {
        opacity: .2;
    }


    .btn-default {
        width: 72px;
        height: 42px;
        margin-top: 10px;
        line-height: 26px;
    }

    .zidingyi li input {
        width: auto;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
    }

    .zidingyi li {
        margin-bottom: 23px;
        display: inline-block;
    }

    .zidingyi li span {
        line-height: 34px;
    }


    .control-label {
        text-align: right;
    }

    .album-del {
        position: absolute;
        right: 0em;
    }

    div.nav {
        background-color: rgb(233, 233, 233);
        height: 35px;
        line-height: 35px;
    }

    div.nav span {
        background-color: #fff;
        height: 32px;
        margin: 10px auto auto 10px;
        padding: 0 1em;
        width: 6em;
        display: block;
    }
    .album-area {
        background-color: #F8F8F8;
        border-top: 1px solid #9D9D9D;
    }
    .mail-add input{
        width:3em;
    }
    .edit2{
        display: none;
    }
    input[type=file]{
      width:0px;
      height:0px;
      opacity: 0;
    }
    .mail-add input{
        width:3em;
    }
    .edit2{
        display: none;
    }
</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>编辑运营帖3</h3></div>
    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>
            <div class="col-lg-12">
                <section class="box ">
                    <div class=" tab-pane fade in active" id="home-2">
                        <form id="product_form" name="product_form" class="form-horizontal">
                            <input type="hidden" name="collection_id" id="collection_id" value="<?= $collection['id'] ?>"/>
                            <input type="hidden" name="rt" value="json"/>
                            <input type="hidden" name="item_id" id="item_id" value=""/>
			    <input type="file" name="file" id="file" vlaue="" onchange="ajaxFileUpload();"/>
			    <input type="hidden" name="type" id="type" value=""/>
                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">主标题</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="主标题" style="" class="form-control" name="title"
                                               id="title" value="<?= $collection['title'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 128 - strlen($collection['title']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('title') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">副标题</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="副标题" style="" class="form-control" name="subhead"
                                               id="subhead" value="<?= $collection['subhead'] ?>">
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 128 - strlen($collection['subhead']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('subhead') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">描述/备注</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="官网地址" style="" class="form-control" name="description"
                                               id="description" value="<?= $collection['description'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 256 - strlen($collection['description']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('description') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">状态</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="status" id="status" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="0">停止使用</option>
                                            <option value="1">启用</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group  clearfix">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="button" class="btn btn-purple" style="width:8em" onclick="sub();">保存</button>
                                    <button type="button" class="btn btn-purple" style="width:8em" onclick="bak();">返回</button>
                                </div>
                            </div>
                            <input type="hidden" name="st" id="st" value="<?= $collection['status'] ?>">
                        </form>
                    </div>
                </section>
            </div>
            <div class="col-lg-12">
                <section class="box ">
                    <table class="table mail-add">
                        <thead style="font-weight:bold;">
                            <th width="5%">ID</th>
                            <th width="15%">图片1</th>
                            <th width="20%">文字1</th>
                            <th width="15%">图片2</th>
                            <th width="20%">文字2</th>
                            <th width="5%">商品</th>
                            <th width="5%">排序</th>
                            <th width="15%">操作</th>
                        </thead>
                        <tbody>
                            <?php foreach($collection['item_list'] as $item){ ?>
                                <tr id="<?php echo $item['id'] ?>a">
                                    <td><?php echo $item['id']?></td>
                                    <td><img src="<?php echo $item['img1']?>@80h" width="50" height="50"></td>
                                    <td><?php echo mb_substr($item['text1'],0,60)?>...</td>
                                    <td><img src="<?php echo $item['img2']?>@80h" width="50" height="50"></td>
                                    <td><?php echo mb_substr($item['text2'],0,60)?>...</td>
                                    <td>
                                    <?php if (is_array($item['products']) && count($item['products'])) {?>
                                        <?php foreach ($item['products'] as $p) {?>
                                            <?php echo $p;?> &nbsp;
                                        <?php } ?>
                                    <?php } ?>
                                    </td>
                                    <td><?php echo $item['order']?></td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="edit('<?php echo $item['id'] ?>')">编辑</a>
                                        <a href="javascript:void(0);" onclick="delItem('<?php echo $item['id'] ?>')">删除</a>
                                    </td>
                                </tr>
                                <tr id="<?php echo $item['id'] ?>b" class="edit2">
                                    <td><?php echo $item['id']?></td>
                                    <td>
                                        <label for="file" onclick="setId('<?php echo $item['id']?>','1')">
                                            <img src="<?php echo $item['img1']?>@80h" width="50" height="50">
                                        </label>
                                    </td>
                                    <td>
                                        <textarea id="<?php echo $item['id']?>text1"><?php echo $item['text1']?></textarea>
                                    </td>
                                    <td>
                                        <label for="file" onclick="setId('<?php echo $item['id']?>','2')">
                                            <img src="<?php echo $item['img2']?>@80h" width="50" height="50">
                                        </label>
                                    </td>
                                    <td>
                                        <textarea id="<?php echo $item['id']?>text2"><?php echo $item['text2']?></textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="p1" id="<?php echo $item['id']?>p1" value="<?php echo @$item['products'][0]?>" style="width:6em">
                                        <input type="text" name="p2" id="<?php echo $item['id']?>p2" value="<?php echo @$item['products'][1]?>" style="width:6em">
                                        <input type="text" name="p3" id="<?php echo $item['id']?>p3" value="<?php echo @$item['products'][2]?>" style="width:6em">
                                    </td>
                                    <td>
                                        <input type="text" name="order" id="<?php echo $item['id']?>order" value="<?php echo $item['order']?>">
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="updateItem('<?php echo $item['id'] ?>');">提交</a>
                                        <a href="javascript:void(0);" onclick="cancel('<?php echo $item['id'] ?>');">取消</a>
                                    </td>
                                </tr>
                            <?php }?>
                            <tr>
                                <td>ID</td>
                                <td>
                                    <label for="file" onclick="setId('0','1')">上传封面</label>
                                </td>
                                <td>
                                    <textarea id="text1"></textarea>
                                </td>
                                <td>
                                    <label for="file" onclick="setId('0','2')">上传封面</label>
                                </td>
                                <td>
                                    <textarea id="text2"></textarea>
                                </td>
                                <td>
                                    <input type="text" name="p1" id="p1" value="" style="width:6em">
                                    <input type="text" name="p2" id="p2" value="" style="width:6em">
                                    <input type="text" name="p3" id="p3" value="" style="width:6em">
                                </td>
                                <td>
                                    <input type="text" name="order" id="order" value="">
                                </td>
                                <td>
                                    <a href="javascript:void(0);" onclick="addItem();">提交</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>
        </section>
    </div>
</div>
<script>
    function ajaxFileUpload(){
        var item = $('#item_id').val();
	var type = $("#type").val();
        var data = {
            'collection_id':$("#collection_id").val(),
            'item_id':item,
            'type':type,
        }
        $.ajaxFileUpload({
            url:"/admin/collection/ct3_item_image",
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
    function setId(id,type)
    {
        document.getElementById("item_id").value=id;
	document.getElementById("type").value=type;
    }
    function delItem(id)
    {
        var url = '/admin/collection/ct3_del_item';
        $.post(url,{id},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    function addItem()
    {
        var collection_id = $("#collection_id").val();
        var text1 = $("#text1").val();
        var text2 = $("#text2").val();
        var order = $("#order").val();
        var p1 = $("#p1").val();
        var p2 = $("#p2").val();
        var p3 = $("#p3").val();
        var p = new Array();
        if (p1>0) {
            p.push(p1);
        }
        if (p2>0) {
            p.push(p2);
        }
        if (p3>0) {
            p.push(p3);
        }
        var products = JSON.stringify(p);
        var url = '/admin/collection/ct3_add_item';
        $.post(url,{collection_id,text1,text2,products,order},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    function updateItem(id)
    {
        var order = $("#"+id+"order").val();
        var text1 = $("#"+id+"text1").val();
        var text2 = $("#"+id+"text2").val();
        var p1 = $("#"+id+"p1").val();
        var p2 = $("#"+id+"p2").val();
        var p3 = $("#"+id+"p3").val();
        var p = new Array();
        if (p1>0) {
            p.push(p1);
        }
        if (p2>0) {
            p.push(p2);
        }
        if (p3>0) {
            p.push(p3);
        }
        var products = JSON.stringify(p);
        var url = '/admin/collection/ct3_update_item';
        $.post(url,{id,text1,text2,products,order},function (data) {
            if (data.res == 0) {
                showSuccess();
                $("#"+id+"b").hide();
                $("#"+id+"a").show();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    function cancel(id)
    {
        $("#"+id+"b").hide();
        $("#"+id+"a").show();
    }
    function edit(id)
    {
        $("#"+id+"a").hide();
        $("#"+id+"b").show();
    }
    function bak()
    {
        window.location.href="/admin/collection/ct3_list";
    }
    function sub()
    {
        var id = $("#collection_id").val();
        var title = $("#title").val();
        var description = $("#description").val();
        var status = $("#status").val();
	var subhead = $("#subhead").val();
        var url = '/admin/collection/ct3_update';
        $.post(url,{id,title,description,status,subhead},function (data) {
            if (data.res == 0) {
                showSuccess();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    $(function () {
        var status = $("#st").val();
        $("#status").val(status);
    })
</script>

