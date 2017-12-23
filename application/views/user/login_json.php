<?php 
$output_array =  array();

$output_array[bp_result_field]		= $res;
if(isset($hint) && $hint!='')
{
	$output_array[bp_result_hint_field]		= $hint;
	
}
if(isset($sid) && $sid!='')
{
	$output_array[bp_login_bp_session_id_field]		= $sid;

}
//if(isset($username) && $username!='')
//{
//	$output_array['username']		= $username;
//
//}
if(isset($userinfo) && $userinfo!='')
{
	$output_array['userinfo']		= $userinfo;

}
if(isset($utype) && $utype!='')
{
	$output_array[bp_login_user_type_field]		= $utype;
	
}
if(isset($oauth_source)){
	$output_array['oauth_source'] = $oauth_source;
}

if(isset($teacher_type)){
	$output_array['teacher_type'] = $teacher_type;
}

if(isset($need_captcha)){
	$output_array['need_captcha'] = $need_captcha;
}
if(isset($captcha)){
	$output_array['captcha'] = $captcha;//todo:只保留路径
}


echo_json($output_array);

//end
