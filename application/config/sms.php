<?php

$config['host'] = 'bcms.api.duapp.com';

/* == 短信配置 == */
$config['mobile_sms'] = array();
//afd_support
$config['mobile_sms'][] = array(
	'accessKey'=>'896c72979347031b78ab176251cfd11c',
	'secretKey'=>'CFb3b0fd6ea40691ea0783d5e79aff6b',
	'queueName'=>'d1c0703c87490d2d3a599784e06295be',
	'host'=> $config['host']
);
//aifudao_mabo
$config['mobile_sms'][] = array(
	'accessKey'=>'4480159578bea6a62d4ae635d7b4c60e',
	'secretKey'=>'55c9a386eb9df21d5c4bf52b5a25bc47',
	'queueName'=>'bc3fb33b7a92b80a6801509e1f5b07af',
	'host'=> $config['host']
);
//afd_zhangyi
$config['mobile_sms'][] = array(
	'accessKey'=>'605ec94f0b8d59f4e4072674a3003bcd',
	'secretKey'=>'01bcf02a54bb77845f2b44271e1b695c',
	'queueName'=>'82d2893576c65af948c0b6eeb29a018f',
	'host'=> $config['host']
);
//aifudao_sms2
$config['mobile_sms'][] = array(
	'accessKey'=>'626cafb668a4bfcbe8537d6cbed4ef7b',
	'secretKey'=>'AE13b11ba6c6ae246c91753377189160',
	'queueName'=>'caf2a6c506838b1aa50c1cfa9fc1d217',
	'host'=> $config['host']
);

//aifudao_sms
$config['mobile_sms'][] = array(
	'accessKey'=>'D60a31a47f91d57272372d861e37cb33',
	'secretKey'=>'08a337815879d26d89da5229266ac480',
	'queueName'=>'de78113d3df7ceaf94260cc495879f7a',
	'host'=> $config['host']
);

//aifudao
$config['mobile_sms'][] = array(
	'accessKey'=>'0Ef025d29df8c3382be09cb6b0b29b84',
	'secretKey'=>'B955c72525c33d4bf7551cc58ed99a5b',
	'queueName'=>'1dd52df410f97bb0073eed564223987a',
	'host'=> $config['host']
);

//tconzi
$config['mobile_sms'][] = array(
	'accessKey'=>'E50d9dffa91c544fcbda278a76e0af95',
	'secretKey'=>'2f5b6d078a923fdde5544e38a6460016',
	'queueName'=>'91105fc1ff7733c1c6658d4f5411ac18',
	'host'=> $config['host']
);


/* == 百度消息服务配置，使用aifudao帐号 == */
$config['query_message'] = array(
		'accessKey'=>'0Ef025d29df8c3382be09cb6b0b29b84',
		'secretKey'=>'B955c72525c33d4bf7551cc58ed99a5b',
		'queueName'=>'1dd52df410f97bb0073eed564223987a',
		'host'=> $config['host']
);


/* == 百度云存储，使用aifudao帐号 == */
$config['bcs_conf_paper'] = array(
		'accessKey'=>'0Ef025d29df8c3382be09cb6b0b29b84',
		'secretKey'=>'B955c72525c33d4bf7551cc58ed99a5b',
		'host'=> 'bcs.duapp.com',
		'bucket'=>'aifudao-paper'
);


/* == 百度云存储，用于生成的文件缓存，使用afd_support帐号 == */
$config['bcs_conf_cache'] = array(
	'accessKey'=>'896c72979347031b78ab176251cfd11c',
	'secretKey'=>'CFb3b0fd6ea40691ea0783d5e79aff6b',
	'host'=> 'bcs.duapp.com',
	'bucket'=>'aifudao-cache'
);

/* == 百度云存储，用于资源文件的存储 ，使用aifudao_sms帐号 == */
$config['bcs_conf_resource'] = array(
	'accessKey'=>'D60a31a47f91d57272372d861e37cb33',
	'secretKey'=>'08a337815879d26d89da5229266ac480',
	'host'=> 'bcs.duapp.com',
	'bucket'=>'aifudao-resource'
);

/* == 百度云存储，用于资源文件的存储 ，使用aifudao_sms2帐号 == */
$config['bcs_conf_question'] = array(
	'accessKey'=>'626cafb668a4bfcbe8537d6cbed4ef7b',
	'secretKey'=>'AE13b11ba6c6ae246c91753377189160',
	'host'=> 'bcs.duapp.com',
	'bucket'=>'aifudao-question'
);




/* == 百度开放云存储，使用aifudao帐号 == */
$config['bos_conf_paper'] = array(
		'accessKey'=>'1104611f44c840b98883d5a53d23202e',
		'secretKey'=>'8f07579f017f4b2a8054b2355c17bd94',
		'host'=> 'bj.bcebos.com',
		'type' =>'bos',
		'bucket'=>'aifudao-paper'
);


/* == 百度开放云存储，使用aifudao帐号 == */
$config['bos_conf_cache'] = array(
	'accessKey'=>'1104611f44c840b98883d5a53d23202e',
	'secretKey'=>'8f07579f017f4b2a8054b2355c17bd94',
	'host'=> 'bj.bcebos.com',
	'type' =>'bos',
	'bucket'=>'aifudao-paper'
);

/* == 百度开放云存储，使用aifudao帐号 == */
$config['bos_conf_resource'] = array(
	'accessKey'=>'1104611f44c840b98883d5a53d23202e',
	'secretKey'=>'8f07579f017f4b2a8054b2355c17bd94',
	'host'=> 'bj.bcebos.com',
	'type' =>'bos',
	'bucket'=>'aifudao-resource'
);

/* == 百度开放云存储，使用aifudao帐号 == */
$config['bos_conf_question'] = array(
	'accessKey'=>'1104611f44c840b98883d5a53d23202e',
	'secretKey'=>'8f07579f017f4b2a8054b2355c17bd94',
	'host'=> 'bj.bcebos.com',
	'type' =>'bos',
	'bucket'=>'aifudao-question'
);



/* == 百度开放云存储，使用aifudao帐号 == */
$config['bos_conf_test'] = array(
	'accessKey'=>'1104611f44c840b98883d5a53d23202e',
	'secretKey'=>'8f07579f017f4b2a8054b2355c17bd94',
	'host'=> 'bj.bcebos.com',
	'type' =>'bos',
	'bucket'=>'aifudao-test'
);



/* == 站内消息配置 == */

//$config['ims_server_ip'] = '106.3.40.123';
$config['ims_server_ip'] = '210.14.156.173';
//$config['ims_server_ip'] = '210.14.147.28';
$config['ims_port'] = '9009';
$config['ims_user'] = 'admin';


