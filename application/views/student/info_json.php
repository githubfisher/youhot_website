<?php
	/*
	 * ：
sinfores：< student-info-result：0表示操作成功，其他表示失败，后续版本可以具体定义错误码>，hint：<hint：操作失败的提示原因，成功时不需要这个域>， viewpm:<view-permission，查看权限，非登录用户：0，登录用户：1，有关系：2>, 
student:<student，学生所有信息>{username：<username>， realname：<realname>，school：<school-name>，
grade：<grade-name，譬如初中三年级>，telephone：<telephone-number>，
email：<email-address>，father：<parent-name>，fatherphone：<telephone of father>，
intro：<other-info submitted when student registering>，
status：<status of student，3表示正在接受辅导，即忙，不能打扰，2表示已经在客户端登陆，1表示在正在使用WEB服务，0：表示不在线，也没有使用WEB服务>}。
c)	student-id指学生的username，本文述及所有student-id都指这个意思。
d)	查看学生资料权限分三种：非登录用户只能查看网名和自我介绍；登录用户只能查看网名、姓名、学校、年级、自我介绍；学员/学生的老师（含免费试讲关系）可以查看所有资料，即上述接口描述的信息。对于没有权限查看的信息，返回的接口中可以不出现此域。
		define('i_infores', 'sinfores');
		define('i_hint', 'hint');
		define('i_datalevel', 'datalevel');
		define('i_student', 'student');
		
*
*/
$output_arr = array();
$output_arr[bp_student_info_result_field] = $sinfores;
if(!array_key_exists('res', $output_arr)){
	$output_arr['res'] = $sinfores;
	$output_arr['usertype'] = 'student';
}

