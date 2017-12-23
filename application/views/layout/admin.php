<!DOCTYPE html>
<html>

	<head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <title><?php if($this->layout->placeholder('title')){echo $this->layout->placeholder('title');}?>_youhot</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <link rel="shortcut icon" href="/favicon.ico?v=1.0" />    <!-- Favicon -->
        <link rel="apple-touch-icon-precomposed" href="/static/images/styl_57.png?v=1.0">	<!-- For iPhone -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/images/styl_114.png?v=1.0">    <!-- For iPhone 4 Retina display -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/images/styl_72.png?v=1.0">    <!-- For iPad -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/images/styl_144.png?v=1.0">    <!-- For iPad Retina display -->

        <link href="/static/admin/plugins/messenger/css/messenger.css?v=<?=STATIC_ADMIN_VERSION?>" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/static/admin/plugins/messenger/css/messenger-theme-future.css?v=<?=STATIC_ADMIN_VERSION?>" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/static/admin/plugins/messenger/css/messenger-theme-flat.css?v=<?=STATIC_ADMIN_VERSION?>" rel="stylesheet" type="text/css" media="screen"/>


		<link rel="stylesheet" href="/static/admin/css/var.css" />
<link href="/static/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="/static/admin/css/response.css" />
		<link rel="stylesheet" href="/static/admin/css/manage.css" />

		<?php echo $this->layout->css();?>
		<script src="/static/admin/js/jquery/dist/jquery.min.js"></script>
		<script src="/static/admin/plugins/bootstrap/js/bootstrap.min.js"></script>
		

		<?php if (isset($product_id)): ?>
        <script type="text/javascript">
            var g_product_id = <?=$product_id?>
        </script>
        <?php endif?>

	</head>
	<body>
			<!--顶部边框-->
			<div class="message-top"></div>
			<!--顶部logo部分-->
			<div class="m-logo ">
				<div class="m-top-left ">
				<a href="http://youhot.com.cn/admin/search"><img src="/static/images/logo.png" alt="styl" /></a>
				</div>
				<div class="m-top-right ">
						<div class="m-top-right-circle"><img src="<?=$user['facepic']?$user['facepic']:"/static/images/defaultface.png"?>" alt="user-image"  /></div>
						<div class="btn-group">
					<!--用户名登陆状态-->

						<span type="" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor:pointer">
     						<?=$user['nickname']?>
							<span class="caret"></span>
						</span>
						<ul class="dropdown-menu" style="position: absolute;left:-20px!important;">
							<li><a href="/admin/user/<?=$user['userid']?>/edit">编辑资料</a></li>
							<li><a href="/user/logout" id="logout">退出</a></li>

						</ul>
					</div>

				</div>
			</div>
			<!--中部内容部分开始-->
			<div class="m-middle">
				<div class="m-center-content">
					<!--中部内容左侧一栏-->
					<div class="m-center-left">
						<div class="index">
							<div class="index-word">
								<img src="/static/admin/images/shou.png" alt=""  />
								<a href="<?=$this->config->item('url')['admin']?>" id="shouye">首页</a>
							</div>
						</div>
						<div class="m-list" style="margin-bottom: 30px;">
							<a href="<?=$this->config->item('url')['admin_order']?>"><i class="icon icon-order"></i> 订单管理</a>
							<a href="<?=$this->config->item('url')['admin_search']?>"><i class="icon icon-prev"></i> 商品搜索</a>
							<a href="<?=$this->config->item('url')['admin_product']?>"><i class="icon icon-product"></i> 商品管理</a>
							<a href="<?=$this->config->item('url')['admin_collection']?>"><i class="icon icon-col"></i> 专辑管理</a>
							<a href="<?=$this->config->item('url')['admin_ct1']?>"><i class="icon icon-order"></i> 折扣推荐</a>
							<a href="<?=$this->config->item('url')['admin_ct3']?>"><i class="icon icon-stat"></i> 运营帖三</a>
							<a href="<?=$this->config->item('url')['admin_ctl']?>"><i class="icon icon-select"></i> 运营总表</a>
							<!--<a href="<?=$this->config->item('url')['admin_user']?>"><i class="icon icon-user"></i> 用户管理</a>-->
						<!--</div>
						<div class="m-list">-->
							<a href="<?=$this->config->item('url')['admin_commond']?>"><i class="icon icon-select"></i> 单品推荐</a>
							<a href="<?=$this->config->item('url')['admin_role']?>" class="hidden"> <i class="icon icon-prev"></i> 权限管理</a>
							<a href="<?=$this->config->item('url')['admin_store']?>"><i class="icon icon-audit"></i> 商城管理</a>
						<!--	<a href="<?=$this->config->item('url')['admin_banner']?>"><i class="icon icon-prev"></i> 推荐管理</a> -->
							<a href="<?=$this->config->item('url')['admin_size']?>"><i class="icon icon-product"></i> 尺码图片</a><!-- by fisher 2017-03-15 -->
							<a href="<?=$this->config->item('url')['return_back']?>"><i class="icon icon-order"></i> 售后管理</a>
							<a href="<?=$this->config->item('url')['admin_coupon']?>"><i class="icon icon-col"></i> 卡券管理</a>
							<a href="<?=$this->config->item('url')['admin_history']?>"><i class="icon icon-stat"></i> 搜索历史</a>
							<a href="<?=$this->config->item('url')['admin_category']?>"><i class="icon icon-select"></i> 分类管理</a>
							<a href="<?=$this->config->item('url')['admin_referer']?>"><i class="icon icon-user"></i> 分享详情</a>
			                	</div>
					</div>
					<!--中部内容右侧一栏-->
					<!-- content-->
                <?php if (isset($res)): ?>
                    <?php if (($res == bp_operation_usertype_not_match) || ($res == bp_operation_user_forbidden)): ?>
                        <div class='error box'>
                            <?= $hint ?>
                            <a href='#' class='login'>请登录</a>
                        </div>
                    <?php elseif ($res == bp_operation_ok): ?>
                        <?= $content ?>
                    <?php else: ?>
                        <?= $hint ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?= $content ?>
                <?php endif; ?>
               </div>
			</div>
			<!--内容中部分结束-->
        <div class="container-fluid " style="background:#B8B9B9;height:80px;float:left;text-align: center;line-height: 80px;color:#E5E5E5" >
				<div class="row">
						<!--<span >Copyright @2016 Youhot Allrights Resevered</span>-->
						<span >Copyright &copy 2016 - 2018 Youhot. Allrights Resevered. 晖焕（北京）科技有限公司 版权所有</span>
				</div>
			</div>

			<div class="modal fade" tabindex="-1" id="layout-modal" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Modal title</h4>
						</div>
						<div class="modal-body">
							<p>One fine body&hellip;</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="button" class="btn btn-primary">下一步</button>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

	<?php echo $this->layout->js();?>
			<script src="/static/admin/plugins/messenger/js/messenger.min.js" type="text/javascript"></script>
			<script src="/static/admin/js/app.js?v=<?=STATIC_ADMIN_VERSION?>" type="text/javascript"></script>
			<script src="/static/admin/js/manage.js?v=<?=STATIC_ADMIN_VERSION?>" type="text/javascript"></script>
	</body>

</html>
