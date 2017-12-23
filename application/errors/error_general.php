<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'/>
<title>出错啦!</title>
<style type="text/css">

body {
background-color:	#fff;
margin:				40px;
font-family:		Lucida Grande, Verdana, Sans-serif;
font-size:			12px;
color:				#000;
}

#content  {
border:				#999 1px solid;
background-color:	#fff;
padding:			20px 20px 12px 20px;
}

h1 {
font-weight:		normal;
font-size:			14px;
color:				#990000;
margin:				0 0 4px 0;
}
p{
	padding:10px 0;
}

a{
	margin: 0 2px;
}
</style>
</head>
<body>
	<div id="content">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
		<p>请使用管理员帐号<a href="/user/login?r=<?=base64_encode('/admin')?>">登录</a>后重试</p>
		<p><a href='#' onclick="javascript:history.back()">返回</a> </p>
	</div>
</body>
</html>