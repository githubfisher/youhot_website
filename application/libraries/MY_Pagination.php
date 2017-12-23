<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class  MY_Pagination extends CI_Pagination {

//修改几个默认的配置
	
	var $per_page			= 20; // Max number of items you want shown per page
	var $first_link			= '第一页';
	var $next_link			= '下一页';
	var $prev_link			= '上一页';
	var $last_link			= '最后一页';
	var $query_string_segment = 'pn';
	var $page_query_string	= TRUE;
	var $full_tag_open		= '<ul class="ipager clearfix">';
	var $full_tag_close		= '</ul>';
	var $first_tag_open		= '<li class="first">';
	var $first_tag_close	= '</li>';
	var $last_tag_open		= '<li class="last">';
	var $last_tag_close		= '</li>';
	var $first_url			= ''; // Alternative URL for the First Page.
	var $cur_tag_open		= '<li class="current">';
	var $cur_tag_close		= '</li>';
	var $next_tag_open		= '<li class="next">';
	var $next_tag_close		= '</li>';
	var $prev_tag_open		= '<li class="prev">';
	var $prev_tag_close		= '</li>';
	var $num_tag_open		= '<li>';
	var $num_tag_close		= '</li>';
}
// END Pagination Class

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */