<?php
$this->layout->load_css('h5/css9/brand.css');
$this->layout->load_js('h5/js6/jquery-2.1.1.min.js');              
$this->layout->load_js('h5/js6/template.js');
$this->layout->load_js('h5/js6/jaliswall.js');
$this->layout->load_js('h5/js9/brand.js');	
$this->layout->load_js('/h5/js9/jweixin-1.2.0.js');	
?>
<!--<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>-->
<input type="hidden" id="brandId" value="<?= $brandId ?>">
<input type="hidden" id="appId" value="<?= $signature['appId'] ?>">
<input type="hidden" id="nonceStr" value="<?= $signature['nonceStr'] ?>">
<input type="hidden" id="timestamp" value="<?= $signature['timestamp'] ?>">
<input type="hidden" id="signature" value="<?= $signature['signature'] ?>">
<div class="qgzmain outbox">
			<div class="topvis">
				<div class="topvis_l fl">
					<a href="javascript:history.go(-1);" ><img src="/static/h5/images/goback1.png"></a>
				</div>
			</div>
			<div class="share">
				<script id="share" type="text/html">
					<div class="qgzshare2 qgzshare2new">
						<h3 class="title">{{data.brand}}</h3>
						<!-- <img src="/static/h5/images/likeimg.jpg"> -->
						<a href="javascript::" class="J-jump">关注</a>
					</div>

					<!-- <div class="qgzsignn padding3 qgzsignneww">
						<p>意大利品牌Prada于1913年在米兰创建。Miuccia Prada的独特天 赋在于对新创意的不懈追求，融合了对知识的好奇心和文化兴趣， 从而开辟了先驱之路。她不仅能够预测时尚趋势...
							<a href="javascript:;" class="J-jump">查看全部</a>
						</p>
					</div> -->

					<div class="qgztype qgztypenew">
						<ul>
							<li>
								<a href="javascript:;" class="arrow J-jump">分类</a>
							</li>
							<li>
								<a href="javascript:;" class="arrow J-jump">价格</a>
							</li>
							<li>
								<a href="javascript:;" class="arrow J-jump">折扣</a>
							</li>
							<li>
								<a href="javascript:;" class="qgzmenubtn J-jump">价格(升序)</a>
							</li>

						</ul>
					</div>

					<div class="qgzlike wrapper">
						<ul class="wall">
							{{each data.list as v j}}
							<li class="item">
								<a href="javascript:;" class="J-jump">
									<div class="likehat">
										<img src="{{v.cover_image}}">
										{{each v.superscript as v i }}
											{{if i==0}}
											<img src="{{v.url}}" alt="" class="qblock">
											{{/if}}
										{{/each}}
										<em class="sbg1 {{if v.like == 1}}sbg1s{{/if}}"></em>
										{{if v.status != '1'}}
										<div class="sqing">
											<span>已售罄</span>
										</div>
										{{/if}}
									</div>
									<h6></h6>
									<p>{{v.title}}</p>
									<span>¥{{v.price}}
										{{if v.presale_price != '0'}}
										<i>¥{{v.presale_price}}</i>
										{{/if}}
									</span>
								</a>
									
							</li>
							{{/each}}
						</ul>						
						
					</div>					
				</script>
			</div>		

		</div>
		<div class="download-pannel">
			<div class="left">
				<img src="/static/h5/images/yh.png" class="logo">
				<img src="/static/h5/images/yhz.png" class="appname">
			</div>
			<div class="right">
				<a href="javascrip:;" class="open J-jump">打开app</a>
			</div>
		</div>
		<!--提示跳转Safari S-->
		<div class="safari">
		</div>
		<!--提示跳转Safari E-->
