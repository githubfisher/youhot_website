<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="renderer" content="webkit">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
		<!-- 启用webAPP全屏模式-->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<!-- 隐藏状态栏或者设置状态栏的颜色-->
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<!-- 忽略数字自动识别为电话号码-->
		<meta name="format-detection" content="telephone=no">
		<!--控制缓存的失效日期 -->
		<meta http-equiv="Expires" content="-1">
		<!-- 禁止转码-->
		<meta http-equiv="Cache-Control" content="no-cache">
		<!-- 禁止缓存访问页面-->
		<meta http-equiv="Pragma" content="no-cache">
		<meta name="applicable-device" content="mobile">
		<!-- 页面关键词-->
		<meta content="" name="keywords">
		<meta name="description" content="">
		<title id="title"><?php if($this->layout->placeholder('title')){echo $this->layout->placeholder('title').'_youhot';}?></title>
		<?php echo $this->layout->css();?>
		<?php echo $this->layout->js();?>
	</head>
	<body>
		<?php echo $content; ?>
	</body>
</html>
