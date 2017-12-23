$(function(){
    $("#logout").click(function(){
        var data={
            "rt":"json",
        };
        $.post($(this).attr('href'),data, function (res) {
            if (res.res != 0) {
                showErrorMessage(res.hint);
            }else{
                window.location.href = "/user/login?r=L2FkbWlu"
            }
        }, 'json');
        return false;
    })


    function getAct(url){
        var r = /action=([^?&]*)/,
            res = url.match(r);
        return res? res[1] : '';
    }
    var act = getAct(location.href);

    var curLink = $(".admin_side .nav-tab, .admin_side .sub-tab").filter(function(index){
        if ($(this).attr('href').replace(/http:\/\/[^\/]*/,'').replace(/[?#].*$/,'').replace(/\/$/, '') == location.pathname ){
            if(!act) {
                return true;
            }else{
                return act == getAct($(this).attr('href'))
            }
        }
    })
    if(curLink.length >0){
        curLink.parent().addClass('on');
        if(curLink.hasClass('sub-tab')){
            curLink.closest('.admin-sub-nav').css('display','block').parent('li').addClass('current');
        }else{
            curLink.parent('li').addClass('current')
        }
    }

})
