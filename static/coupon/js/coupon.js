$(function() {

    // 判断页面是否安装app，是则跳转到app页面，否则跳转到appstore里面下载app
    var appstoreUrl = "https://itunes.apple.com/us/app/yang-huoyouhot/id1222254047?l=zh&ls=1&mt=8";
    $(document).on('click', '.J-jump', function() {
        window.location = "youhot://com.style.YouHot"; //打开某手机上的某个app应用
        setTimeout(function() {
            window.location = appstoreUrl; //如果超时就跳转到app下载页
        }, 500);
    })

    share();
    var _val;
    $('#btn').click(function(){
        _val = $("#input").val();
        if(_val ==""){
            alert("请输入手机号码");
        }else{
           receive(_val); 
        }
    })
    // 调取优惠券接口
    function share() {
        $.ajax({
            type: "POST",
            url: 'http://youhot.com.cn/coupon/showCoupon?rt=jsonp&coupon_id='+$("#coupon_id").val(),
            dataType: "jsonp",
            jsonp: "callback",
            jsonpCallback: "success",
            success: function(data) {
                var datainfo = data;
		$("#des").html(datainfo.description);
                var html = template('share', {
                    data: datainfo
                });
                $(".top").html(html);
            },
            error: function(xhr) {
                //请求出错处理
                alert("请求出错(请检查相关度网络状况.)");
            }
        })
    }
    // 领取优惠券接口
    function receive(_val) {
        $.ajax({
            type: "POST",
            url: 'http://youhot.com.cn/coupon/getCoupon?rt=jsonp&coupon_id=1&mobile='+_val,
            dataType: "jsonp",
            jsonp: "callback",
            jsonpCallback: "success",
            success: function(data) {
                if(data.res ==1){
                    alert(data.hits);
                    if(data.hits=="对不起,您已经领取过该优惠券,请勿重复领取!"){
                        window.location = "youhot://com.style.YouHot"; 
                    }else{
                        window.location = appstoreUrl; 
                    }
                }else{
                    alert(data.hits);
                    window.location = "youhot://com.style.YouHot"; 
                }
            },
            error: function(xhr) {
                //请求出错处理
                alert("请求出错(请检查相关度网络状况.)");
            }
        })
    }
})
