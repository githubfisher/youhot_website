$(function() {
	// 判断页面是否安装app，是则跳转到app页面，否则跳转到appstore里面下载app
	var appstoreUrl = "https://itunes.apple.com/us/app/yang-huoyouhot/id1222254047?l=zh&ls=1&mt=8";
/*	$(document).on('click', '.J-jump', function() {
		window.location = "youhot://com.style.YouHot"; //打开某手机上的某个app应用
		setTimeout(function() {
			window.location = appstoreUrl; //如果超时就跳转到app下载页
		}, 500);
	})*/
	var order = $("#order").val();
	var od = order.replace(/'/g, '"');
	var url = 'http://youhot.com.cn/product/filterProduct?rt=jsonp&pr=['+ $("#price").val()+']&cate=['+ $("#category").val()+']&br=[' + $('#brand').val()  + ']&dr=['+ $("#discount").val()+']&of='+ $("#of").val()+'&lm='+ $("#lm").val()+'&store=['+ $("#store").val()+']&kw='+$("#kw").val()+'&sort='+od;
	console.log(url);
	share();
	// 调取商品接口
	function share() {
		$.ajax({
			type: "POST",
			url: url,
			dataType: "jsonp",
			jsonp: "callback",
			jsonpCallback: "success",
			success: function(data) {
				console.log(data);
				var datainfo = data;
				var html = template('share', {
					data: datainfo
				});
				$(".share").html(html);
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
