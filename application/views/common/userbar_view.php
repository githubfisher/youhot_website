
<div id="menu_ub">
<?php 
echo anchor('user/index','我的辅导');
if(isset($usertype))
{
	switch ($usertype) {
		case USERTYPE_USER:
			echo anchor(bp_url_student_profile,'个人资料');
			echo anchor(bp_url_student_course,'我的课程');
			echo anchor(bp_url_student_accounts,'我的学费');
			break;
		case USERTYPE_DESIGNER:
			echo anchor(bp_url_teacher_profile,'个人资料');
			echo anchor(bp_url_teacher_spare,'预约时间设置');
			echo anchor(bp_url_teacher_intro,'我的介绍页');
			echo anchor(bp_url_teacher_accounts,'我的账户');
			break;
	}
}


?>

</div>

