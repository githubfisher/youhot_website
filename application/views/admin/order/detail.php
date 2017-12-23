<?php
$this->layout->placeholder('title', '订单详情');
$this->layout->load_css('admin/css/indent.css');
$this->layout->load_js('admin/plugins/jquery-validation/js/jquery.validate.js');
?>
<style>
    .message{
        border-left: 2px solid #3c3c3c;
        padding-left: 7px;
        line-height: 10px;
        margin-top: 17px;
        margin-bottom: 10px;
        font-weight:bold;
        font-size:14px;
    }
    .line{
        height: 1px;
        border-top: 1px solid #e8e8e8;
        margin: 14px -23px 18px;
    }
    .dingdan-middle .row{font-size:12px;margin-top:15px}
    .overseas input{
        margin: 3px;
	width: 100px;
        padding: 2px 3px;
    }
    .overseas textarea{
	margin: 3px;
	padding: 2px 3px;
    }
    .edit2{
        display: none;
    }
</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>订单详情</h3></div>
<!--        <div class="col-sm-8">
            <?php if (count($order) > 1) { ?>
                <?php foreach ($order as $or) { ?>
                    <?php if (($or['pid'] == 0) && empty($or['product_title'])) { ?>
                        <h3 class="text-right"><a href="/admin/order/edit/<?=$or['order_id']?>" class="product-add btn btn-purple pull-right " data-author="<?= $responsible_userid ?>"><i span="icon icon-edit"></i>编辑订单</a></h3>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <h3 class="text-right"><a href="/admin/order/edit/<?=$order[0]['order_id']?>" class="product-add btn btn-purple pull-right " data-author="<?= $responsible_userid ?>"><i span="icon icon-edit"></i>编辑订单</a></h3>
            <?php } ?> 
        </div> -->
    </div>

    <div class="m-content">
        <!--顶部结束-->
        <!-- 中部内容开始 -->
        <div class="ding-center" style="width:100%;height:auto;border:1px solid #e8e8e8">
            <!-- <div class="dingdan"><span>订单信息</span></div> -->
            <div class="dingdan-middle">
                <div class="message">买家信息</div>
		    <?php if (count($order) > 1) { ?>
			<?php foreach ($order as $oor) { ?>
			    <?php if (($oor['pid'] == 0) && empty($oor['product_title'])) { ?>
			    	<div class="row">
                              	  <!-- 
				  <div class="col-sm-1 text-right">用户名：</div>
				    <?php if (!empty($oor['buyer_username'])) { ?>
                                        <?php $buyer_username = substr($oor['buyer_username'], 0, 3).'****'.substr($oor['buyer_username'],-4) ?>
                                        <div class="col-sm-2"><?= $buyer_username ?></div>
				    <?php } else { ?>
                                	<div class="col-sm-2"></div>
                                    <?php } ?>
				    -->
                              	    <div class="col-sm-1 text-right">昵称：</div>
                              	    <div class="col-sm-2"><?= $oor['buyer_nickname'] ?></div>
				    <!--
				    <div class="col-sm-1 text-right">地区：</div>
                                    <div class="col-sm-2"><?= $oor['buyer_city'] ?></div>
				    -->
                          	</div>
			        <!--
                          	<div class="row">
				    <div class="col-sm-1 text-right">身份证：</div>
                                     <?php if (!empty($oor['idcard'])) { ?>
                                         <?php $idcard = substr($oor['idcard'], 0, 4).'********'.substr($oor['idcard'],-4) ?>
                                         <div class="col-sm-2"><?= $idcard ?></div>
				     <?php } else { ?>
                                	<div class="col-sm-2"></div>
                                     <?php } ?>
				    <div class="col-sm-1 text-right">电话：</div>
                                    <?php if (!empty($oor['buyer_username'])) { ?>
                                        <?php $buyer_username = substr($oor['buyer_username'], 0, 3).'****'.substr($oor['buyer_username'],-4) ?>
                                        <div class="col-sm-2"><?= $buyer_username ?></div>
				    <?php } else { ?>
                                	<div class="col-sm-2"></div>
                                    <?php } ?>
				</div>
				-->
			    	<?php break; ?>
                            <?php } ?>
			<?php } ?>    	
		    <?php } else {?>
			<div class="row">
			    <!-- 
                    	    <div class="col-sm-1 text-right">用户名：</div>
			    <?php if (!empty($order[0]['buyer_username'])) { ?>
                               <?php $buyer_username = substr($order[0]['buyer_username'], 0, 3).'****'.substr($order[0]['buyer_username'],-4) ?>
                                    <div class="col-sm-2"><?= $buyer_username ?></div>
			    <?php } else { ?>
                                <div class="col-sm-2"></div>
                            <?php } ?>
			    -->
                    	    <div class="col-sm-1 text-right">昵称：</div>
                    	    <div class="col-sm-2"><?= $order[0]['buyer_nickname'] ?></div>
			    <!--
			    <div class="col-sm-1 text-right">地区：</div>
                            <div class="col-sm-2"><?= $order[0]['buyer_city'] ?></div>
			    -->
                    	</div>
		        <!--
                	<div class="row">
			    <div class="col-sm-1 text-right">身份证：</div>
                            <?php if (!empty($order[0]['idcard'])) { ?>
                                <?php $idcard = substr($order[0]['idcard'], 0, 4).'********'.substr($order[0]['idcard'],-4) ?>
                                <div class="col-sm-2"><?= $idcard ?></div>
			    <?php } else { ?>
				<div class="col-sm-2"></div>
                            <?php } ?>
                    	    <div class="col-sm-1 text-right">电话：</div>
			    <?php if (!empty($order[0]['buyer_username'])) { ?>
                               <?php $buyer_username = substr($order[0]['buyer_username'], 0, 3).'****'.substr($order[0]['buyer_username'],-4) ?>
                                    <div class="col-sm-2"><?= $buyer_username ?></div>
			    <?php } else { ?>
                                <div class="col-sm-2"></div>
                            <?php } ?>
                	</div>
			-->
		    <?php } ?>
		<div class="line"></div>
                <!-- 订单信息 -->
                <div class="message">订单信息</div>
                <table class="table-box" cellspacing=0;cellpadding=0;>
                    <tr style="background:#F4F5F9;height:40px;border:1px solid #ccc">
                        <td>订单信息</td>
			<td>商品ID</td>
                        <td>属性</td>
                        <td>商品单价</td>
                        <td>数量</td>
                        <td>优惠券</td>
                        <td>商城优惠</td>
                        <td>运费</td>
                        <td>付款金额</td>
                        <td>支付方式</td>
                        <td>状态</td>
			<td>通知</td>
                    </tr>
                    <?php foreach($order as $od) { ?>
                        <tr id="tdbox">
                            <td>	
                                <div style="width:100%;height:100%;margin:0 auto;text-align:center;">
                                    <?php if (($od['pid'] == 0) && empty($od['product_title'])) { ?>
                                        <span style="font-size:20px;">主订单</span><br/>
					订单编号：<span><?= $od['order_id'] ?></span><br>
                                        订单时间：<span><?= $od['create_time'] ?></span><br>
					<!--付款时间：<span><?= $od['last_paid_time'] ?></span><br>-->
					交易号：<span><?= $od['last_paid_payinfo'] ?></span><br>
                                    <?php } else { ?>
                                        <img src="<?= $od['product_cover_image'] ?>" alt="" style="width:60px;height:70px;float:left;margin-left:20px;display:block;margin-top:10px;">
                                        <span style="line-height:20px;height:76px;width:200px;padding-left:10px;display:block;padding-top:20px"><?= $od['product_title'] ?></span>
                                        <span style="float: left;"><a href="<?= $od['m_url'] ?>" target="_blank">下单链接</a></span>
                                    <?php } ?>
                                </div>
                            </td>
			    <td><?= $od['product_id'] ?></td>
                            <td><span><?= $od['product_color'] ?></span><br/><span><?= $od['product_size'] ?></span></td>
                            <td><?= $od['product_price'] ?></td>
                            <td><?= $od['product_count'] ?></td>
                            <td><?= $od['last_pay_coupon_value'] ?></td>
                            <td><?= $od['promotion_value'] ?></td>
                            <td><?= $od['freight'] ?></td>
			    <td><?= $od['last_paid_money'] ?></td>
			    <td>
				<?php
				if ($od['last_payment'] == 'zhifubao') { 
				    echo '支付宝';
				} else if ($od['last_payment'] == 'weixin') {
				    echo '微信';
				}
			   	?> 
			    </td>
                            <td style="color:#EC1379"><?= format_order_status($od['status']) ?></td>
			    <?php if ($od['product_title'] != '') { ?>
			        <td>
				    <?php if ($od['out_of_stock_note'] > 0) { ?>
					<p>已通知<?=$od['out_of_stock_note']?>次</p>
				    <?php } ?>
				    <button data-toggle="modal" data-target="#noteModal" onclick="setPtInfo('<?=$od["order_id"]?>','<?=$od["product_id"]?>','<?php echo addslashes($od["product_title"]) ?>','<?=$od["product_cover_image"]?>','<?=$od["product_price"]?>','<?php echo addslashes($od["product_color"]);?>','<?php echo addslashes($od["product_size"]);?>','<?=$od["product_count"]?>','<?=$od['buyer_userid']?>');">断货通知</button></td>
			    <?php } else { ?>
				<td></td>
			    <?php } ?> 
                        </tr>
                    <?php } ?>

                </table>

                <div class="line"></div>
                <div class="row">
                    <div class="col-md-2"><span class="message">收货信息</span></div>
                    <div class="col-md-10">
			<?php if (count($order) > 1) { ?>
                            <?php foreach ($order as $odr) { ?>
                                <?php if (($odr['pid'] == 0) && empty($odr['product_title'])) { ?>
                         	    <?php if ($odr['status'] == ORDER_STATUS_LAST_PAID): ?>
                            	    	<button data-toggle="modal" data-target="#shipModal">发货</button>
                        	    <?php endif; ?>
				<?php } ?>
                    	    <?php } ?>
                 	<?php } else { ?>
			    <?php if ($order[0]['status'] == ORDER_STATUS_LAST_PAID): ?>
                                 <button data-toggle="modal" data-target="#shipModal">发货</button>
                            <?php endif; ?>
			<?php } ?>
                    </div>
                </div>
		
		<?php if (count($order) > 1) { ?>
                    <?php foreach ($order as $odr) { ?>
                        <?php if (($odr['pid'] == 0) && empty($odr['product_title'])) { ?>
			    <?php if ($user['username'] ==  'admin@youhot.com.cn') { ?>
                              <div class="dingdan-bottom">收货地址:&nbsp&nbsp<span>地址编号<?= $odr['ship_info_id'] ?></span></div>
			    <?php } else { ?>
			      <?php if ((int)strtotime($odr['create_time']) >= 1510675200) { ?>
                                <div class="dingdan-bottom">收货地址:&nbsp&nbsp<span><?= $odr['address'] ?></span>&nbsp&nbsp收货人：&nbsp&nbsp<span><?= $odr['receiver'] ?></span>&nbsp&nbsp手机：&nbsp&nbsp<span><?= $odr['phone_num'] ?></span></div>
			      <?php } else { ?>
                                <div class="dingdan-bottom">收货地址:&nbsp&nbsp<span><?= mb_substr($odr['address'], 0,6,'utf-8') ?>***</span>&nbsp&nbsp收货人：&nbsp&nbsp<span><?= mb_substr($odr['receiver'],0,1,'utf-8') ?>***</span>&nbsp&nbsp手机：&nbsp&nbsp<span><?= substr($odr['phone_num'],0,3) ?>****<?= substr($odr['phone_num'],6,4) ?></span></div>
			      <?php } ?>
			    <?php } ?>
			    <div class="dingdan-bottom">卖家留言: &nbsp&nbsp<span><?= $odr['memo'] ?></span></div>
                            <div class="dingdan-bottom">运送方式:&nbsp&nbsp<span><?= $odr['courier_company'] ?></span></div>
                            <div class="dingdan-bottom">快递单号:&nbsp&nbsp <span><?= $odr['courier_number'] ?></span></div>
			   <div class="dingdan-bottom">客服备注:&nbsp&nbsp <span><?= $odr['remarks'] ?></span></div>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
		    <?php if ($user['username'] == 'admin@youhot.com.cn') { ?>
                      <div class="dingdan-bottom">收货地址:&nbsp&nbsp<span>地址编号<?= $order[0]['ship_info_id'] ?></span></div>
		    <?php } else { ?>
		      <?php if ((int)strtotime($order[0]['create_time']) >= 1510675200) { ?>
                        <div class="dingdan-bottom">收货地址:&nbsp&nbsp<span><?= $order[0]['address'] ?></span>&nbsp&nbsp收货人：&nbsp&nbsp<span><?= $order[0]['receiver'] ?></span>&nbsp&nbsp手机：&nbsp&nbsp<span><?= $order[0]['phone_num'] ?></span></div>
		      <?php } else { ?>
                        <div class="dingdan-bottom">收货地址:&nbsp&nbsp<span><?= mb_substr($order[0]['address'], 0, 6, 'utf-8') ?>***</span>&nbsp&nbsp收货人：&nbsp&nbsp<span><?= mb_substr($order[0]['receiver'],0,1,'utf-8') ?>**</span>&nbsp&nbsp手机：&nbsp&nbsp<span><?= substr($order[0]['phone_num'],0,3) ?>****<?= substr($order[0]['phone_num'], 6, 4); ?></span></div>
		      <?php } ?>
		    <?php } ?>
		    <div class="dingdan-bottom">卖家留言: &nbsp&nbsp<span><?= $order[0]['memo'] ?></span></div>
                    <div class="dingdan-bottom">运送方式:&nbsp&nbsp<span><?= $order[0]['courier_company'] ?></span></div>
                    <div class="dingdan-bottom">快递单号:&nbsp&nbsp <span><?= $order[0]['courier_number'] ?></span></div>
		    <div class="dingdan-bottom">客服备注:&nbsp&nbsp <span><?= $order[0]['remarks'] ?></span></div>
                <?php } ?>

		<div class="row">
		    <div class="col-md-2"><span class="message">编辑信息</span></div>
		    <div class="col-md-10">
			<button data-toggle="modal" data-target="#editModal">编辑</button>
		    </div>
 		
		</div>
		<div class="message">下单信息</div>
		    <?php if (is_array($shipping_info) && count($shipping_info)) { ?>
			<table class="table-box" cellspacing=0;cellpadding=0;>
                    	    <tr style="background:#F4F5F9;height:40px;border:1px solid #ccc">
                        	<td width="350px" ;>商城信息</td>
                        	<td>运输方式</td>
                        	<td>运费</td>
				<td>运费详情</td>
                        	<td>运输时效</td>
                       		<td>运输说明</td>
                        	<td>清关方式</td>
                        	<td>税费</td>
                     	    </tr>
			    <?php foreach($shipping_info as $si) { ?>
                        	<tr id="tdbox">
                            	    <td><?= $si['store_name']?></td>
				    <td><?= $si['shipping_name']?></td>
				    <td><?= $si['shipping_fee']?></td>
				    <td><?= $si['shipping_fee_info']?></td>
				    <td><?= $si['shipping_days']?></td>
				    <td><?= $si['store_shipping_name']?></td>
                                    <td><?= $si['tax_name']?></td>
				    <td><?= $si['tax_fee']?></td>
				</tr>
			    <?php } ?>
			</table>
		    <?php } ?>
			
                <input type="hidden" id="overseas" value="<?= $overseas?>">
                <input type="hidden" id="orderid" value="<?= $orderid?>">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="overseas">
                            <thead style="background:#F4F5F9;height:40px;border:1px solid #ccc">
                                <th with="15%">订单信息</th>
                                <th with="10%">商品清单</th>
                                <th with="15%">商城运输</th>
                                <th with="15%">转运运输</th>
                                <th with="15%">国内快递</th>
                                <th with="10%">清关信息</th>
                                <th with="10%">备注</th>
                                <th with="10%">操作</th>
                            </thead>
                            <tbody>
                                <?php if (is_array($deliver) && count($deliver)) { $num = 0; ?>
                                    <?php foreach ($deliver as $item) { ?>
                                        <tr id="<?=$num?>a">
                                            <td>
                                                <span><?=$item['order'][0];?></span><br>
                                                <span><?=$item['order'][1];?></span><br>
                                                <span><?=$item['order'][2];?>元</span><br>
                                            </td>
                                            <td>
                                                <span><?=$item['order'][3];?></span><br>
                                            </td>
                                            <td>
                                                <span><?=$item['store'][0];?></span><br>
                                                <span><?=$item['store'][1];?></span><br>
                                                <span><?=$item['store'][2];?>元</span><br>
                                                <span><?=$item['store'][3];?></span><br>
                                                <span><?=$item['store'][4];?></span><br>
                                            </td>
                                            <td>
                                                <span><?=$item['zhuanyun'][0];?></span><br>
                                                <span><?=$item['zhuanyun'][1];?></span><br>
                                                <span><?=$item['zhuanyun'][2];?>元</span><br>
                                                <span><?=$item['zhuanyun'][3];?></span><br>
                                                <span><?=$item['zhuanyun'][4];?></span><br>
                                            </td>
                                            <td>
                                                <span><?=$item['guonei'][0];?></span><br>
                                                <span><?=$item['guonei'][1];?></span><br>
                                                <span><?=$item['guonei'][2];?>元</span><br>
                                                <span><?=$item['guonei'][3];?></span><br>
                                                <span><?=$item['guonei'][4];?></span><br>
                                            </td>
                                            <td>
                                                <span><?=$item['qingguan'][0];?></span><br>
                                                <span><?=$item['qingguan'][1];?></span><br>
                                                <span><?=$item['qingguan'][2];?>元</span><br>
                                                <span><?=$item['qingguan'][3];?></span><br>
                                                <span><?=$item['qingguan'][4];?></span><br>
                                            </td>
                                            <td>
                                                <span><?=$item['order'][4];?></span><br>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-purple" style="width:6em" onclick="edit('<?=$num?>');">编辑</button>
                                            </td>
                                        </tr>
                                        <tr><td style="color:white;">空白行</td></tr>
                                        <tr class="edit2" id="<?=$num?>b">
                                            <td>
                                                <input type="text" id="<?=$num?>od_nu" value="<?=$item['order'][0];?>" placeholder="订单单号">
                                                <input type="text" id="<?=$num?>od_at" value="<?=$item['order'][1];?>" placeholder="下单时间">
                                                <input type="text" id="<?=$num?>od_fee" value="<?=$item['order'][2];?>" placeholder="订单金额">
                                            </td>
                                            <td>
                                                <textarea id="<?=$num?>od_p" placeholder="商品ID 逗号隔开" rows="6"><?=$item['order'][3];?></textarea>
                                            </td>
                                            <td>
                                                <input type="text" id="<?=$num?>st_cp" value="<?=$item['store'][0];?>" placeholder="运输公司">
                                                <input type="text" id="<?=$num?>st_nu" value="<?=$item['store'][1];?>" placeholder="商城运输单号">
                                                <input type="text" id="<?=$num?>st_fee" value="<?=$item['store'][2];?>" placeholder="商城运费">
                                                <input type="text" id="<?=$num?>st_at" value="<?=$item['store'][3];?>" placeholder="起运时间">
                                                <input type="text" id="<?=$num?>st_end" value="<?=$item['store'][4];?>" placeholder="到达时间">
                                            </td>
                                            <td>
                                                <input type="text" id="<?=$num?>zy_cp" value="<?=$item['zhuanyun'][0];?>" placeholder="转运公司">
                                                <input type="text" id="<?=$num?>zy_nu" value="<?=$item['zhuanyun'][1];?>" placeholder="转运单号">
                                                <input type="text" id="<?=$num?>zy_fee" value="<?=$item['zhuanyun'][2];?>" placeholder="转运运费">
                                                <input type="text" id="<?=$num?>zy_at" value="<?=$item['zhuanyun'][3];?>" placeholder="起运时间">
                                                <input type="text" id="<?=$num?>zy_end" value="<?=$item['zhuanyun'][4];?>" placeholder="到达时间">
                                            </td>
                                            <td>
                                                <input type="text" id="<?=$num?>gn_cp" value="<?=$item['guonei'][0];?>" placeholder="快递公司">
                                                <input type="text" id="<?=$num?>gn_nu" value="<?=$item['guonei'][1];?>" placeholder="快递单号">
                                                <input type="text" id="<?=$num?>gn_fee" value="<?=$item['guonei'][2];?>" placeholder="快递费用">
                                                <input type="text" id="<?=$num?>gn_at" value="<?=$item['guonei'][3];?>" placeholder="起运时间">
                                                <input type="text" id="<?=$num?>gn_end" value="<?=$item['guonei'][4];?>" placeholder="到达时间">
                                            </td>
                                            <td>
                                                <input type="text" id="<?=$num?>qg_ty" value="<?=$item['qingguan'][0];?>" placeholder="清关方式">
                                                <input type="text" id="<?=$num?>qg_sl" value="<?=$item['qingguan'][1];?>" placeholder="清关税率">
                                                <input type="text" id="<?=$num?>qg_fee" value="<?=$item['qingguan'][2];?>" placeholder="清关税费">
                                                <input type="text" id="<?=$num?>qg_at" value="<?=$item['qingguan'][3];?>" placeholder="入关时间">
                                                <input type="text" id="<?=$num?>qg_end" value="<?=$item['qingguan'][4];?>" placeholder="出关时间">
                                            </td>
                                            <td>
                                                <textarea id="<?=$num?>od_des" placeholder="备注信息" rows="6"><?=$item['order'][4];?></textarea>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-purple" style="width:6em" onclick="save('<?=$num?>','<?=$overseas?>');">保存</button>
                                                <button type="button" class="btn btn-purple" style="width:6em" onclick="cancel('<?=$num?>');">取消</button>
                                            </td>
                                        </tr>
                                        <tr><td style="color:white;">空白行</td></tr>
                                    <?php $num++; } ?>
                                <?php } ?>
                                <hr/>
                                <tr><td style="color:white;">空白行</td></tr>
                                <tr>
                                <td>
                                    <input type="text" id="od_nu" value="" placeholder="订单单号">
                                    <input type="text" id="od_at" value="" placeholder="下单时间">
                                    <input type="text" id="od_fee" value="" placeholder="订单金额">
                                </td>
                                <td>
                                    <textarea id="od_p" placeholder="商品ID 逗号隔开" rows="6"></textarea>
                                </td>
                                <td>
                                    <input type="text" id="st_cp" value="" placeholder="运输公司">
                                    <input type="text" id="st_nu" value="" placeholder="商城运输单号">
                                    <input type="text" id="st_fee" value="" placeholder="商城运费">
                                    <input type="text" id="st_at" value="" placeholder="起运时间">
                                    <input type="text" id="st_end" value="" placeholder="到达时间">
                                </td>
                                <td>
                                    <input type="text" id="zy_cp" value="" placeholder="转运公司">
                                    <input type="text" id="zy_nu" value="" placeholder="转运单号">
                                    <input type="text" id="zy_fee" value="" placeholder="转运运费">
                                    <input type="text" id="zy_at" value="" placeholder="起运时间">
                                    <input type="text" id="zy_end" value="" placeholder="到达时间">
                                </td>
                                <td>
                                    <input type="text" id="gn_cp" value="" placeholder="快递公司">
                                    <input type="text" id="gn_nu" value="" placeholder="快递单号">
                                    <input type="text" id="gn_fee" value="" placeholder="快递费用">
                                    <input type="text" id="gn_at" value="" placeholder="起运时间">
                                    <input type="text" id="gn_end" value="" placeholder="到达时间">
                                </td>
                                <td>
                                    <input type="text" id="qg_ty" value="" placeholder="清关方式">
                                    <input type="text" id="qg_sl" value="" placeholder="清关税率">
                                    <input type="text" id="qg_fee" value="" placeholder="清关税费">
                                    <input type="text" id="qg_at" value="" placeholder="入关时间">
                                    <input type="text" id="qg_end" value="" placeholder="出关时间">
                                </td>
                                <td>
                                    <textarea id="od_des" placeholder="备注信息" rows="6"></textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-purple" style="width:6em" onclick="sub('<?=$overseas?>');">提交</button>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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
                        <input type="text" class="form-control" id="recipient-name" name="courier_company" value="<?=$order[0]['courier_company']?>">
                    </div>
                    <div class="form-group">
                        <label for="courier_number" class="control-label">快递单号:</label>
                        <input type="text" class="form-control" id="courier_number" name="courier_number" value="<?=$order[0]['courier_number']?>">
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">编辑信息</h4>
            </div>
            <form action="/order/update" id="edit-form" method="post">
                <div class="modal-body">
                    <?php if (count($order) > 1) { ?>
                        <?php foreach ($order as $odr) { ?>
                            <?php if (($odr['pid'] == 0) && empty($odr['product_title'])) { ?>
                            <input type="hidden" name="order_id" value="<?= $odr['order_id'] ?>">
			    <input type="hidden" id="order_status" value="<?= $odr['status'] ?>">
			    <div class="form-group">
                                <label for="last_paid_money" class="control-label" style="line-height: 34px;">订单总价:</label>
                                <input type="text" class="form-control" id="last_paid_money" name="last_paid_money" style="float:right;width:88%" value="<?=$odr['last_paid_money']?>">
                            </div>
                            <div class="form-group">
                                <label for="freight" class="control-label"  style="line-height: 34px;">订单运费:</label>
                                <input type="text" class="form-control" id="freight" name="freight" style="float:right;width:88%" value="<?=$odr['freight']?>">
                            </div>
                            <div class="form-group">
                                <label for="tax" class="control-label" style="line-height: 34px;">订单税费:</label>
                                <input type="text" class="form-control" id="tax" name="tax" style="float:right;width:88%" value="<?=$odr['tax']?>">
                            </div>
                            <div class="form-group">
                                <label for="last_pay_coupon_value" class="control-label"  style="line-height: 34px;">订单优惠:</label>
                                <input type="text" class="form-control" id="last_pay_coupon_value" name="last_pay_coupon_value" style="float:right;width:88%" value="<?=$odr['last_pay_coupon_value']?>">
                            </div>
                            <div class="form-group">
                                <label for="status" class="control-label" style="line-height: 34px;">订单状态:</label>
                                <select class="form-control" id="status" name="status" style="float:right;width:88%">
                                    <option value="20">待付款</option>
                                    <option value="24">待发货</option>
                                    <option value="30">已发货</option>
                                    <option value="31">已完成</option>
                                    <option value="41">已关闭</option>
                                    <option value="25">已取消（未支付）</option>
                                    <option value="26">已取消（已支付）</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="courier_company" class="control-label"  style="line-height: 34px;">快递公司:</label>
                                <input type="text" class="form-control" id="courier_company" name="courier_company" style="float:right;width:88%" value="<?=$odr['courier_company']?>">
                            </div>
                            <div class="form-group">
                                <label for="courier_number" class="control-label" style="line-height: 34px;">快递单号:</label>
                                <input type="text" class="form-control" id="courier_number" name="courier_number" style="float:right;width:88%" value="<?=$odr['courier_number']?>">
                            </div>
			    <div class="form-group">
                                <label for="remarks" class="control-label"  style="line-height: 34px;">客服备注:</label>
                                <textarea class="form-control" id="remarks" name="remarks" style="float:right;width:88%"><?=$odr['remarks']?></textarea>
                            </div>
		   </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                            <input type="hidden" name="order_id" value="<?= $order[0]['order_id'] ?>">
			    <input type="hidden" id="order_status" value="<?= $order[0]['status'] ?>">
			    <div class="form-group">
                        	<label for="last_paid_money" class="control-label" style="line-height: 34px;">订单总价:</label>
                        	<input type="text" class="form-control" id="last_paid_money" name="last_paid_money" style="float:right;width:88%" value="<?=$order[0]['last_paid_money']?>">
                    	    </div>
                    	    <div class="form-group">
                        	<label for="freight" class="control-label"  style="line-height: 34px;">订单运费:</label>
                        	<input type="text" class="form-control" id="freight" name="freight" style="float:right;width:88%" value="<?=$order[0]['freight']?>">
                    	    </div>
                    	    <div class="form-group">
                        	<label for="tax" class="control-label" style="line-height: 34px;">订单税费:</label>
                        	<input type="text" class="form-control" id="tax" name="tax" style="float:right;width:88%" value="<?=$order[0]['tax']?>">
                    	    </div>
                    	    <div class="form-group">
                        	<label for="last_pay_coupon_value" class="control-label"  style="line-height: 34px;">订单优惠:</label>
                        	<input type="text" class="form-control" id="last_pay_coupon_value" name="last_pay_coupon_value" style="float:right;width:88%" value="<?=$order[0]['last_pay_coupon_value']?>">
                    	    </div>
                    	    <div class="form-group">
                        	<label for="status" class="control-label" style="line-height: 34px;">订单状态:</label>
                        	<select class="form-control" id="status" name="status" style="float:right;width:88%">
                            	    <option value="20">待付款</option>
                            	    <option value="24">待发货</option>
                            	    <option value="30">已发货</option>
                            	    <option value="31">已完成</option>
                            	    <option value="41">已关闭</option>
                            	    <option value="25">已取消（未支付）</option>
                            	    <option value="26">已取消（已支付）</option>
                        	</select>
                   	    </div>
			    <div class="form-group">
                        	<label for="courier_company" class="control-label"  style="line-height: 34px;">快递公司:</label>
                        	<input type="text" class="form-control" id="courier_company" name="courier_company" style="float:right;width:88%" value="<?=$order[0]['courier_company']?>">
                    	    </div>
                    	    <div class="form-group">
                        	<label for="courier_number" class="control-label" style="line-height: 34px;">快递单号:</label>
                        	<input type="text" class="form-control" id="courier_number" name="courier_number" style="float:right;width:88%" value="<?=$order[0]['courier_number']?>">
                    	    </div>
                    	    <div class="form-group">
                        	<label for="remarks" class="control-label"  style="line-height: 34px;">客服备注:</label>
                        	<textarea class="form-control" id="remarks" name="remarks" style="float:right;width:88%"><?=$order[0]['remarks']?></textarea>
                    	    </div>
		</div>
                    <?php } ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-primary">确定</button>
                </div>
	    </form>
        </div>
    </div>
</div>
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">断货通知</h4>
            </div>
            <form action="/order/out_of_stock_note" id="note-form" method="post">
		<input type="hidden" id="order_id" name="order_id" value="">	
		<input type="hidden" id="pt_id" name="pt_id" value="">
		<input type="hidden" id="pt_title" name="pt_title" value="">
		<input type="hidden" id="pt_cover_image" name="pt_cover_image" value="">
		<input type="hidden" id="pt_price" name="pt_price" value="">
		<input type="hidden" id="pt_color" name="pt_color" value="">
		<input type="hidden" id="pt_size" name="pt_size" value="">
		<input type="hidden" id="pt_count" name="pt_count" value="">
		<input type="hidden" id="pt_buyer" name="pt_buyer" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note-title" class="control-label">通知标题:</label>
                        <input type="text" class="form-control" id="note-title" name="note-title" value="非常抱歉，您订购的商品断货啦！">
                    </div>
                    <div class="form-group">
                        <label for="note-content" class="control-label">通知内容:</label>
                        <textarea class="form-control" id="note-content" name="note-content">很抱歉，您订购的商品（商品名称为：<i id="pt2_title"></i>），经客服人员确认已经断货，请您知悉！</textarea>
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
	$("#status").val($("#order_status").val());
        $('form#ship-form').validate({
                rules: {
                    // simple rule, converted to {required:true}
                    courier_company: "required",
                    courier_number: "required",
                },
                submitHandler:function(form){
                    $.post($(form).attr('action'), $(form).serialize(), function (data) {
                        //if (data.res == '0') {
                            showSuccess();
                            location.reload();
                        /*} else {
                            showErrorMessage(data.hint);
                        }*/
                    }, 'json');
                    return false;
                }
            }
        );
	$('form#edit-form').validate({
                rules: {
                },
                submitHandler:function(form){
                    $.post($(form).attr('action'), $(form).serialize(), function (data) {
                        //if (data.res == '0') {
                            showSuccess();
                            location.reload();
                        /*} else {
                            showErrorMessage(data.hint);
                        }*/
                    }, 'json');
                    return false;
                }
            }
        );
    });
    function sub(str)
    {
        var od = new Array();
        od[0] = $("#od_nu").val();
        od[1] = $("#od_at").val();
        od[2] = $("#od_fee").val();
        od[3] = $("#od_p").val();
        od[4] = $("#od_des").val();

        var st = new Array();
        st[0] = $("#st_cp").val();
        st[1] = $("#st_nu").val();
        st[2] = $("#st_fee").val();
        st[3] = $("#st_at").val();
        st[4] = $("#st_end").val();

        var zy = new Array();
        zy[0] = $("#zy_cp").val();
        zy[1] = $("#zy_nu").val();
        zy[2] = $("#zy_fee").val();
        zy[3] = $("#zy_at").val();
        zy[4] = $("#zy_end").val();

        var gn = new Array();
        gn[0] = $("#gn_cp").val();
        gn[1] = $("#gn_nu").val();
        gn[2] = $("#gn_fee").val();
        gn[3] = $("#gn_at").val();
        gn[4] = $("#gn_end").val();

        var qg = new Array();
        qg[0] = $("#qg_ty").val();
        qg[1] = $("#qg_sl").val();
        qg[2] = $("#qg_fee").val();
        qg[3] = $("#qg_at").val();
        qg[4] = $("#qg_end").val();

        var info = new Array();
        info[0] = JSON.stringify(od);
        info[1] = JSON.stringify(st);
        info[2] = JSON.stringify(zy);
        info[3] = JSON.stringify(gn);
        info[4] = JSON.stringify(qg);

        var info_str = '{"order":'+info[0]+',"store":'+info[1]+',"zhuanyun":'+info[2]+',"guonei":'+info[3]+',"qingguan":'+info[4]+'}';
        if (str.length > 0) {
            str += ',' + info_str;
        } else {
            str = info_str;
        }
        
        var order_id = $("#orderid").val();
        var url = '/admin/order/update_deliver_detail';
        $.post(url,{order_id,str},function (data) {
            if (data.res == 0) {
                showSuccess();
                window.location.reload();
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
    function save(id,overseas)
    {
        var od = new Array();
        od[0] = $("#"+id+"od_nu").val();
        od[1] = $("#"+id+"od_at").val();
        od[2] = $("#"+id+"od_fee").val();
        od[3] = $("#"+id+"od_p").val();
        od[4] = $("#"+id+"od_des").val();

        var st = new Array();
        st[0] = $("#"+id+"st_cp").val();
        st[1] = $("#"+id+"st_nu").val();
        st[2] = $("#"+id+"st_fee").val();
        st[3] = $("#"+id+"st_at").val();
        st[4] = $("#"+id+"st_end").val();

        var zy = new Array();
        zy[0] = $("#"+id+"zy_cp").val();
        zy[1] = $("#"+id+"zy_nu").val();
        zy[2] = $("#"+id+"zy_fee").val();
        zy[3] = $("#"+id+"zy_at").val();
        zy[4] = $("#"+id+"zy_end").val();

        var gn = new Array();
        gn[0] = $("#"+id+"gn_cp").val();
        gn[1] = $("#"+id+"gn_nu").val();
        gn[2] = $("#"+id+"gn_fee").val();
        gn[3] = $("#"+id+"gn_at").val();
        gn[4] = $("#"+id+"gn_end").val();

        var qg = new Array();
        qg[0] = $("#"+id+"qg_ty").val();
        qg[1] = $("#"+id+"qg_sl").val();
        qg[2] = $("#"+id+"qg_fee").val();
        qg[3] = $("#"+id+"qg_at").val();
        qg[4] = $("#"+id+"qg_end").val();

        var info = new Array();
        info[0] = JSON.stringify(od);
        info[1] = JSON.stringify(st);
        info[2] = JSON.stringify(zy);
        info[3] = JSON.stringify(gn);
        info[4] = JSON.stringify(qg);

        var str = '{"order":'+info[0]+',"store":'+info[1]+',"zhuanyun":'+info[2]+',"guonei":'+info[3]+',"qingguan":'+info[4]+'}';

        var order_id = $("#orderid").val();
        var url = '/admin/order/update_deliver_detail';
        $.post(url,{id,str,order_id,overseas},function (data) {
            if (data.res == 0) {
                showSuccess();
                /*window.location.reload();*/
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    function setPtInfo(order_id,id,title,cover_image,price,color,size,count,buyer)
    {
	$("form#note-form #order_id").val(order_id);
	$("#pt_id").val(id);
	$("#pt_title").val(title);
	$("#note-content").html('  很抱歉，您订购的商品（商品名：' + title + '），经客服人员确认已经断货，请您知悉！');
	$("#pt_cover_image").val(cover_image);
	$("#pt_price").val(price);
	$("#pt_color").val(color);
	$("#pt_size").val(size);
	$("#pt_count").val(count);
	$("#pt_buyer").val(buyer);
    }
    function bak()
    {
	window.history.back();
    }
</script>

