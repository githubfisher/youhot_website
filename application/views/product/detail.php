<?php
/*$this->layout->load_css("/admin/css/mui-slider.css");*/
$this->layout->placeholder('title', $title);
?>
<div class="notes-top container-fluid" id="open-app-ctner" style="border-bottom:1px solid #EEEEEE">
    <div class="row">
        <div class="col-xs-6 notes-left ">
            <div class="notes-logo">
                <div class="logo-images">
                    <img src="/static/images/yhIcon.png" alt="styl">
                </div>
                <!-- <div class="logo-images-right">
                    <div class="logo-images-top">
                        <img src="/static/admin/images/style.png" alt="">
                    </div>
                    <div class="logo-images-bottom">
                        <p>原创服装设计平台</p>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="col-xs-6 notes-left">
            <a href="https://itunes.apple.com/us/app/yang-huoyouhot/id1222254047?l=zh&ls=1&mt=8">打开应用</a> 
        </div>
    </div>
</div>
<!-- 顶部导航结束 -->
<div style="height:51px;width:100%;" id="top-placehoder"></div>
<!-- banner部分-->
<div id="slider" class="mui-slider">
    <div class="mui-slider-group mui-slider-loop ">
        <!--        <!-- 额外增加的一个节点(循环轮播：第一个节点是最后一张轮播) -->
        <div class="mui-slider-item mui-slider-item-duplicate">
            <a href="#">
                <img src="<?= $album[count($album) - 1]['content'] ?>" >
            </a>
        </div>
        <?php foreach ($album as $item): ?>
            <?php if ($item['type'] == 2): ?>
                <div class="mui-slider-item">
                    <a href="#">
                        <iframe src="<?= preg_replace('/width=(\d*)&height=(\d*)/', 'width=300&height=450', $item['content']) ?>" frameborder="0" scrolling="no" width="300px" height="450px" style="margin-left: 10px"></iframe>
                    </a>
                </div>
                <?php
//                $url = parse_url($item['content']);
//                if(array_key_exists('query',$url) && !empty($url['query'])){
//                    $query = $url['query'];
//                    parse_str($query);
//                    echo <<<EOD
//                    <div class="mui-slider-item lunbo">
//                    <a href=#>
//                    <embed style="position:absolute;top:0;left:0;z-index: 100" src="http://yuntv.letv.com/bcloud.swf" allowFullScreen="true" quality="high"  width="400" height="350" align="middle" allowScriptAccess="always" flashvars="uu={$uu}&vu={$vu}&auto_play=1&gpcflag=1&width=400&height=350" type="application/x-shockwave-flash"></embed>
//                    </a>
//                    </div>
//EOD;
//                }
                ?>

            <?php else: ?>


                <div class="mui-slider-item">
                    <a href="#">
                        <img src="<?= $item['content'] ?>"/>
                    </a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <!--        <!-- 额外增加的一个节点(循环轮播：第一个节) -->
        <div class="mui-slider-item mui-slider-item-duplicate ">
            <a href="#">
                <img src="<?= $album[0]['content'] ?>">
            </a>
        </div>
    </div>

    <div class="mui-slider-indicator" style="display:none;">
        <?php for ($i = 0; $i < count($album); $i++): ?>
            <div class="mui-indicator<?php if ($i == 0) echo ' mui-active'; ?>"></div>
        <?php endfor; ?>

    </div>
</div>


<div class="shop-content-top">
    <div class="shop-t">
        <div class="shop-c-top">

            <div class="shop-content-left">
              <span><?= $title ?></span>
            </div>

            <div class="shop-content-m">
                <div class="s-m-top">
                    <div class="s-m-left" style="position:relative">￥<?= $price ?></div> 
                    <div style="width:100%;height:16px;color:#999;font-size:11px;line-height:16px;text-align:center;text-decoration: line-through;" class="wbl">￥<span class="wblMon"><?= $presale_price ?></span></div> 
                </div>
            </div>

        </div>
    </div>
</div>
<!--<div class="s-b-bottom" style="line-height:32px">
    <div class="bbb">剩余<span>
                <?php
/*                $days = '0天';
                if ($remain_days > 0) {
                    if (($td = $remain_days / (24 * 3600)) >= 1) {
                        $days = round($td) . '天';
                    } elseif (($td = $remain_days / (3600)) >= 1) {
                        $days = round($td) . '时';
                    } else {
                        $days = round($remain_days / 60) . '分';
                    }
                }
                echo $days;
                */?>
            </span>
    </div>
    <div class="s-b-right" style="text-align:right">
        预售数量<span><?/*= $presold_count */?></span>/<span><?/*= $presale_minimum */?></span>
    </div>
</div>-->
<div style="height:8px;width:100%;background:#E5E5E5;"></div>
<div class="img-personal">
    <div class="img-person">感兴趣的人</div>
    <div class="img-img">
        <div style="width:90%;height:60px;float:left">
            <?php foreach ($liked_users as $user): ?>
                <img src="<?= !empty($user['facepic']) ? $user['facepic'] : "/static/admin/images/touxiang.png" ?>" alt="">
            <?php endforeach; ?>

        </div>
        <div class="personal" style="padding-right:13px;"><a href="javascript:void(0)" style="color:#8B8888"><?= count($liked_users) ?>&nbsp&nbsp></a></div>
    </div>
