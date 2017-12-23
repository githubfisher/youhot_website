<?php
$this->layout->load_css("/h5/css9/friends.css");
$this->layout->load_js('/h5/js9/jquery-2.1.1.min.js');
$this->layout->load_js('/h5/js9/template.js');
$this->layout->load_js('/h5/js9/friends.js');
$this->layout->load_js('/h5/js9/jaliswall.js');
$this->layout->load_js('/h5/js9/common.js');
$this->layout->load_js('/h5/js9/jweixin-1.2.0.js');
$this->layout->placeholder('title', '好友券');
?>
<input type="hidden" id="appId" value="<?= $signature['appId'] ?>">
<input type="hidden" id="nonceStr" value="<?= $signature['nonceStr'] ?>">
<input type="hidden" id="timestamp" value="<?= $signature['timestamp'] ?>">
<input type="hidden" id="signature" value="<?= $signature['signature'] ?>">
<div class="box friends">
			<script type="text/html" id="coupon">
			<div class="count">
				<div class="left">
					¥<b>{{data.value}}</b>
				</div>
				<div class="right">
					<p>好友券</p>
					<p>有效期至{{data.deadline}}</p>
				</div>
			</div>
			<h5>规则说明</h5>
			<ol>
				<li>分享youhot洋火优惠活动给好友，好友下载App并且填写邀请人电话号码完成注册后，即可获得[{{data.value}}元好友券]；</li>
				<li>每分享2名好友于youhot洋火App完成注册后，分享者即可获得1张[{{data.value}}元好友券]；</li>
				<li>邀请好友数量无上限，多邀多得；</li>
				<li>好友券使用时间截止到{{data.deadline}}；</li>
				<li>App下载目前仅限苹果用户；</li>
				<li>本次好友券使用最终解释权归youhot洋火所有。</li>
			</ol>
			<div class="button J-jump">下载APP</div>
			</script>
		</div>
		<!--提示跳转Safari S-->
		<div class="safari">
		</div>
		<!--提示跳转Safari E-->
