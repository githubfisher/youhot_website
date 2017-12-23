<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php echo $this->layout->placeholder("title")?></title>


    <link rel="shortcut icon" href="/favicon.ico?v=1.0" />    <!-- Favicon -->
    <link rel="apple-touch-icon-precomposed" href="/static/images/styl_57.png?v=1.0">	<!-- For iPhone -->
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/images/styl_114.png?v=1.0">    <!-- For iPhone 4 Retina display -->
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/images/styl_72.png?v=1.0">    <!-- For iPad -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/images/styl_144.png?v=1.0">    <!-- For iPad Retina display -->
    <link rel="stylesheet" href="/static/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/admin/css/notes.css">
</head>
<body style="background:#e5e5e5">
<!-- 顶部导航栏开始 -->
<div class="notes-top container-fluid">
    <div class="row">
        <div class="col-xs-6 notes-left ">
            <div class="notes-logo" >
                <div class="logo-images">
                    <img src="/static/admin/images/logo111.png" alt="">
                </div>
                <div class="logo-images-right">
                    <div class="logo-images-top">
                        <img src="/static/admin/images/style.png" alt="">
                    </div>
                    <div class="logo-images-bottom">
                        <p style="width:110px">原创服装设计平台</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-6 notes-left">
            <a href="<?=$this->config->item('url')['open_app'].'?url='.urlencode("styl://viewBrandLogShow?blsID=$id")?>">打开应用</a>
        </div>
    </div>
</div>
<div style="height:51px;width:100%"></div>
<!-- 顶部导航结束 -->
<!-- content部分图片内容 -->
        <div class="notes-img"><img src="<?=$cover_image?>" alt=""></div><!-- @235h_lwh-->
<!-- banner结束 -->
<!-- 内容第一栏 -->
<div class="container-fluid notes-content">
<!--人物小头像-->
    <div class="img-cle">
        <img src="<?=$facepic?>" alt="">
        <span><?=$nickname?></span>
    </div>
    <div class="row">
        <div class="img-content">
            <p><?=$title?></p>
        </div>
        <div class="img-bottom-content">
            <span><?=$description?></span>
        </div>
    </div>
</div>
</div>


<?php foreach($item_list as $img_content):?>
<!-- 内容第二栏 -->
<div class="container-fluid content-scond">
        <div class="content-top">
            <div class="content-left">
                <p><?=$img_content['product_title']?></p></div>
            <div class="content-right">
                <span>￥<?=$img_content['product_price']?></span>
            </div>
        </div>
        <div class="content-bottom" style="padding-right:0">
            <img src="<?=$img_content['content']?>" alt=""><!-- @330h_lwh-->
        </div>
        <div class="content-content">
            <span><?=$img_content['text']?></span>
        </div>
</div>
<?php endforeach;?>
<!-- 内容第三栏 -->


<!-- 底部内容 -->
<div class="container-fluid content-logo-bottom">
    <div class="content-img-logo">
        <div style="width:100%;height:1px;"></div>
        <div class="style-top">
            <img src="/static/admin/images/logo111.png" alt="">
        </div>
        <div class="style-middle">
            一原创服装设计平台一</div>
        <div class="style-bottom">
            <a href=""><img src="/static/admin/images/apple.png" alt=""></a>
        </div>
 </div>
</div>
</body>
</html>
