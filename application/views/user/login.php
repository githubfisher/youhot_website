<?php
$this->layout->placeholder ( 'title', '登录' );
//$this->layout->placeholder ( 'nav_current', bp_nav_login );
$js = array ('admin/js/jquery/jquery.validate.js','admin/js/app.js' );
$this->layout->load_js ( $js );
$this->layout->load_css("admin/css/login.css");
$this->layout->load_css("admin/css/manage.css");

?>

<!--banner开始部分-->
<div class="banner  container-fluid ">
	<div class="row">
		<div class="bannerright ">
			<div style="margin-left:10px;padding-bottom:20px;"><img src="/static/admin/images/bannerword.png"  alt="styl"></div>
			<div class="row" style="text-align: center">


				<div class="col-xs-12 col-sm-6 col-md-8" style="height:292px;width:355px;background:#fff;">
					<?php
					$attr = array('name'=>'f_userreg',
							'id'	=> 'f_userreg'
					);

					$_current_url = current_whole_url();

					if(isset($hint)){
						echo '<div class="error">'.$hint.'</div>';
					}
					echo form_open($_current_url,$attr);

					$this->load->view('common/login_box.php');

					?>

				</div>



			</div>
		</div>
	</div>
</div>
<!--banner结束部分-->


<script>
$(document).ready(function(){

	$("#f_userreg").validate({
		rules: {
			username: {
				required: true,
				minlength: 2,
				maxlength: 20,
				email_phone:true
			},
			password: {
				required: true,
				minlength: 4,
				maxlength:16,
				alpha_dash:true
			}
		}
	});

	$(document).on('click', '.captcha-change',function(e){
		e.preventDefault();
		e.stopPropagation();
		var p = $(this).parent();
		$.getJSON('/user/captcha',function(res){
			console.log(res)
			p.find('.captcha').html(res.data);
		})
	}).on('change', '#captcha', function(e){
		var v = this.value.replace(/[^0-9]/g,'');
		if(v.length !== 4){
			$('#captcha_error').addClass('error').show().html('你输入的验证码不正确，请重新输入');
		}else{
			$('#captcha_error').hide();

		}
	});
});


</script>