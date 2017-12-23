$(function() {
    getSign();
    function getSign() {
        $.ajax({
            type: "POST",
            url: 'http://youhot.com.cn/jssdk/getSignPackage?rt=jsonp',
            dataType: "jsonp",
            jsonp: "callback",
            jsonpCallback: "success00",
            success: function(data) {
                wx.config({
                    debug: false, 
                    appId: data.appId, // 必填，公众号的唯一标识
                    timestamp: data.timestamp, // 必填，生成签名的时间戳
                    nonceStr: data.nonceStr, // 必填，生成签名的随机串
                    signature: data.signature,// 必填，签名，见附录1
                    jsApiList: [
                        'onMenuShareTimeline', 'onMenuShareAppMessage',
                        'onMenuShareQQ', 'onMenuShareWeibo',
                        'onMenuShareQZone', 'hideMenuItems',
                        'getLocation', 'closeWindow',
                    ],
                });
            },
            error: function(xhr) {
                //请求出错处理
                alert("请求出错(请检查相关度网络状况.)");
            }
        })
    }
})