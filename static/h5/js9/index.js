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
		if (isAndroid) {
			window.location = 'blank.html';
		} else {
			window.location = "com.style.YouHot://youhot"; //打开某手机上的某个app应用
			setTimeout(function() {
				window.location = appstoreUrl; //如果超时就跳转到app下载页
			}, 500);
		}

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
				console.log(data);
				var datainfo = data;
				var html = template('goosdetail', {
					data: datainfo
				});
				$(".goosdetail").html(html);

				var swiper = new Swiper('.swiper-container', {
					pagination: '.swiper-pagination',
					nextButton: '.swiper-button-next',
					prevButton: '.swiper-button-prev',
					paginationClickable: true,
					centeredSlides: true,
					autoplay: 2500,
					autoplayDisableOnInteraction: false
				});

				// 猜你喜欢瀑布流
				$('.wall').jaliswall({
					item: '.item'
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