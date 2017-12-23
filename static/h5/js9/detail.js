$(function() {

	var productId = getParameter('productId');
	if (!productId) {
		productId = '469471';
	}
	// 判断页面是否安装app，是则跳转到app页面，否则跳转到appstore里面下载app
	var appstoreUrl = "https://itunes.apple.com/us/app/yang-huoyouhot/id1222254047?l=zh&ls=1&mt=8";
	$(document).on('click', '.J-jump', function() {

		var u = navigator.userAgent;
		var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
		console.log('isAndroid:'+isAndroid);
		if (isAndroid) {
			window.location = 'http://youhot.com.cn/blank.html';
		} else {
			var ua = navigator.userAgent.toLowerCase();
			if(ua.match(/MicroMessenger/i) == "micromessenger") {
				$(".safari").fadeIn();
			} else {
				window.location = "com.style.YouHot://product_id="+productId+"&referer="+$("#referer").val(); //打开某手机上的某个app应用
				setTimeout(function() {
					window.location = appstoreUrl; //如果超时就跳转到app下载页
				}, 3000);
			}
		}
	}).on('click','.safari',function(){
		$(".safari").fadeOut();
	})

	goodsdetail();

	// 调取商品接口
	function goodsdetail() {
		$.ajax({
			type: "POST",
			url: 'http://youhot.com.cn/product/detail?rt=jsonp&product_id=' + productId,
			dataType: "jsonp",
			jsonp: "callback",
			jsonpCallback: "success",
			success: function(data) {
				// console.log(data);
				template.helper('Fixed', function(value) {
					value = parseFloat(value);
					return value.toFixed(1);

				})
				var datainfo = data;
				var html = template('goosdetail', {
					data: datainfo
				});
				$(".goosdetail").html(html);
				$(".swiper-slide").height($(".swiper-slide").width());
				var swiper = new Swiper('.swiper-container', {
					pagination: '.swiper-pagination',
					paginationClickable: true,
					loop: true
				});

				// 猜你喜欢瀑布流
				$('.wall').jaliswall({
					item: '.item'
				});
				wx.config({
                    debug: false,
                    appId: $("#appId").val(), // 必填，公众号的唯一标识
                    timestamp: $("#timestamp").val(), // 必填，生成签名的时间戳
                    nonceStr: $("#nonceStr").val(), // 必填，生成签名的随机串
                    signature: $("#signature").val(),// 必填，签名，见附录1
                    jsApiList: [
                        'onMenuShareTimeline', 'onMenuShareAppMessage',
                        'onMenuShareQQ', 'onMenuShareWeibo',
                        'onMenuShareQZone', 'hideMenuItems',
                        'getLocation', 'closeWindow',
                    ],
                });
				wx.ready(function() {
					// 分享给朋友
					wx.onMenuShareAppMessage({
						title: data.title, // 分享标题
						desc: 'youhot洋火，海外代下单一站式购物app', // 分享描述
						link: location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
						imgUrl: data.cover_image, // 分享图标
						type: '', // 分享类型,music、video或link，不填默认为link
						dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
						success: function() {
							// 用户确认分享后执行的回调函数
						},
						cancel: function() {
							// 用户取消分享后执行的回调函数
						}
					});
					// 分享给朋友圈
                                        wx.onMenuShareTimeline({
                                                title: data.title,
                                                desc: 'youhot洋火，海外代下单一站式购物app',
                                                link: location.href,
                                                imgUrl: data.cover_image,
                                                success: function() {
                                                },
                                                cancel: function() {
                                                }
                                        });
					// 分享到QQ
					wx.onMenuShareQQ({
						title: data.title, // 分享标题
						desc: 'youhot洋火，海外代下单一站式购物app', // 分享描述
						link: location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
						imgUrl: data.cover_image, // 分享图标
						type: '', // 分享类型,music、video或link，不填默认为link
						dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
						success: function() {
							// 用户确认分享后执行的回调函数
						},
						cancel: function() {
							// 用户取消分享后执行的回调函数
						}
					});
				});
			},
			error: function(xhr) {
			    //请求出错处理
			    //alert("请求出错(请检查相关度网络状况.)");
			}
		})
	}
	// 获取参数
	function getParameter(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return decodeURI(r[2]);
		return null;
	}
        phone();
	//	获取手机号
	function phone() {
		$.ajax({
			type: "POST",
			url: 'http://www.youhot.com.cn/user/getUserMobile?rt=jsonp&referer='+$("#referer").val(),
			dataType: "jsonp",
			jsonp: "callback",
			jsonpCallback: "success",
			success: function(data) {
				//				console.log(data.hits);
				if(data.hits == "success") {
					$("#phone").text(data.mobile);
					$("#selector").html(data.mobile);
				} else {
					$(".text").html(data.hits);
				}

			},
			error: function(xhr) {
				//请求出错处理
				alert("请求出错(请检查相关度网络状况.)");
			}
		})
	}
	$("#close").click(function() {
		$(".maskbox").hide();
	});

	var aEle = document.querySelector('#copy');
	aEle.addEventListener('click', function() {
		var copyDOM = document.querySelector('#selector');
		var range = document.createRange();
		range.selectNode(copyDOM);
		window.getSelection().addRange(range);
		var successful = document.execCommand('copy');
		try {
			// Now that we've selected the anchor text, execute the copy command  

			var msg = successful ? 'successful' : 'unsuccessful';
			console.log('Copy email command was ' + msg);
		} catch(err) {
			console.log('Oops, unable to copy');
		}

		// Remove the selections - NOTE: Should use
		// removeRange(range) when it is supported  
		window.getSelection().removeAllRanges();
		$(".J-jump").click();
		$(".maskbox").hide();
	}, false);

	//	$("#copy").click(function() {
	//		//		copyUrl2();
	//		var copyDOM = document.querySelector('#mobile');
	//		//		alert(copyDOM.innerHTML);
	//		var range = document.createRange();
	//		// 选中需要复制的节点
	//		range.selectNode(copyDOM);
	//		// 执行选中元素
	//		window.getSelection().addRange(range);
	//		// 执行 copy 操作
	//		var successful = document.execCommand('copy');
	//		try {
	//			var msg = successful ? 'successful' : 'unsuccessful';
	//			console.log('copy is' + msg);
	//		} catch(err) {
	//			console.log('Oops, unable to copy');
	//		}
	//		// 移除选中的元素
	//		window.getSelection().removeAllRanges();
	//	});

	function copyUrl2() {
		var Url2 = $("#text");
		Url2.select(); // 选择对象
		var content = window.getSelection().toString();
		document.execCommand("copy"); // 执行浏览器复制命令
		$(".J-jump").click();
		$(".maskbox").hide();
		//		alert("复制成功，去贴粘。");
	}
})
