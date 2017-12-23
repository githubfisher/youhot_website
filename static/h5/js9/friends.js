$(function() {
	
	
    getCoupon();
    function getCoupon() {
        $.ajax({
            type: "POST",
            url: 'http://youhot.com.cn/coupon/FriendCouponInfo?rt=jsonp',
            dataType: "jsonp",
            jsonp: "callback",
            jsonpCallback: "success",
            success: function(data) {
                var html = template('coupon', {
                    data: data
                });
                $(".friends").html(html);
                
                // 判断页面是否安装app，是则跳转到app页面，否则跳转到appstore里面下载app
				var appstoreUrl = "https://itunes.apple.com/us/app/yang-huoyouhot/id1222254047?l=zh&ls=1&mt=8";
				$('.J-jump').on('click', function() {
					var u = navigator.userAgent;
					var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
					if(isAndroid) {
						window.location = 'blank.html';
					} else {
						var ua = navigator.userAgent.toLowerCase();
						if(ua.match(/MicroMessenger/i) == "micromessenger") {
							$(".safari").fadeIn();
						} else {
							window.location = "com.style.YouHot://activity"; //打开某手机上的某个app应用
							setTimeout(function() {
								window.location = appstoreUrl; //如果超时就跳转到app下载页
							}, 3000);
						}
					}
				}).on('click','.safari',function(){
					$(".safari").fadeOut();
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
                                                title: '好友券', // 分享标题
                                                desc: 'youhot洋火，海外代下单一站式购物app', // 分享描述
                                                link: location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                                                imgUrl: 'http://youhot.com.cn/static/h5/images/fcoupon.jpg', // 分享图标
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
                                                title: '好友券',
                                                desc: 'youhot洋火，海外代下单一站式购物app',
                                                link: location.href,
                                                imgUrl: 'http://youhot.com.cn/static/h5/images/fcoupon.jpg',
                                                success: function() {
                                                },
                                                cancel: function() {
						}
                                        });
                                        // 分享到QQ
                                        wx.onMenuShareQQ({
                                                title: '好友券', // 分享标题
                                                desc: 'youhot洋火，海外代下单一站式购物app', // 分享描述
                                                link: location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                                                imgUrl: 'http://youhot.com.cn/static/h5/images/fcoupon.jpg', // 分享图标
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
                alert("请求出错(请检查相关度网络状况.)");
            }
        })
    }
    
})
