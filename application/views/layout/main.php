<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    $version = STATIC_VERSION;

    if ($this->layout->placeholder('meta')) {
        echo $this->layout->placeholder('meta');
    }
    ?>
    <title><?php
        $title = (isset($title) && $title != '') ? $title : '';
        if ($this->layout->placeholder('title')) {
            $title = $this->layout->placeholder('title');
        }
        echo  $title; ?></title>
    <meta name="description" content="styl是全球最新流行时尚平台,设计师发布最新服装,粉丝预定最新服装"/>
    <meta name="keywords" content="styl,stylshow,时尚,服装,服装预售,上衣,包,高端,独立,个性化,女性"/>

    <!--[if lt IE 9]>

    <![endif]-->

    <?php echo $this->layout->css(); ?>

    <link rel="shortcut icon" href="/favicon.ico?v=1.0" />    <!-- Favicon -->
    <link rel="apple-touch-icon-precomposed" href="/static/images/styl_57.png?v=1.0">	<!-- For iPhone -->
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/images/styl_114.png?v=1.0">    <!-- For iPhone 4 Retina display -->
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/images/styl_72.png?v=1.0">    <!-- For iPad -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/images/styl_144.png?v=1.0">    <!-- For iPad Retina display -->

    <link href="/static/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

    <script src="/static/admin/js/jquery/dist/jquery.min.js"></script>
</head>
<body <?php if ($this->layout->placeholder('main_class')): ?>class="<?= $this->layout->placeholder('main_class') ?>"<?php endif; ?>>

<div class="bigbox">
    <!--logo部分-->
    <div class="top-logo"></div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-8 logoleft"><img src="/static/images/logo.png" alt="styl" ></div>
            <div class="col-xs-6 col-md-4 logoright"><a href="">帮助中心</a></div>
        </div>
    </div>
    <!--logo结束-->
    <!--banner开始部分-->
    <?php echo $content; ?>

    <!--banner结束部分-->
    <div class="bottom container-fluid" style="text-align: center">
        <div class="row">
            <p>版权所有 Copyright © 2015 STYL All Rights Reserved</p>
        </div>
    </div>
</div>

<?php if (array_key_exists('REQUEST_URI', $_SERVER)): ?>
    <input type='hidden' id='r-u' value='<?= base64_encode(str_replace(base_url(), '', $_SERVER["REQUEST_URI"])) ?>'/>
<?php endif; ?>


<?php echo $this->layout->js(); ?>

</body>
</html>

