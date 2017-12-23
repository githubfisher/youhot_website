<div class="column">
	<div class="column-title">
		<div id="tabs">
			<ul>
				<li><a href="/user/accounts">我的学费</a></li>
				<li class="selected"><a href="/user/doudou">我的豆豆</a></li>
				<?php if(JIFEN_OPEN):?><li><a href="/jifen">我的积分</a></li><?php endif;?>
			</ul>
		</div>
	</div>
	<div class="column-content">
		<p class="highlight_tip clearfix" style="line-height:30px;">
			您当前有豆豆<span class="green big strong"><?=$doudou?></span>粒 <br />
			<?php if($usertype == USERTYPE_STUDENT or $usertype == USERTYPE_PARENT):?>
			<!--
				<input type="button" class="btn-appointment doudou_charge fr" value="兑换/购买豆豆"/>
-->
				<!--
				<input type="button" class="btn-appointment vip_charge fr mr1e" value="VIP答疑卡"/>
				-->
			<span style="margin-left:1em" class="big strong">
				<a href="http://www.aifudao.com/user/accounts?from=web#doudou_charge" class="red" title="用学费兑换答疑豆豆，量多优惠">兑换/购买豆豆</a>
			</span>
			<span style="margin-left:1em" class="small"><a href="http://www.aifudao.com/accounts/card_charge?from=web">套餐充值卡充值</a></span>
			<?php endif;?>
		</p>
		<table border=​0 cellpadding=​4 cellspacing=0 class=zebra>
			<thead>
				<th>时间</th><th>说明</th><th>豆豆</th>
			</thead>
			<tbody>
			</tbody>
			<tfoot></tfoot>
		</table>
	</div>
</div>
<?php if($this->isAdmin):?>
	<script>
		$.ajaxSetup({data:{username:'<?=urlencode($this->input->get_post("username",true))?>'}});
	</script>
<?php endif;?>
<script>
	var offset = 0 ;
	function get_record(){
		$.getJSON('/user/doudou_records',{pn:offset},function(data){
			offset +=data.count;
			var html = [];
			$.each(data.list, function(i, item){
				html.push(
					'<tr><td>'+item.create_time+"</td><td>"+$('<p>').html(item.info).text().replace(/\n/g,'<br>')+'</td><td>'+item.doudou+'</td></tr>'
					)
			})
			if(offset < data.total){
				$('.zebra tfoot').html('<tr rowspan=3><td><a href="#" onclick="get_record();return false;">加载更多</a></td><tr>');
			}else{
				$('.zebra tfoot').empty();
			}
			$('.zebra tbody').append(html.join(''));
		})
	}
	get_record();

	$('.doudou_charge').click(function(){
		location.href='/user/accounts?from=web#doudou_charge'
	})
	$('.vip_charge').click(function(){
		location.href="/user/accounts?from=web#vip_charge"
	})
</script>


