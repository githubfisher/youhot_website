<?php
$this->layout->load_css("/h5/css6/swiper.css");
$this->layout->load_css("/h5/css9/share1.css");
$this->layout->load_js('/h5/js6/jquery-2.1.1.min.js');
$this->layout->load_js('/h5/js6/template.js');
$this->layout->load_js('/h5/js6/swiper.min.js');
$this->layout->load_js('/h5/js6/jaliswall.js');
$this->layout->load_js('/h5/js9/share1.js');
$this->layout->load_js('/h5/js9/jweixin-1.2.0.js');
?>
<!--<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>-->
<input type="hidden" id="collection_id" value="<?= $collection_id ?>">
<input type="hidden" id="type" value="<?= $type ?>">
<input type="hidden" id="appId" value="<?= $signature['appId'] ?>">
<input type="hidden" id="nonceStr" value="<?= $signature['nonceStr'] ?>">
<input type="hidden" id="timestamp" value="<?= $signature['timestamp'] ?>">
<input type="hidden" id="signature" value="<?= $signature['signature'] ?>">
<div class="qgzmain outbox">
			<div class="details">
				<div class="topvis">
					<div class="topvis_l fl">
						<a href="javascript:history.go(-1);" ><img src="/static/h5/images/goback1.png"></a>
					</div>
					<div class="topvis_r fr">
						<a href="javascript:;" class="fr J-jump"><img src="/static/h5/images/goshare.png"></a>
					</div>
				</div>
			</div>
			<div class="share">
				<script id="share" type="text/html">
					<div class="qgzimg">
						<a href="javascript:;"><img src="{{data.cover_image}}"></a>
						<div class="box">
							<h4>{{data.title}}</h4>
							<p>{{data.subhead}}</p>
						</div>
					</div>
					<div class="screen">
						<!-- Swiper -->
					    <div class="swiper-container">
					        <div class="swiper-wrapper">
					            <div class="swiper-slide">
									<a href="" class="J-jump">分类</a>
					            </div>
					            <div class="swiper-slide">
									<a href="" class="J-jump">品牌</a>
					            </div>
					            <div class="swiper-slide">
									<a href="" class="J-jump">价格</a>
					            </div>
					            <div class="swiper-slide">
									<a href="" class="J-jump">折扣</a>
					            </div>
					            <div class="swiper-slide">
									<a href="" class="J-jump">价格（</a>
					            </div>
					            <div class="swiper-slide">
									<a href="" class="J-jump">折扣</a>
					            </div>
					            <div class="swiper-slide">
									<a href="" class="J-jump">品牌</a>
					            </div>
					            <div class="swiper-slide">
									<a href="" class="J-jump">折扣</a>
					            </div>
					        </div>
					    </div>

					</div>
					<div class="qgzlike wrapper">
						<ul class="wall">
							{{each data.item_list as v j}}
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