if($sinfores==bp_operation_ok)
{
	$output_arr[bp_student_info_view_permission_field] = $datalevel;
	switch ($datalevel) {
		case PERMISSION_LOGIN:
			$output_arr[bp_student_info_field] = array(
				bp_student_username_field		=> $student['username']
				,bp_student_realname_field		=> $student['realname']
				,bp_student_school_field		=> $student['school']
				,bp_student_grade_field			=> $student['gradename']
				,bp_student_introduction_field	=> $student['info']
				,bp_student_face_image_url_field =>$student['facepic']
			);
		
		break;
		case PERMISSION_RELATED:
		case PERMISSION_MYSELF:	
//			var_dump($student);
		/*	{username：<username>， realname：<realname>，school：<school-name>，
grade：<grade-name，譬如初中三年级>，telephone：<telephone-number>，
email：<email-address>，father：<parent-name>，fatherphone：<telephone of father>，
intro：<other-info submitted when student registering>，
status：*/
				
				$tmparr = array(
				 bp_student_username_field		=> $student['username']
				 );
				if(array_key_exists('facepic', $student))
				{
					$tmparr[bp_student_face_image_url_field]= $student['facepic'];
				}
				if(array_key_exists('realname', $student))
				{
					$tmparr[bp_student_realname_field]		= $student['realname'];
				}
				if(array_key_exists('school', $student))
				{
					$tmparr[bp_student_school_field]		= $student['school'];
				}
				if(array_key_exists('gradename', $student))
				{
					$tmparr[bp_student_grade_field]		= $student['gradename'];
				}
				if(array_key_exists('telephone', $student))
				{
					$tmparr[bp_student_telephone_field]		= $student['telephone'];
				}	
				
				$tmparr += array(
				
				bp_student_father_field	=> $student['father']
				,bp_student_father_phone_field		=> $student['telephone2']
				,bp_student_introduction_field			=> $student['info']
				,bp_student_status_field	=>$student['status']
				
				);
			
				$output_arr[bp_student_info_field] = $tmparr;
				//[status] => 0 [username] => [usertype] => 0 [tinfores] => 0 [hint] => [viewpm] => 0 [isok] => 0 [familyname] => 刘 [school] => 北京大学 [grade] => 2 [pay] => 80 [realname] => 刘标1 [realname_isok] => 1 [familyname_isok] => [telephone] => 134342342343 [telephone_isok] => [email] => liubiao@aifudao.com [email_isok] => [gender] => 0 [gender_isok] => [birthday] => 9-9 [birthday_isok] => [major] => ?? [school_isok] => [major_isok] => [grade_isok] => [skilled_subject] => Array ( ) [skilledsub_isok] => [award] => 奥数金牌 江苏理科状元 [award_isok] => [exp_year] => 3 [expyear_isok] => [teach_exp] => 0 [teachexp_isok] => [intro] => 奥数金牌 江苏理科状元 [intro_isok] => [rank] => 中级教师 [facepic] => http://user.aifudao.com/74c73a8a24935d05b3a2177c5666ddea.jpg [smallpic] => http://user.aifudao.com/9c55a83e505e5f3da6b66b77f415de42.jpg [facepic_isok] => [middlepic] => http://user.aifudao.com/c44540d7a15fc41fae0d04d1c700db7a.jpg [userid] => 8 [password] => 74b87337454200d4d33f80c4663dc5e5 [regtime] => 2011-08-14 09:55:13 [lastlogin] => 2011-10-05 15:49:27 [lastlogout] => 2011-10-05 15:51:22 [lastping] => 0000-00-00 00:00:00 [isblocked] => 0 [gradename] => 大学二年级 [skilled_grade] => Array ( ) [comments] => Array ( ) ) 
				//s chool：<school-name>，grade：<grade-name，譬如大学三年级>，department：<department>，telephone：<telephone-number>，email：<email>，idcard：<idcard-image-url>，edutitle：<education-card-title，学生证等证书的标题>，educard：<education-card-image-url，譬如学生证的URL，或者其他类似证件的URL>，idpicture：<id-picture-url，面试和认证当场流下的现场照片的URL>，ocert0：<other-certificate-image-caption-0：证书0的标题>, ocert1：<other-certificate-image-caption-1：证书1的标题>, ocert2：<other-certificate-image-caption-2：证书2的标题>, ocert3：<other-certificate-image-caption3：证书3的标题>, ocert4：<other-certificate-image-caption-4：证书4的标题>, ocerturl0：<other-certificate-image-url-0，其他各种证书0图片的URL>，ocerturl1：<other-certificate-image-url-1，其他各种证书0图片的URL>，ocerturl2：<other-certificate-image-url-2，其他各种证书0图片的URL>，ocerturl3：<other-certificate-image-url-3，其他各种证书0图片的URL>，ocerturl4：<other-certificate-image-url-4，其他各种证书0图片的URL>，intro：<introduction-info submitted when student registering>，status：<student-status：3表示正在辅导学生，即忙，不能打扰，2表示在线，可以随时发起辅导邀请，1表示在正在使用WEB服务，但是不可以发起辅导邀请，0：表示不在线，也没有使用WEB服务>，award：<award-information，获奖情况说明>，expYear：<experience-year-in-teaching，教学年限>，teachExp：<teaching-experence：教学经历说明>，rank：<rank-in-aifudao，在爱辅导上的教师登记>，pay：<sales_in_hour，按照小时计算的教学费用要求，即每小时收费>，rcount：<remark-count，推荐的评论数量，必须>=0>，trcount：<total-remark-count，该老师得到的学生有效评价总数，必须>=0>，remark：[{sid：<sid-of-the-fudao-class，评论的辅导课程当时的fudao会话ID>，username：<remark-username>，content：<remark-content，评论的内容>，date：<remark-on-date>，评论的日期，格式为YYYY-MM-DD
			
		break;
		case PERMISSION_NOT_LOGIN:
		default:
			$output_arr[bp_student_info_field]  = array(
				bp_student_username_field		=> $student['username']
				,bp_student_introduction_field	=> $student['info']
				
			);
			if(array_key_exists('facepic', $student)) {
				$output_arr[bp_student_info_field] += array(bp_student_face_image_url_field=>$student['facepic']);
			}
	
		break;
	}
	
}
else 
{
	$output_arr[bp_student_info_hint_field]	  = $hint;
}
echo_json($output_arr);
