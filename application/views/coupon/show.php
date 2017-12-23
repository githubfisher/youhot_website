<?php
$this->layout->load_css('/h5/css6/discount.css');
$this->layout->load_js('/h5/js6/jquery-2.1.1.min.js');
$this->layout->load_js('/h5/js9/discount.js');
$this->layout->load_js('/h5/js6/jaliswall.js');
$this->layout->load_js('/h5/js6/template.js');
$this->layout->load_js('/h5/js9/jweixin-1.2.0.js');
$this->layout->placeholder('title', '优惠券');
?>
<!--<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>-->
<input type="hidden" id="coupon_id" value="<?php echo $coupon_id;?>">
<input type="hidden" id="appId" value="<?= $signature['appId'] ?>">
<input type="hidden" id="nonceStr" value="<?= $signature['nonceStr'] ?>">
<input type="hidden" id="timestamp" value="<?= $signature['timestamp'] ?>">
<input type="hidden" id="signature" value="<?= $signature['signature'] ?>">
<div class="box">
			<ul class="top">
				<script type="text/html" id="share">
				{{each data.coupons as v}}
				<li>
					<div class="left">
						¥<b>{{v.value}}</b>
					</div>
					<div class="right">
						<p><b>满 ¥ {{v.limit}}减 ¥ {{v.value}}</b></p>
						<p>{{v.category}}、{{v.store}}</p>
						<p>有效期：{{v.use_at}}至{{v.use_end}}</p>
					</div>
				</li>
				{{/each}}
				</script>
				
			</ul>
			<div class="main">
				<h3>领取优惠券</h3>
				<input type="text" id="input" value="" placeholder="请输入您的手机号" maxlength="11" onkeyup="value=value.replace(/[^\d]/g,'') " onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))">
				<button id="btn">领取</button>
			</div>
			<div class="bottom">
				<h3>活动规则</h3>
				<p id="des"></p>
			</div>
		</div>
		<!--提示跳转Safari S-->
		<div class="safari">
		</div>
		<!--提示跳转Safari E-->
