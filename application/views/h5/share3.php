<?php
$this->layout->load_css("/h5/css9/share3.css");
$this->layout->load_js('/h5/js6/jquery-2.1.1.min.js');
$this->layout->load_js('/h5/js6/template.js');
$this->layout->load_js('/h5/js9/share3.js');
$this->layout->load_js('/h5/js9/jweixin-1.2.0.js');
?>
<!--<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>-->
<input type="hidden" id="collection_id" value="<?= $collection_id ?>">
<input type="hidden" id="type" value="<?= $type ?>">
<input type="hidden" id="appId" value="<?= $signature['appId'] ?>">
<input type="hidden" id="nonceStr" value="<?= $signature['nonceStr'] ?>">
<input type="hidden" id="timestamp" value="<?= $signature['timestamp'] ?>">
<input type="hidden" id="signature" value="<?= $signature['signature'] ?>">
<div class="qgzmain outbox share">
			<script id="share" type="text/html">
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
				<div class="qgzmok">
					
					<div class="qgzimg">
						<a href="javascript:;"><img src="{{data.cover_image}}"></a>
						<div class="box">
							<h4>{{data.title}}</h4>
							<p>{{data.subhead}}</p>
						</div>
					</div>
					<p>{{data.description}}</p>
					{{each data.item_list}}
						{{if $value.img1}}
							<div class="imgwi"><img src="{{$value.img1}}"></div>
						{{/if}}
						{{if $value.text1}}
							<p>{{$value.text1}}</p>
						{{/if}}
						{{if $value.img2}}
							<div class="imgwi"><img src="{{$value.img2}}"></div>
						{{/if}}
						{{if $value.text2}}
							<p>{{$value.text2}}</p>
						{{/if}}
						{{each $value.products}}
							<div class="goods">
								
								<div class="details padding3">
									{{if $value.superscript}}
										{{each $value.superscript as v i}}
											{{if i==0}}
												<img src="{{v.url}}" alt="" class="label">
											{{/if}}
										{{/each}}
									{{/if}}								
									<a href="javascript:;" class="J-jump">
										<img src="{{$value.cover_image}}" />
									</a>	
								</div>
								<div class="shopname">
									<p>{{$value.title}}<em class="sbg1"></em></p>
									<dl>
										<dt>¥{{$value.price}} 
										     {{if $value.presale_price > 0}}
                                                                                        <i>¥{{$value.presale_price}}</i>
                                                                                    {{/if}}
										</dt>
										<dd>
											<a href="javascript:;" class="J-jump">查看商品</a>
										</dd>
									</dl>
								</div>
							</div>		
								
						{{/each}}
					{{/each}}
				</div>
			</script>
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
