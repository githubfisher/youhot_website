$(function() {

	var productId = getParameter('productId');
	if (!productId) {
		productId = '469471';
	}
	// 判断页面是否安装app，是则跳转到app页面，否则跳转到appstore里面下载app
	var appstoreUrl = "https://itunes.apple.com/us/app/yang-huoyouhot/id1222254047?l=zh&ls=1&mt=8";
	$(document).on('click', '.J-jump', function() {
		window.location = "youhot://com.style.YouHot"; //打开某手机上的某个app应用
		setTimeout(function() {
			window.location = appstoreUrl; //如果超时就跳转到app下载页
		}, 500);
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

				wx.ready(function(){
					// 分享给朋友
					wx.onMenuShareAppMessage({
					    title: data.title, // 分享标题
					    desc: data.description, // 分享描述
					    link: location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
					    imgUrl: data.cover_image, // 分享图标
					    type: '', // 分享类型,music、video或link，不填默认为link
					    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
					    success: function () { 
					        // 用户确认分享后执行的回调函数
					    },
					    cancel: function () { 
					        // 用户取消分享后执行的回调函数
					    }
					});
					// 分享到QQ
					wx.onMenuShareQQ({
					    title: data.title, // 分享标题
					    desc: data.description, // 分享描述
					    link: location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
					    imgUrl: data.cover_image, // 分享图标
					    type: '', // 分享类型,music、video或link，不填默认为link
					    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
					    success: function () { 
					        // 用户确认分享后执行的回调函数
					    },
					    cancel: function () { 
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
	// 获取参数
	function getParameter(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return decodeURI(r[2]);
		return null;
	}
})