</div>
<div style="height:8px;width:100%;background:#E5E5E5;"></div>
<div class="img-bottom">
    <div class="shejishi">
        <div class="shejishi-left">
            <img src="<?= isset($author_facepic) ? $author_facepic : "http://php.net/images/logo.php" ?>" alt="">

            <div class="shejishi-top">
                <p style="font-weight:bold;text-align:center;"><?= $author_nickname ?></p>
                <!--<span>独立设计师</span>-->
            </div>
        </div>
        <div class="shejishi-right"><a href="javascript:void(0)">品牌店铺</a></div>
    </div>
    <div style="width:100%;height:50px;">
        <div class="shopping-intrduce">
            <span style="display:block;text-align:left;line-height:50px">商品介绍</span>
        </div>
        <div class="shopping-right">
            <a href="javascript:void(0)"><img src="/static/admin/images/jiantou.png" alt="" style="width:15px;height:15px;transform:rotate(180deg)"></a>
        </div>
    </div>
    <div class="shop-banner-jiage container-fluid" style="height:auto;border-bottom:1px solid #CACACA;padding-bottom:10px">
        <div class="row">

            <div class="introducebox">
                <div class="introduce1" style="height:auto;">
                    <?php foreach ($description as $introduction): ?>
                        <div class="row">
                            <div class="col-xs-2 col-md-4 col-lg-2" style="width:30%;height:auto;float:left;text-align: left;line-height: 20px;color:#A0A0A0"><?= $introduction['title'] ?></div>
                            <div class="col-xs-10 col-md-8 col-lg-10" style="width:70%;height:auto;float:left;line-height: 20px;color:#383838;word-wrap: break-word;"><?= $introduction['content'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid content-logo-bottom">
        <div class="content-img-logo">

            <div class="style-top">
                <img src="/static/images/yhIcon.png" alt="styl">
            </div>
            <div class="style-middle">
                <!-- 一原创服装设计平台一 -->
            </div>
            <div class="style-bottom">
                <a href="javascript:void(0)"><img src="/static/admin/images/apple.png" alt=""></a>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="wx-popup" style="display:none;background-color: #515556;">
        <div class="row" style="height:100px">
            <div class="col-xs-2">
            </div>
            <div class="col-xs-8" style="padding: 23px 0px 0px;">
                <div style="text-align: left;" class="pull-right">
                    <p>点击右上角菜单</p>
                    <p>在Safari浏览器中打开并进入应用</p>
                </div>
            </div>
            <div class="col-xs-2 " style="text-align: right;padding: 10px 12px 0px 0px">
                <img src="/static/images/arrow.png" width="44" height="48">
            </div>
        </div>
    </div>

    <div class="modal fade" id="qrcode-modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header clearfix">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">扫码下载</h4>
                    </div>

                <div class="modal-body clearfix" style="text-align: center;">

                    <img alt="Scan me!" src="data:image/png;base64,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" class="center-block">

                    <p style="margin-top: 2em"> 目前仅iOS版本,请用iPhone扫码下载</p>

                </div>

            </div>
        </div>
    </div>

    <!-- 判断是在微博和微信中有提示的弹窗 -->
    <div id="fixedLayer" style="display:none;background:rgba(0,0,0,0.6);position:fixed;top:0;left:0;width:100%;height:100%;z-index:1000;">
        <div class="container-fluid" style="padding-top:100px;" >
            <div class="row" style="height:100px">
                <div class="col-xs-4">
                </div>
                <div class="col-xs-7" style="position:relative;">
                    <div style="text-align: center;border-radius:15px;background:#fff;padding: 20px 0px 20px;" >
                        <p style="font-size:16px;">点击在浏览器中打开</p>
                    </div>
                    <div style="width:70px;height:84px;position:absolute;right:0;top:-85px;">
                        <img src="/static/images/tips.png" width="100%" height="100%">
                    </div>
                </div>
                <div class="col-xs-1"></div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(function () {

            if($('.mui-slider-item').length > 3){

                var gallery = mui('.mui-slider');
                gallery.slider({
                  interval:5000
                });

                $('.mui-slider-indicator').show();

            }
            


            //取出里面的数字做判断
            var wblNum = parseInt($(".wblMon").text());
            if(wblNum == 0){
               $('.wbl').hide();
            }

            //判断浏览器是否是在微信和微博中打开
            //var browser = {
            //   versions: function () {

            //       var u = navigator.userAgent, app = navigator.appVersion;

            //       return {         //移动终端浏览器版本信息

            //           mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端

            //           iOS: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端

            //           Android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1 //android终端或uc浏览器

            //       };

            //   }(),

            //   language: (navigator.browserLanguage || navigator.language).toLowerCase()

            //}
/*
            $('.notes-left a').click(function(){

                if (browser.versions.mobile) { //判断是否是移动设备打开

                   var ua = navigator.userAgent.toLowerCase(); //获取判断用的对象

                   if (ua.match(/MicroMessenger/i) == "micromessenger" || ua.match(/WeiBo/i) == "weibo") {

                           document.getElementById('fixedLayer').style.display = "block";

                   }

                }
            })*/

            


            
            function is_weixn(){
                var ua = navigator.userAgent.toLowerCase();
                if(ua.match(/MicroMessenger/i)=="micromessenger") {
                    return true;
                } else {
                    return false;
                }
            }

            $('#a-open-app').click(function (e) {
                var platform="<?=isset($platform) ?$platform:''?>";
                if(platform == 'ios'){
                    if(is_weixn()){
                        $('#open-app-ctner').css({'position': 'initial', 'top': 'auto'});
                        $('#top-placehoder').remove();
                        var html = $('#wx-popup');
                        html.prependTo('body').slideDown('slow');
                    }else{
                        location.href = $(this).attr('href');
                    }
                }else{  //pc or an
                    $('#qrcode-modal').modal('show');
                }

                return false;
            });



        });

    </script>
