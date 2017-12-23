<?php
$this->layout->placeholder('title', '优惠券');
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
        <div class="col-sm-4"><h3>编辑优惠券</h3></div>
	<div class="col-sm-8">
            <h3 class="text-right">
                <button type="button" class="btn btn-purple" style="width:8em" onclick="sub();">保存</button>
		<button type="button" class="btn btn-purple" style="width:8em" onclick="bak();">返回</button>   
            </h3>
        </div>
    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>
            <div class="col-lg-12">
                <section class="box ">
                    <div class=" tab-pane fade in active" id="home-2">
                        <form id="product_form" name="product_form" class="form-horizontal">
                            <input type="hidden" id="coupon_id" value="<?= $store['id'] ?>"/>
                            <input type="hidden" name="rt" value="json"/>
			    <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">类型</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="type" id="type" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="1">普通</option>
                                            <option value="5">好友券</option>
                                            <option value="2">限店铺</option>
                                            <option value="3">限品类</option>
                                            <option value="4">限新注册</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12" for="field-1">名称</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="名称" style="" class="form-control" name="name"
                                               id="name" value="<?= $store['name'] ?>" required>
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">说明</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <textarea placeholder="说明" class="form-control" id="description"><?= $store['description'] ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">金额</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="金额" style="" class="form-control" name="value"
                                               id="value" value="<?= $store['value'] ?>" required>
                                    </div>
                                </div>
                            </div>
			   
			    <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">最低消费</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="最低消费" style="" class="form-control" name="limit"
                                               id="limit" value="<?= $store['limit'] ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">限用店铺</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="store" id="store" class="form-control" data-pid="" aria-invalid="false" disabled>
					     <option value="0">不限</option>
					     <?php foreach ($stores as $k => $v) { ?> 
                                               <option value="<?php echo $v['id'] ?>"><?php echo $v['show_name'] ?></option>
                                            <?php } ?>                                            
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">限用品类</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="category" id="category" class="form-control" data-pid="" aria-invalid="false" disabled>
					  <option value="0">不限</option>
                                           <?php foreach ($cates as $k => $v) { ?>
					       <option value="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></option>
					   <?php } ?>
                                        </select>
                                    </div>

                                </div>
                            </div>
	
			    <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">获取开始时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<?php if ($store['get_at'] > 0) { ?>
                                        <input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()+604800) ?>" style="" class="form-control" name="get_at"
                                               id="get_at" value="<?= date('Y-m-d H:i:s', $store['get_at']) ?>" required>
					<?php } else { ?>
					<input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()) ?>" style="" class="form-control" name="get_at"
                                               id="get_at" value="" required>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
			   <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">获取结束时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<?php if ($store['get_at'] > 0) { ?>
                                        <input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()+604800) ?>" style="" class="form-control" name="get_end"
                                               id="get_end" value="<?= date('Y-m-d H:i:s', $store['get_end']) ?>" required>
					<?php } else { ?>
					<input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()+604800) ?>" style="" class="form-control" name="get_end"
                                               id="get_end" value="" required>
					<?php } ?>	
                                    </div>
                                </div>
                            </div>
			    <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">使用开始时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<?php if ($store['get_at'] > 0) { ?>
                                        <input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()) ?>" style="" class="form-control" name="use_at"
                                               id="use_at" value="<?= date('Y-m-d H:i:s', $store['use_at']) ?>" required>
					<?php } else { ?>
					<input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()) ?>" style="" class="form-control" name="use_at"
                                               id="use_at" value="" required>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
			    <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">使用结束时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<?php if ($store['get_at'] > 0) { ?>
                                        <input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()+604800) ?>" style="" class="form-control" name="use_end"
                                               id="use_end" value="<?= date('Y-m-d H:i:s', $store['use_end']) ?>" required>
					<?php } else { ?>
					<input type="text" placeholder="<?php echo date('Y-m-d H:i:s', time()+604800) ?>" style="" class="form-control" name="use_end"
                                               id="use_end" value="" required>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

			    <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">排他性</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="is_exclusive" id="is_exclusive" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="0">否</option>
                                            <option value="1">是</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

			    <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">限定领取次数</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select id="time_limit" class="form-control" data-pid="" aria-invalid="false">
					    <option value="0">不限定</option>
                                            <option value="1">1次</option>
                                            <option value="2">2次</option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>

  			    <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">限定每次领取张数</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select id="get_limit" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="1">1张</option>
                                            <option value="2">2张</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

			    <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">限定领取手机号</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <textarea id="mobile_limit" class="form-control" rows="10" placeholder="请输入限定手机号码，以英文逗号','间隔；不限定请留空！"><?php echo $store['mobile_limit']; ?></textarea>
                                    </div>
                                    
                                </div>
                            </div>

			    <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">使用详情</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" style="" class="form-control" name="info"
                                               id="info" value="生成数量: <?= $store['created_sum'] ?>  获取数量: <?= $store['geted_sum'] ?>  使用数量: <?= $store['used_sum'] ?>" disabled>
                                    </div>
                                </div>
                            </div>

			    <div class="form-group clearfix">
                                <div class="controls">
                                    <span class="col-md-4" style="margin-left:38%;font-size:18px;"><b>以下设置仅作用于“好友券”</b></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">活动开始时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<?php if ($store['reg_at'] > 0) { ?>
                                        <input type="text" placeholder="活动开始时间: <?php echo date('Y-m-d H:i:s', time()) ?>" style="" class="form-control" name="reg_at"
                                               id="reg_at" value="<?= date('Y-m-d H:i:s', $store['reg_at']) ?>" required>
                                        <?php } else { ?>
					<input type="text" placeholder="活动开始时间: <?php echo date('Y-m-d H:i:s', time()) ?>" style="" class="form-control" name="reg_at"
                                               id="reg_at" value="" required>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                           <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">活动结束时间</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
					<?php if ($store['reg_end'] > 0) { ?>
                                        <input type="text" placeholder="活动结束时间: <?php echo date('Y-m-d H:i:s', time()+2592000) ?>" style="" class="form-control" name="reg_end"
                                               id="reg_end" value="<?= date('Y-m-d H:i:s', $store['reg_end']) ?>" required>
                                        <?php } else { ?>
                                        <input type="text" placeholder="活动结束时间: <?php echo date('Y-m-d H:i:s', time()+2592000) ?>" style="" class="form-control" name="reg_end"
                                               id="reg_end" value="" required>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">需推荐注册数量</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="推荐几个人注册后得到一张券? 3个?" style="" class="form-control" name="value" id="reg_min" value="<?php echo $store['reg_min'] ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">是否可累积获取</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select id="repeat" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="1">是，可以累计获取多张优惠券</option>
                                            <option value="0">否，只能获取一张</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-1">推荐送券</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select id="get_cid" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="0">就送本新手券</option>
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
			    <input type="hidden" id="c_type" value="<?php echo $store['type']?>">
			    <input type="hidden" id="c_store" value="<?php echo $store['store']?>">
			    <input type="hidden" id="c_category" value="<?php echo $store['category']?>">
			    <input type="hidden" id="c_is_exclusive" value="<?php echo $store['is_exclusive']?>"> 
			    <input type="hidden" id="c_get_limit" value="<?php echo $store['get_limit']?>">
			    <input type="hidden" id="c_time_limit" value="<?php echo $store['time_limit']?>">
			    <input type="hidden" id="c_repeat" value="<?php echo $store['repeat']?>">
			    <input type="hidden" id="c_get_cid" value="<?php echo $store['get_cid']?>">
                        </form>
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>
<script>
    function bak()
    {
	window.history.back();
    }
    function sub()
    {
        var id = $("#coupon_id").val();
	var type = $("#type").val();
        var name = $("#name").val();
        var value = $("#value").val();
        var limit = $("#limit").val();
        var store = $("#store").val();
        var category = $("#category").val();
        var get_at = $("#get_at").val();
        var get_end = $("#get_end").val();
        var use_at = $("#use_at").val();
        var use_end = $("#use_end").val();
        var description = $("#description").val();
	var is_exclusive = $("#is_exclusive").val();
	var time_limit = $("#time_limit").val();
        var get_limit = $("#get_limit").val();
        var mobile_limit = $("#mobile_limit").val();
        var reg_at = $("#reg_at").val();
        var reg_end = $("#reg_end").val();
        var reg_min = $("#reg_min").val();
        var repeat = $("#repeat").val();
        var get_cid = $("#get_cid").val();
        var url = '/admin/coupon/update';
        $.post(url,{id,type,name,value,limit,store,category,get_at,get_end,use_at,use_end,is_exclusive,description,time_limit,get_limit,mobile_limit,reg_at,reg_end,reg_min,repeat,get_cid},function (data) {
            if (data.res == 0) {
                showSuccess();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    $(function () {
        var store = $("#c_store").val();        
        var category = $("#c_category").val();
        var type = $("#c_type").val();
	var is_exclusive = $("#c_is_exclusive").val();
        $("#store").val(store);
        $("#category").val(category);
        $("#type").val(type);	
        $("#is_exclusive").val(is_exclusive);
	$("#get_limit").val($("#c_get_limit").val());
	$("#time_limit").val($("#c_time_limit").val());
	$("#repeat").val($("#c_repeat").val());
	$("#get_cid").val($("#c_get_cid").val());
    })
</script>


