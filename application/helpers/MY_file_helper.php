<?php
/**
 * 从mysql数据库中读出二进制文件，生成图片，返回url
 * Enter description here ...
 * @param blob $data
 * @return image file url
 */
function face($data, $username, $field) {
	//todo:需要判断type，保存临时路径，过期时间
	//var_dump($data);
	$url = getimg( $data, $username, $field );
	if ($url == '') {
		return base_url() . '/static/defaultface.jpg';
	
	}
	else {
		return $url;
	}
}

function image_url($username, $type, $field) {
	if($type == 'face'){
		return base_url().'img/face/'.urlencode($username);
	}
	if($type == 'auth'){
		return sprintf(base_url().'img/auth/%s/%s', urlencode($username),$field);
	}

	return '';
}
/*根据二进制内容生成图片并返回url
 * @param byte $data
 * @param string $username
 * @param string $field
 * @return string  '' or url
 */
function getimg($data, $username, $field) {
	//log_message('debug','generate image happened');
	

	if ($data == null) {
		return '';
	
	}
	
	$filename = md5( sprintf( '%s-%s', $username, $field ) . $data );
	
	//echo $data;
	$CI = & get_instance();
	
	$filetype = 'jpg';
	$filename = $filename . '.' . $filetype;
	$filepath = $CI->config->item( 'img_upload_path' );
	$file_fullpath = $filepath . $filename;
	
	if (! file_exists( $file_fullpath )) {
		if (! write_file( $file_fullpath, $data )) {
			return '';
		}
	}
	
	return base_url() . $CI->config->item( 'img_upload_path_in_url' ) . $filename;

}
/**
 * undocumented function
 *
 * @return void
 * @author apple
 **/
function getimagetype($file) {
	$_type = getimagesize( $file );
	$_type_value = $_type[2];
	$filetype = '';
	switch ($_type_value) {
		case 'IMAGETYPE_GIF' :
			$filetype = 'gif';
		break;
		case 'IMAGETYPE_JPEG' :
			$filetype = 'jpeg';
		break;
		case 'IMAGETYPE_PNG' :
			$filetype = 'png';
		break;
		case 'IMAGETYPE_SWF' :
			$filetype = 'swf';
		break;
		case 'IMAGETYPE_PSD' :
			$filetype = 'psd';
		break;
		case 'IMAGETYPE_BMP' :
			$filetype = 'bmp';
		break;
		case 'IMAGETYPE_WBMP' :
			$filetype = 'wbmp';
		break;
		case 'IMAGETYPE_XBM' :
			$filetype = 'xbm';
		break;
		case 'IMAGETYPE_TIFF_II' :
			$filetype = 'tiff';
		break;
		case 'IMAGETYPE_TIFF_MM' :
			$filetype = 'tiff';
		break;
		case 'IMAGETYPE_IFF' :
			$filetype = 'iff';
		break;
		case 'IMAGETYPE_JB2' :
			$filetype = 'jb2';
		break;
		case 'IMAGETYPE_JPC' :
			$filetype = 'jpc';
		break;
		case 'IMAGETYPE_JP2' :
			$filetype = 'jp2';
		break;
		case 'IMAGETYPE_JPX' :
			$filetype = 'jpx';
		break;
		case 'IMAGETYPE_SWC' :
			$filetype = 'swc';
		break;
		default :
			$filetype = '';
		break;
	}

}
