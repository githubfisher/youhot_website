<div class="column">
	<div class="column-title">
		<div id="tabs">
			<ul>
				<li><a href="/user/accounts">我的学费</a></li>
				<li><a href="/user/doudou">我的豆豆</a></li>
				<li class="selected"><a href="/jifen">我的积分</a></li>
			</ul>
		</div>
	</div>
	<div class="column-content">
		<p class="highlight_tip clearfix" style="line-height:30px;">
			您当前有积分<span class="green big strong"><?=$jifen?></span>分
			<span >
				<a href="/blog/archives/1302" class="green" title="如何获得更多积分？" target="_blank"> (如何获得更多积分？)</a>
			</span>
			<span  class="ml1e strong">
				爱辅导有 <a href="/jifen/store" class="red big" title="积分商城_好玩又有趣的礼品">积分商城</a> 了
			</span>
		</p>
		<table border=​0 cellpadding=​4 cellspacing=0 class=zebra>
			<thead>
				<th>时间</th><th>说明</th><th>积分</th>
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
		$.getJSON('/jifen/jifen_list',{pn:offset},function(data){
			offset +=data.count;
			var html = [];
			$.each(data.list, function(i, item){
				html.push(
					'<tr><td>'+item.create_time+"</td><td>"+$('<p>').html(item.info).text().replace(/\n/g,'<br>')+'</td><td>'+item.jifen+'</td></tr>'
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

	$('.jifen_charge').click(function(){
		location.href='/user/accounts#jifen_charge'
	})
	$('.vip_charge').click(function(){
		location.href="/user/accounts#vip_charge"
	})
</script>


