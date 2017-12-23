$(document).ready(function(){
    var appstoreUrl = "https://itunes.apple.com/us/app/yang-huoyouhot/id1222254047?l=zh&ls=1&mt=8";
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    if(isAndroid) {
  	window.location = 'http://www.youhot.com.cn/blank.html';
    } else {
	var ua = u.toLowerCase();
	if(ua.match(/MicroMessenger/i) == "micromessenger") {
		$(".safari").fadeIn();
	} else {
		window.location = "com.style.YouHot://youhot"; //打开某手机上的某个app应用
		setTimeout(function() {
			window.location = appstoreUrl; //如果超时就跳转到app下载页
		}, 3000);
	}
    }
})
