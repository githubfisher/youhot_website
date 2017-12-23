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
        <div class="col-sm-4"><h3>编辑优惠活动</h3></div>
    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>
            <div class="col-lg-12">
                <section class="box ">
                    <div class=" tab-pane fade in active" id="home-2">
                        <form id="product_form" name="product_form" class="form-horizontal">
                            <input type="hidden" name="sales_id" id="sales_id" value="<?= $sales['id'] ?>"/>
                            <input type="hidden" name="rt" value="json"/>
                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">名称</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="名称" style="" class="form-control" name="name"
                                               id="name" value="<?= $sales['name'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 32 - strlen($sales['name']) ?></span>个字
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
                                               id="description" value="<?= $sales['description'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 64 - strlen($sales['description']) ?></span>个字
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
					<?php if ($sales['start_at'] > 0) { ?>
                                            <input type="text" placeholder="<?= date('Y-m-d', time()); ?>" class="form-control" name="start_at"
                                               id="start_at" value="<?= date('Y-m-d H:i:s', $sales['start_at']) ?>">
					<?php } else { ?>
                                                 <input type="text" placeholder="<?= date('Y-m-d', time()); ?>" class="form-contrl" name="start_at" id="start_at" value="">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">截止时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<?php if ($sales['end_at'] > 0) { ?>
                                            <input type="text" placeholder="<?= date('Y-m-d', time() + 7 * 86400); ?>" class="form-control" name="end_at"
                                               id="end_at" value="<?= date('Y-m-d H:i:s', $sales['end_at']) ?>">
					<?php } else { ?>
						<input type="text" placeholder="<?= date('Y-m-d', time() + 7 * 86400); ?>" class="form-contrl" name="end_at" id="end_at" value="">
					<?php } ?>
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">排序</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<input type="text" placeholder="排序" class="form-contrl" name="order" id="order" value="<?php echo $sales['order']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group  clearfix">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="button" class="btn btn-purple" style="width:8em" onclick="sub();">保存</button>
				    <button type="button" class="btn btn-purple" style="width:8em" onclick="bak();">返回</button>
                                </div>
                            </div>
                            <input type="hidden" name="s_status" id="s_status" value="<?= $sales['status'] ?>">
                            <input type="hidden" name="s_type" id="s_type" value="<?= $sales['type'] ?>">
                        </form>
                    </div>
                </section>
            </div>
        </section>
    </div>
    <div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-01">
            <table class="table text-center">
                <thead>
                    <tr style="font-weight:bold;">
                        <td width="5%">ID</td>
                        <td width="15%">货币</td>
                        <td width="10%">LowerLimit</td>
			<td width="10%">UpperLimit</td>	
                        <td width="10%">优惠内容</td>
                        <td width="10%">状态</td>
                        <td width="10%">操作</td>
                    </tr>
                </thead>
                <tbody class=".table-striped">
                <?php
                    $count = 0;
                    foreach($options as $item){
                    $count++;
                ?>
                    <tr id="<?php echo $item['id'] ?>a">
                        <td><?php echo $item["id"];?></td>
                        <td><?php echo $item["currency"];?></td>
			<td><?php echo $item['lower'] ?></td> 
			<td><?php echo $item['upper'] ?></td> 
			<td><?php echo $item['promotion'] ?></td> 
                        <?php if ($item['status'] == 1) { ?>
                            <td>启用</td>
                        <?php } else { ?>
                            <td>停用</td>
                        <?php } ?>
                        <td>
                            <a href="javascript:void(0);" onclick="edit('<?php echo $item['id'] ?>','<?php echo $item['currency'] ?>','<?php echo $item['status'] ?>')">编辑</a>
                        </td>
                    </tr>
                    <tr id="<?php echo $item['id'] ?>b" class="edit2 mail-add">
                        <td><?php echo $item["id"];?></td>
                        <td>
                            <select name="<?php echo $item['id'] ?>ccy" id="<?php echo $item['id'] ?>ccy" class="form-control" data-pid="" aria-invalid="false">
                                <option value="USD">美元/USD</option>
                                <option value="HKD">港币/HKD</option>
                                <option value="GBP">英镑/GBP</option>
                                <option value="EUR">欧元/EUR</option>
                                <option value="CAD">加元/CAD</option>
                                <option value="JPY">日元/JPY</option>
                                <option value="KRW">韩元/KRW</option>
                                <option value="CHF">法郎/CHF</option>
                                <option value="RMB">人民币/RMB</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="<?php echo $item['id'] ?>low" id ="<?php echo $item['id'] ?>low" value="<?php echo $item['lower'] ?>">
                        </td>
			<td>
                            <input type="text" name="<?php echo $item['id'] ?>high" id ="<?php echo $item['id'] ?>high" value="<?php echo $item['upper'] ?>">
                        </td>
                        <td>
                            <input type="text" name="<?php echo $item['id'] ?>lf" id ="<?php echo $item['id'] ?>promotion" value="<?php echo $item['promotion'] ?>">
                        </td>
                        <td>
                            <select name="<?php echo $item['id'] ?>status" id="<?php echo $item['id'] ?>status" class="form-control" data-pid="" aria-invalid="false">
                                <option value="1">启用</option>
                                <option value="0">停用</option>
                            </select>
                        </td>
                        <td>
                            <a href="javascript:void(0);" onclick="update('<?php echo $item['id'] ?>');">提交</a>
                            <a href="javascript:void(0);" onclick="cancel('<?php echo $item['id'] ?>');">取消</a>
                        </td>
                    </tr>
                <?php }?>
                    <tr style="color:white;"><td colspan="11">空白行</td></tr>
                    <tr class="mail-add">
                        <td></td>
                        <td>
                            <select name="ccy" id="ccy" class="form-control" data-pid="" aria-invalid="false">
                                <option value="USD">美元/USD</option>
                                <option value="HKD">港币/HKD</option>
                                <option value="GBP">英镑/GBR</option>
                                <option value="EUR">欧元/EUR</option>
                                <option value="CAD">加元/CAD</option>
                                <option value="JPY">日元/JPY</option>
                                <option value="KRW">韩元/KRW</option>
                                <option value="CHF">法郎/CHF</option>
                                <option value="RMB">人民币/RMB</option>
                            </select>
                        </td>
                        <td><input type="text" name="low" id ="low" value=""></td>
			<td><input type="text" name="high" id ="high" value=""></td>
                        <td><input type="text" name="promotion" id ="promotion" value=""></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-purple" style="width:6em" onclick="add();">添加</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<script>
    function bak()
    {
	window.history.back();
    }
    function cancel(id)
    {
        $("#"+id+"b").hide();
        $("#"+id+"a").show();
    }
    function edit(id,ccy,status)
    {
        $("#"+id+"ccy").val(ccy);
        $("#"+id+"status").val(status);
        $("#"+id+"a").hide();
        $("#"+id+"b").show();
    }
    function update(id)
    {
        var currency = $("#"+id+"ccy").val();
        var upper = $("#"+id+"high").val();
        var lower = $("#"+id+"low").val();
        var promotion = $("#"+id+"promotion").val();
        var status = $("#"+id+"status").val();
        var update_url = '/admin/store/sales_options_update';
        $.post(update_url,{id,currency,upper,lower,promotion,status},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    function add()
    {
        var id = $("#sales_id").val();
        var currency = $("#ccy").val();
        var upper = $("#high").val();
        var lower = $("#low").val();
        var promotion = $("#promotion").val();
        var add_url = '/admin/store/sales_options_add';
        $.post(add_url,{id,currency,upper,lower,promotion},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    function sub()
    {
        var id = $("#sales_id").val();
        var name = $("#name").val();
        var description = $("#description").val();
        var type = $("#type").val();
        var status = $("#status").val();
        var start = $("#start_at").val();
        var end = $("#end_at").val();
        var order = $("#order").val();
        var update_url = '/admin/store/sales_update';
        $.post(update_url,{id,name,description,type,status,start,end,order},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    $(function () {
        var sta = $("#s_status").val();        
        var type = $("#s_type").val();
        $("#status").val(sta);
        $("#type").val(type);
    })
</script>

