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
        <div class="col-sm-4"><h3>编辑商城</h3></div>
    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>
            <div class="col-lg-12">
                <section class="box ">
                    <div class=" tab-pane fade in active" id="home-2">
                        <form id="product_form" name="product_form" class="form-horizontal">
                            <input type="hidden" name="store_id" value="<?= $store['id'] ?>"/>
                            <input type="hidden" name="rt" value="json"/>
                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12" for="field-1">检索名称</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="检索名称" style="" class="form-control" name="name"
                                               id="name" value="<?= $store['name'] ?>" disabled>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 32 - strlen($store['name']) ?></span>个字
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
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12" for="field-1">显示名称</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="显示名称" style="" class="form-control" name="show_name"
                                               id="show_name" value="<?= $store['show_name'] ?>">
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 64 - strlen($store['show_name']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('show_name') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12" for="field-1">官网地址</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="官网地址" style="" class="form-control" name="url"
                                               id="url" value="<?= $store['url'] ?>">
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 64 - strlen($store['url']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('url') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">国家/地区</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="country" id="country" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="USA">美国/USA</option>
                                            <option value="HK">中国香港/HK</option>
                                            <option value="IT">意大利/IT</option>
                                            <option value="FRA">法国/FRA</option>
                                            <option value="UK">英国/UK</option>
                                            <option value="CA">加拿大/CA</option>
                                            <option value="JP">日本/JP</option>
                                            <option value="KR">韩国/KR</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">货币</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="currency" id="currency" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="USD">美元/USD</option>
                                            <option value="HKD">港币/HKD</option>
                                            <option value="GBP">英镑/GBP</option>
                                            <option value="EUR">欧元/EUR</option>
                                            <option value="CAD">加拿大元/CAD</option>
                                            <option value="JPY">日元/JPY</option>
                                            <option value="KRW">韩元/KRW</option>
                                            <option value="CHF">法郎/CHF</option>
                                            <option value="RMB">人民币/RMB</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">快递直邮</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="direct_mail" id="direct_mail" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="1">支持</option>
                                            <option value="0">不支持</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

  			   <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">快递直邮_税率</label>
				    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="" style="" class="form-control" id="direct_rate" value="<?= $store['direct_mail_rate'] ?>" required>
                                    </div>
                                </div>
                            </div>

			    <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">一号仓</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="number_one" id="number_one" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="1">支持</option>
                                            <option value="0">不支持</option>
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
                            <input type="hidden" id="sid" value="<?= $store['id'] ?>">
                            <input type="hidden" id="ctry" value="<?= $store['country'] ?>">
                            <input type="hidden" id="cenry" value="<?= $store['currency'] ?>">
                            <input type="hidden" id="dmail" value="<?= $store['direct_mail'] ?>">
                            <input type="hidden" id="numberone" value="<?= $store['number_one'] ?>">
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
                        <td width="3%">ID</td>
                        <td width="10%">方式</td>
                        <td width="10%">国家/地区</td>
                        <td width="8%">货币</td>
                        <td width="8%">MIN消费</td>
			<td width="8%">MAX消费</td>	
                        <td width="3%">运费</td>
                        <td width="6%">基运</td>
                        <td width="12%">时效（天）</td>
			<td width="8%">类型</td>
			<td width="8%">单位</td>
                        <td width="8%">状态</td>
                        <td width="8%">操作</td>
                    </tr>
                </thead>
                <tbody class=".table-striped">
                <?php
                    $count = 0;
                    foreach($shipping as $item){
                    $count++;
                ?>
                    <tr id="<?php echo $item['id'] ?>a">
                        <td><?php echo $item["id"];?></td>
                        <?php if ($item['type'] == 1) { ?>
                            <td>直邮</td>
                        <?php } else if ($item['type'] == 2) { ?>
                            <td>商家转运</td>
                        <?php } else if ($item['type'] == 3) { ?>
                            <td>中间转运</td>
			<?php } else if ($item['type'] == 4) { ?>
                            <td>奢侈品牌</td>
			<?php } else if ($item['type'] == 5) { ?>
                            <td>转运（缴税）</td>
                        <?php } ?>
                        <td><?php echo $item["country"];?></td>
                        <td><?php echo $item["currency"];?></td>
                        <td><?php echo $item["low"];?></td>
			<td><?php echo $item["high"];?></td>
                        <td><?php echo $item["low_fee"];?></td>
                        <td><?php echo $item["base_fee"];?></td>
                        <td><?php echo $item["days"];?></td>
			<?php if ($item['count_type'] == 3) { ?>
			    <td>计重</td>
			<?php } elseif ($item['count_type'] == 2) { ?>
			   <td>计件</td>
			<?php } else { ?>
			   <td>计费</td>
			<?php } ?>
			<td><?php echo $item['count_unit'] ?></td> 
                        <?php if ($item['status'] == 1) { ?>
                            <td>启用</td>
                        <?php } else { ?>
                            <td>停用</td>
                        <?php } ?>
                        <td>
                            <a href="javascript:void(0);" onclick="edit('<?php echo $item['id'] ?>','<?php echo $item['type'] ?>','<?php echo $item['country'] ?>','<?php echo $item['currency'] ?>','<?php echo $item['status'] ?>','<?php echo $item['count_type'] ?>', '<?php echo $item['count_unit'] ?>')">编辑</a>
                        </td>
                    </tr>
                    <tr id="<?php echo $item['id'] ?>b" class="edit2 mail-add">
                        <td><?php echo $item["id"];?></td>
                        <td>
                            <select name="<?php echo $item['id'] ?>ty" id="<?php echo $item['id'] ?>ty" class="form-control" data-pid="" aria-invalid="false">
                                <option value="1">直邮</option>
                                <option value="2">商家转运</option>
                                <option value="3">中间转运</option>
				<option value="4">奢侈品牌</option>
				<option value="5">转运（缴税）</option>
                            </select>
                        </td>
                        <td>
                            <select name="<?php echo $item['id'] ?>cy" id="<?php echo $item['id'] ?>cy" class="form-control" data-pid="" aria-invalid="false">
                                <option value="USA">美国</option>
                                <option value="HK">中国香港</option>
                                <option value="IT">意大利</option>
                                <option value="FRA">法国</option>
                                <option value="UK">英国</option>
                                <option value="CA">加拿大</option>
                                <option value="JP">日本</option>
                                <option value="KR">韩国</option>
				<option value="GER">德国</option>
                            </select>
                        </td>
                        <td>
                            <select name="<?php echo $item['id'] ?>ccy" id="<?php echo $item['id'] ?>ccy" class="form-control" data-pid="" aria-invalid="false">
                                <option value="USD">美元</option>
                                <option value="HKD">港币</option>
                                <option value="GBP">英镑</option>
                                <option value="EUR">欧元</option>
                                <option value="CAD">加元</option>
                                <option value="JPY">日元</option>
                                <option value="KRW">韩元</option>
                                <option value="CHF">法郎</option>
                                <option value="RMB">人民币</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="<?php echo $item['id'] ?>low" id ="<?php echo $item['id'] ?>low" value="<?php echo $item['low'] ?>">
                        </td>
			<td>
                            <input type="text" name="<?php echo $item['id'] ?>high" id ="<?php echo $item['id'] ?>high" value="<?php echo $item['high'] ?>">
                        </td>
                        <td>
                            <input type="text" name="<?php echo $item['id'] ?>lf" id ="<?php echo $item['id'] ?>lf" value="<?php echo $item['low_fee'] ?>">
                        </td>
                        <td>
                            <input type="text" name="<?php echo $item['id'] ?>hf" id ="<?php echo $item['id'] ?>hf" value="<?php echo $item['base_fee'] ?>">
                        </td>
                        <td>
                            <input type="text" name="<?php echo $item['id'] ?>ld" id ="<?php echo $item['id'] ?>ld" value="<?php echo strchr($item['days'],'-',true) ?>" style="width:2em">-
                            <input type="text" name="<?php echo $item['id'] ?>hd" id ="<?php echo $item['id'] ?>hd" value="<?php echo ltrim(strchr($item['days'],'-'),'-') ?>" style="width:2em">
                        </td>
			<td>
                            <select name="<?php echo $item['id'] ?>ct" id="<?php echo $item['id'] ?>ct" class="form-control" data-pid="" aria-invalid="false">
                                <option value="1">计费</option>
                                <option value="2">计件</option>
                                <option value="3">计重</option>
                            </select>
                        </td>
                        <td>
                            <select name="<?php echo $item['id'] ?>cu" id="<?php echo $item['id'] ?>cu" class="form-control" data-pid="" aria-invalid="false">
                                <option value="YUAN">元</option>
                                <option value="LBS">磅</option>
                                <option value="KG">千克</option>
                                <option value="JIN">斤</option>
                            </select>
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
                            <select name="ty" id="ty" class="form-control" data-pid="" aria-invalid="false">
				<option value="2">商家转运</option>
                                <option value="1">直邮</option>
                                <option value="3">中间转运</option>
				<option value="4">奢侈品</option>
				<option value="5">转运（缴税）</option>
                            </select>
                        </td>
                        <td>
                            <select name="cy" id="cy" class="form-control" data-pid="" aria-invalid="false">
                                <option value="USA">美国</option>
                                <option value="HK">中国香港</option>
                                <option value="IT">意大利</option>
                                <option value="FRA">法国</option>
                                <option value="UK">英国</option>
                                <option value="CA">加拿大</option>
                                <option value="JP">日本</option>
                                <option value="KR">韩国</option>
				<option value="GER">德国</option>
                            </select>
                        </td>
                        <td>
                            <select name="ccy" id="ccy" class="form-control" data-pid="" aria-invalid="false">
                                <option value="USD">美元</option>
                                <option value="HKD">港币</option>
                                <option value="GBP">英镑</option>
                                <option value="EUR">欧元</option>
                                <option value="CAD">加元</option>
                                <option value="JPY">日元</option>
                                <option value="KRW">韩元</option>
                                <option value="CHF">法郎</option>
                                <option value="RMB">人民币</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="low" id ="low" value="">
                        </td>
			<td>
                            <input type="text" name="high" id ="high" value="">
                        </td>
                        <td>
                            <input type="text" name="lf" id ="lf" value="">
                        </td>
                        <td>
                            <input type="text" name="hf" id ="hf" value="">
                        </td>
                        <td>
                            <input type="text" name="ld" id ="ld" value="" style="width:2em">-
                            <input type="text" name="hd" id ="hd" value="" style="width:2em">
                        </td>
			<td>
			    <select name="ct" id="ct" class="form-control" data-pid="" aria-invalid="false">
                                <option value="1">计费</option>
                                <option value="2">计件</option>
                                <option value="3">计重</option>
                            </select>
			</td>
			<td>
                            <select name="cu" id="cu" class="form-control" data-pid="" aria-invalid="false">
                                <option value="YUAN">元</option>
                                <option value="LBS">磅</option>
                                <option value="KG">千克</option>
				<option value="JIN">斤</option>
                            </select>
                        </td>
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
        //window.location.href="/admin/store";
	window.history.back();
    }
    function cancel(id)
    {
        $("#"+id+"b").hide();
        $("#"+id+"a").show();
    }
    function edit(id,type,cy,ccy,status,ct,cu)
    {
        $("#"+id+"cy").val(cy);
        $("#"+id+"ccy").val(ccy);
        $("#"+id+"ty").val(type);
        $("#"+id+"status").val(status);
	$("#"+id+"ct").val(ct);
	$("#"+id+"cu").val(cu);
        $("#"+id+"a").hide();
        $("#"+id+"b").show();
    }
    function update(id)
    {
        var type = $("#"+id+"ty").val();
        var country = $("#"+id+"cy").val();
        var currency = $("#"+id+"ccy").val();
        var high = $("#"+id+"high").val();
        var low = $("#"+id+"low").val();
        var low_fee = $("#"+id+"lf").val();
        var base_fee = $("#"+id+"hf").val();
        var ld = $("#"+id+"ld").val();
        var hd = $("#"+id+"hd").val();
	var days = '';
	if ((ld.length > 0) && (ld.length > 0)) {
	    days = ld+'-'+hd;
	}
        var status = $("#"+id+"status").val();
	var count_type = $("#"+id+"ct").val();
	var count_unit = $("#"+id+"cu").val();
        var update_url = '/admin/store/shipping_update';
        $.post(update_url,{id,type,country,currency,high,low,low_fee,base_fee,days:days,status,count_type,count_unit},function (data) {
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
        var sid = $("#sid").val();
        var type = $("#ty").val();
        var country = $("#cy").val();
        var currency = $("#ccy").val();
        var high = $("#high").val();
        var low = $("#low").val();
        var low_fee = $("#lf").val();
        var base_fee = $("#hf").val();
        var ld = $("#ld").val();
        var hd = $("#hd").val();
	var days = '';
        if ((ld.length > 0) && (ld.length > 0)) {
            days = ld+'-'+hd;
        }
	var count_type = $("#ct").val();
        var count_unit = $("#cu").val();
        var add_url = '/admin/store/shipping_add';
        $.post(add_url,{sid,type,country,currency,high,low,low_fee,base_fee,days:days,count_type,count_unit},function (data) {
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
        var id = $("#sid").val();
        var name = $("#name").val();
        var show_name = $("#show_name").val();
        var country = $("#country").val();
        var currency = $("#currency").val();
        var url = $("#url").val();
        var direct_mail = $("#direct_mail").val();
        var number_one = $("#number_one").val();
        var direct_mail_rate = $("#direct_rate").val();
        var update_url = '/admin/store/update';
        $.post(update_url,{id,name,show_name,country,currency,url,direct_mail,number_one,direct_mail_rate},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    $(function () {
        var cry = $("#ctry").val();        
        var cey = $("#cenry").val();
        var mal = $("#dmail").val();
        $("#country").val(cry);
        $("#currency").val(cey);
        $("#direct_mail").val(mal);
        $("#number_one").val($("#numberone").val());
    })
</script>

