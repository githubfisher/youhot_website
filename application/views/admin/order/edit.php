<?php
$this->layout->placeholder('title', '订单编辑');
$this->layout->load_js('admin/plugins/jquery-validation/js/jquery.validate.js');
?>
<style>
    .dd-msg {
        border-left: 2px solid #3c3c3c;
        padding-left: 7px;
        line-height: 10px;
        margin-top: 17px;
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 14px;
    }


    .dingdan{width:100%;height:41px;background:#F4F5F9;
        line-height:41px;}
    .dingdan span{margin-left:29px;font-size:16px;}
    .dingdan-middle{width:94%;
        margin:0 auto;
    }

    .line {
        height: 1px;
        border-top: 1px solid #e8e8e8;
        margin: 14px -23px 18px;
    }
</style>
<?php $disabledType = '';
if($user['usertype'] != USERTYPE_ADMIN ){
    $disabledType = 'disabled';
}
?>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>订单编辑</h3></div>
        <div class="col-sm-8"><h3 class="text-right"></h3></div>
    </div>
    <div class="m-content">
        <!--顶部结束-->
        <!-- 中部内容开始 -->
        <div class="ding-center" style="width:100%;height:auto;border:1px solid #e8e8e8">
            <div class="dingdan"><span>修改订单</span></div>
            <div class="dingdan-middle">
                <?php foreach($order as $od) { ?>
               <!-- <div class="dd-msg">订单信息</div> -->
                <div class="row">
                    <div class="col-sm-6">
                        <?php if (($od['pid'] == 0) && empty($od['product_title'])) { ?>
                            <span style="font-size:20px;">主订单</span>
                        <?php } else { ?>
                            <img src="<?= $od['product_cover_image'] ?>"  width="80px" height="80px"/>
                            <span><?= $od['product_title'] ?></span>
                            <p><a href="<?= $od['m_url'] ?>" target="_blank">下单链接</a></p>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6 text-right" style="font-size:12px">
                        <p>订单编号:<?= $od['order_id'] ?></p>
                        <!-- <p>下单时间:<?= $od['create_time'] ?></p>
                        <p>总价 = 商品单价 x 数量 + 单个商品运费 x 数量 - 优惠金额</p>-->
			<?php if (($od['pid'] == 0) && empty($od['product_title'])) { ?>
			    <p>下单时间:<?= $od['create_time'] ?></p>
			    <p>支付时间:<?= $od['last_paid_time'] ?></p>
			<?php } ?>
                    </div>
                </div>
		<hr>
                <!-- 订单信息 -->
                <!-- <div class="dd-msg">订单信息</div> -->
                <form id="order_form" name="order_form" class="form-horizontal" method="post" action="/order/update">
                    <input type="hidden" name="order_id" value="<?= $order_id ?>"/>
                    <input type="hidden" name="rt" value="json"/>
                    <!--价格一栏-->
                    <div class="form-group">
                        <div class="controls">
			    <?php if (($od['pid'] == 0) && empty($od['product_title'])) { ?>
			    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-price"  >商品总价</label>
			    <div class="col-sm-3 col-md-3 col-xs-10">
                                <div class="input-group">
                                    <input type="text" id="field-price" class="form-control" name="product_price" value="<?= $od['product_price'] ?>" <?php echo $disabledType;?> integer required>
                                    <div class="input-group-addon">元</div>
                                </div>
                            </div>
			    <?php } else { ?>
                            <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-price"  >商品单价</label>
			    <div class="col-sm-3 col-md-3 col-xs-10">
                                <div class="input-group">
                                    <input type="text" id="field-price" class="form-control" name="product_price" value="<?= $od['product_price'] ?>" <?php echo $disabledType;?> integer required disabled>
                                    <div class="input-group-addon">元</div>
                                </div>
                            </div>
			    <?php } ?>
			    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">数量</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="product_count" id="input-presale_maximum" value="<?= $od['product_count'] ?>" <?php echo $disabledType;?>  min="0" integer disabled>
                                    <div class="input-group-addon">件</div>
                                </div>
                                <div>
                                    <span class="error"><?= form_error('product_count') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--最大限量一栏-->
                    <div class="form-group">
                        <div class="controls">
			   <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-11">尺码</label>
			   <div class="col-sm-3 col-md-3 col-xs-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="product_size" id="input-presale_maximum" value="<?= $od['product_size'] ?>" integer disabled>
                                </div>
                            </div>
			    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-color">颜色</label>
			    <div class="col-sm-3 col-md-3 col-xs-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="product_color" id="input-presale_maximum" value="<?= $od['product_color'] ?>" <?php echo $disabledType;?>  min="0" integer disabled>
                                </div>
                            </div>
                        </div>
                    </div>
		    <?php if (($od['pid'] == 0) && empty($od['product_title'])) { ?>
		    <div class="form-group">
                        <div class="controls">
			    <label class="control-label col-sm-2 col-md-2 col-xs-2" for="field-price">运费</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" id="" class="form-control" name="freight" value="<?= $od['freight'] ?>">
                            </div>
			    <label class="control-label col-sm-2 col-md-2 col-xs-2" for="field-price">税费</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" id="" class="form-control" name="tax" value="<?= $od['tax'] ?>">
                            </div>
			</div>
                    </div>
		    <div class="form-group">
                        <div class="controls">
                            <label class="control-label col-sm-2 col-md-2 col-xs-2" for="field-price">优惠</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" id="" class="form-control" name="last_pay_coupon_value" value="<?= $od['last_pay_coupon_value'] ?>">
                            </div>
                            <label class="control-label col-sm-2 col-md-2 col-xs-2" for="field-price">支付</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" id="" class="form-control" name="last_paid_money" value="<?= $od['last_paid_money'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="controls">
                            <label class="control-label col-sm-2 col-md-2 col-xs-2" for="field-price">备注信息</label>
                            <div class="col-sm-8 col-md-8 col-xs-8">
                                <input type="text" id="field-memo" class="form-control" name="memo" value="<?= $od['memo'] ?>">
                                <div>
                                    <span class="error"><?= form_error('memo') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
		    <?php } ?>
		    <div class="line"></div>
                    <?php } ?>
                    <div class="form-group" style="margin-top: 3em">
                        <div class="col-md-2"><span class="dd-msg">收货信息</span></div>
                        <div class="col-md-10">
			    <?php if (count($order) > 1) { ?>
                            <?php foreach ($order as $odr) { ?>
                                <?php if (($odr['pid'] == 0) && empty($odr['product_title'])) { ?>
                                    <?php if ($odr['status'] == ORDER_STATUS_LAST_PAID): ?>
					<button data-toggle="modal" data-target="#shipModal" type="button" class="btn btn-purple btn-sm">发货</button>
                                    <?php endif; ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($order[0]['status'] == ORDER_STATUS_LAST_PAID): ?>
                                 <button data-toggle="modal" data-target="#shipModal" type="button" class="btn btn-purple btn-sm">发货</button>
                            <?php endif; ?>
                        <?php } ?>
                        </div>
                    </div>

                    <!--价格一栏-->
                    <!-- <div class="form-group">
                        <label class="control-label col-sm-2 col-md-2 col-xs-2 ">单个商品运费</label>
                        <div class="col-sm-3 col-md-3 col-xs-10">
                            <div class="input-group">
                                <input type="text" id="field-freight" class="form-control" name="freight" value="<?= $order[0]['freight'] ?>" <?php echo $disabledType;?>>

                                <div class="input-group-addon">元</div>
                            </div>
                            <div>
                                <span class="error"><?= form_error('freight') ?></span>
                            </div>
                        </div>

                        <label class="control-label col-sm-2 col-md-2 col-xs-2 " class="control-label" for="field-courier_number">单号</label>

                        <div class="col-sm-3 col-md-3 col-xs-10">
                            <input type="text" id="field-courier_number" class="form-control" name="courier_number" value="<?= $order[0]['courier_number'] ?>">

                        </div>
                    </div> -->
		    <?php if (count($order) > 1) { ?>
                    <?php foreach ($order as $odr) { ?>
                        <?php if (($odr['pid'] == 0) && empty($odr['product_title'])) { ?>
			<div class="form-group">
                        <div class="controls">
                            <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-price">姓名</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" id="field-receiver" class="form-control" name="receiver" value="<?= $odr['receiver'] ?>" disabled>
                            </div>
                            <label class="control-label col-sm-2 col-md-2 col-xs-2 " class="control-label" for="field-1">电话</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" class="form-control" name="phone_num" value="<?= $odr['phone_num'] ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-price">详细地址</label>
                        <div class="col-sm-8 col-md-8 col-xs-10">
                            <input type="text" id="field-price" class="form-control" name="address" value="<?= $odr['address'] ?>" disabled>
                        </div>
                    </div>
			<?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <div class="form-group">
                        <div class="controls">
                            <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-price">姓名</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" id="field-receiver" class="form-control" name="receiver" value="<?= $order[0]['receiver'] ?>" disabled>
                            </div>
                            <label class="control-label col-sm-2 col-md-2 col-xs-2 " class="control-label" for="field-1">电话</label>
                            <div class="col-sm-3 col-md-3 col-xs-10">
                                <input type="text" class="form-control" name="phone_num" value="<?= $order[0]['phone_num'] ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 col-md-2 col-xs-2 " for="field-price">详细地址</label>
                        <div class="col-sm-8 col-md-8 col-xs-10">
                            <input type="text" id="field-price" class="form-control" name="address" value="<?= $order[0]['address'] ?>" disabled>
                        </div>
                    </div>
		    <?php } ?>
                    <div class="form-group">
                        <div class="col-sm-3 col-md-3 col-sm-offset-7 col-md-offset-7">
                            <input type="submit"  class="btn btn-purple" name="submit" value="保存" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="shipModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">发货信息</h4>
            </div>
            <form action="/order/update" id="ship-form" method="post">
                <div class="modal-body">
		    <?php if (count($order) > 1) { ?>
                            <?php foreach ($order as $odr) { ?>
                                <?php if (($odr['pid'] == 0) && empty($odr['product_title'])) { ?>
                                    <?php if ($odr['status'] == ORDER_STATUS_LAST_PAID): ?>
					<input type="hidden" name="order_id" value="<?= $odr['order_id'] ?>">
                                    <?php endif; ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($order[0]['status'] == ORDER_STATUS_LAST_PAID): ?>
				<input type="hidden" name="order_id" value="<?= $order[0]['order_id'] ?>">
                            <?php endif; ?>
                        <?php } ?>
                    <input type="hidden" name="status" value="<?= ORDER_STATUS_SHIP_START ?>">

                    <div class="form-group">
                        <label for="recipient-name" class="control-label">快递公司:</label>
                        <input type="text" class="form-control" id="recipient-name" name="courier_company" value="<?= $order[0]['courier_company'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="courier_number" class="control-label">快递单号:</label>
                        <input type="text" class="form-control" id="courier_number" name="courier_number" value="<?= $order[0]['courier_number'] ?>">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">确定</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {

        $('form#ship-form').validate({
                rules: {
                    // simple rule, converted to {required:true}
                    courier_company: "required",
                    courier_number: "required",
                },
                submitHandler: function (form) {
//                    console.log($(this).valid());
                    $.post($(form).attr('action'), $(form).serialize(), function (data) {
                        if (data.res == '0') {
                            showSuccess();
                            location.reload();
                        } else {
                            showErrorMessage(data.hint);
                        }
                    }, 'json');
                    return false;
                }
            }
        );
        $('form#order_form').validate({
                rules: {
                    // simple rule, converted to {required:true}
                    product_price:{
                        "number":true,
                        "required":true
                    },
                    product_presale_price:{
                        "number":true,
                        "required":true
                    },
                    product_count:{
                        "number":true,
                        "required":true
                    },
                    product_size:{
                        "number":true,
                        "required":true
                    },
                    product_color:{
                        "number":true,
                        "required":true
                    },

                },
                submitHandler: function (form) {
//                    console.log($(this).valid());
                    $.post($(form).attr('action'), $(form).serialize(), function (data) {
                        if (data.res == '0') {
                            showSuccess();
                            history.back();
                        } else {
                            showErrorMessage(data.hint);
                        }
                    }, 'json');
                    return false;
                }
            }
        )



    });
</script>

