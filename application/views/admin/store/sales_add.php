<?php
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
</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>添加优惠活动</h3></div>
    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>
            <div class="col-lg-12">
                <section class="box ">
                    <div class=" tab-pane fade in active" id="home-2">
                        <form id="product_form" name="product_form" class="form-horizontal">
                            <input type="hidden" name="store_id" id="store_id" value="<?= $sales_id ?>"/>
                            <input type="hidden" name="rt" value="json"/>
                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">名称</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="名称" style="" class="form-control" name="name"
                                               id="name" value="" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint">32</span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('name') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">描述</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="描述" style="" class="form-control" name="description"
                                               id="description" value="" >
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint">64</span>个字
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
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">类型</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="type" id="type" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="1">满额立减</option>
                                            <option value="2">满额折扣</option>
                                            <option value="3">满件折扣</option>
					    <!--<option value="4">满件立减</option>-->
                                            <option value="5">满额免邮(商城运费)</option>
                                           <!-- <option value="6">满额直邮</option>
                                            <option value="7">买一送一(同款)</option>
                                            <option value="8">买一送低(减免最低价商品)</option> -->
                                            <option value="9">同款第二件5折</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">状态</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="status" id="status" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="1">启用</option>
                                            <option value="0">暂停使用</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">开始时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                       <input type="text" placeholder="<?= date('Y-m-d', time()); ?>" class="form-contrl" name="start_at" id="start_at" value="">
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">截止时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<input type="text" placeholder="<?= date('Y-m-d', time() + 7 * 86400); ?>" class="form-contrl" name="end_at" id="end_at" value="">
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">排序</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="排序" class="form-contrl" name="order" id="order" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group  clearfix">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="button" class="btn btn-purple" style="width:8em" onclick="sub();">保存</button>
				    <button type="button" class="btn btn-purple" style="width:8em" onclick="bak();">返回</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </section>
    </div>
<script>
    function bak()
    {
	window.history.back();
    }
    function sub()
    {
        var id = $("#store_id").val();
        var name = $("#name").val();
        var description = $("#description").val();
        var type = $("#type").val();
        var status = $("#status").val();
        var start = $("#start_at").val();
        var end = $("#end_at").val();
        var order = $("#order").val();
        var add_url = '/admin/store/sales_new_add';
        $.post(add_url,{id,name,description,type,status,start,end,order},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
</script>

