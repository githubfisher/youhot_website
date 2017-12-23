<?php
$this->layout->load_css('h5/css6/swiper.css');
$this->layout->load_css('h5/css9/detail.css');
$this->layout->load_js('h5/js6/jquery-2.1.1.min.js');
$this->layout->load_js('h5/js6/swiper.min.js');
$this->layout->load_js('h5/js6/template.js');
$this->layout->load_js('h5/js6/jaliswall.js');
$this->layout->load_js('h5/js9/detail.js');
$this->layout->load_js('/h5/js9/jweixin-1.2.0.js');
?>
<!--<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>-->
<style type="text/css">
			.download-pannel{
				position: fixed;
				max-width: 650px;
				left: 0;
				right: 0;
				margin: auto;
				bottom: 0;
				height: 50px;
				display: -webkit-box;
			    display: -webkit-flex;
			    display: -ms-flexbox;
			    display: flex;
			    -webkit-flex-flow: row wrap;
			    -ms-flex-flow: row wrap;
			    flex-flow: row wrap;
			    background: rgba(255,255,255,0.9);
			    box-shadow: 0 0 5px rgba(136,136,136,0.2);;
			}
			.download-pannel .left{
				font-size: 0;
				line-height: 0;
			}
			.download-pannel .left img{
				display: inline-block;
				vertical-align: top;
			}
			.download-pannel .left .logo{
				margin-top: 7px;
				height: 36px;
				width: 36px;
				margin-left: 10px;
				margin-right: 10px;
			}
			.download-pannel .left .appname{
				margin-top: 18px;
			}
			.download-pannel .right{
				-webkit-box-flex: 1;
			    -webkit-flex: 1;
			    -ms-flex: 1;
			    flex: 1;
			    text-align: right;
			}
			.download-pannel .right .open{
				padding: 0 12px;
				color: #fff;
				background: #d82315;
				height: 28px;
				line-height: 28px;
				display: inline-block;
				vertical-align: top;
				text-align: center;
				border-radius: 4px;
				margin-right: 10px;
				margin-top: 12px;
			}
    .text input{
         width: 88px;
         margin-left: 5px;
    }
</style>
<input type="hidden" id="productId" value="<?= $productId ?>">
<input type="hidden" id="appId" value="<?= $signature['appId'] ?>">
<input type="hidden" id="nonceStr" value="<?= $signature['nonceStr'] ?>">
<input type="hidden" id="timestamp" value="<?= $signature['timestamp'] ?>">
<input type="hidden" id="signature" value="<?= $signature['signature'] ?>">
<input type="hidden" id="referer" value="<?= $referer ?>">
<div class="outbox">
		<div>
			<div class="topvis">
				<div class="topvis_l fl">
					<a href="javascript:history.go(-1);" ><img src="/static/h5/images/goback1.png"></a>
				</div>
				<div class="topvis_r fr">
					<a href="javascript:;" class="J-jump"><img src="/static/h5/images/goback2.png"></a>
					<a href="javascript:;" class="fr J-jump"><img src="/static/h5/images/goshare.png"></a>
				</div>
			</div>
			<div class="goosdetail">
				<script id="goosdetail" type="text/html">
					<div class="qgzmain">
						<!-- <div class="details">
							<img src="{{data.cover_image}}" />
						</div> -->

						<div class="swiper-container details">
					        <div class="swiper-wrapper">
					            <div class="swiper-slide">
									<img src="{{data.cover_image}}" />
					            </div>
					            {{if data.album}}
						            {{each data.album as value i}}
						            	<div class="swiper-slide">
						            		<img src="{{value.content}}">
						            	</div>
						            {{/each}}
					            {{/if}}
					        </div>
					    </div>
						<div class="shopname">
							<p>{{data.title}}<a href="javascript:;" class="sbg1 J-jump"></a></p>
							<dl>
								<dt>¥{{data.price}}
									{{if data.presale_price != "0"}}
									<i>¥{{data.presale_price}}</i>
									{{/if}}
								</dt>
								{{if data.discount != "1.00"}}
								    <dd><span>{{data.discount*10 | Fixed}}折</span></dd>
								{{/if}}
							</dl>
						</div>
						{{if data.property != '' || data.property}}
						<div class="sjopname">
							{{each data.property as v i}}
							<p>{{v.name}}<em>{{v.value}}</em></p>
							{{/each}}
						</div>
						{{/if}}
						<div class="qgzcolor padding3">
							{{if data.available_color != '' || data.available_color }}
							<dl>
								<dt>选择颜色</dt>
								<dd>
									{{each data.available_color as v i}}
									<a href="javascript:;" class="J-jump {{if i==0 }}scolro1{{/if}}"  data-id="{{v.color_id}}">{{v.name}}</a> {{/each}}
								</dd>

							</dl>
							{{/if}} 
							{{if data.available_size != '' || data.available_size}}
							<dl>
								<dt>选择尺码</dt>
								<dd>
									{{each data.available_size as v i}}
									<a href="javascript:;" class="J-jump {{if i==0 }}scolro1 {{/if}}"  data-id="{{v.size_id}}">{{v.name}}</a> {{/each}}
								</dd>
							</dl>
							{{/if}}
						</div>
						<div class="padding3 qgzxinxi">
							<h6><a href="javascript:;" class="J-jump">尺码表<em></em></a></h6>
							<h6><a href="javascript:;" class="J-jump"><img src="/static/h5/images/qibtn.png">{{data.store_name}}<span>&middot;</span><i>{{data.sales}}</i><em></em></a></h6>
							<h6><a href="javascript:;">正品保证：<span>全球正品 官网直发</span><em class="rotate1"></em></a></h6> {{if data.description}}
							<h6>商品简介<em class="rotate2"></em>
									<p>{{data.description}}<a href="javascript:;" class="J-jump">翻译成原文</a></p>
							</h6> {{/if}}
						</div>
						<div class="bargray"></div>
						{{if data.others != ''}}
							<div class="padding3 qgzshop">
								<h3>{{data.author_nickname}}<a href="javascript:;" class="J-jump">+关注</a></h3>
								<ul>
									{{each data.others}}
									<li>
										<a href="javascript:;" class="J-jump"><img src="{{$value.cover_image}}"></a>
									</li>
									{{/each}}
									<li>
										<a href="javascript:;" class="J-jump"><img src="/static/h5/images/dimg4.jpg"></a>
									</li>										
								</ul>
							</div>
						{{/if}}
						<div class="bargray"></div>
						{{if data.commonds != ''}}
						<div class="qgzlike">
							<h4>猜你喜欢</h4>
							<ul class="wall">
								{{each data.commonds as v j}}
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
						{{/if}}

					</div>
				</script>
			</div>
		</div>
	</div>
		
		<div class="maskbox">
			<div class="text">
				<p>下载App并注册</p>
				<p>填写邀请人手机号
					<!--<input id="text" type="text" value="1111111223" />-->
					<a id="selector">111</a>
				</p>
				<p>得50元优惠券</p>
			</div>
			<div class="btnbox">
				<button style="color: red;" id="copy">复制手机号</button>
				<a id="close">关闭</a>
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
