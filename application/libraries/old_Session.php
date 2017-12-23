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
 * Session Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session {

	var $sess_encrypt_cookie		= FALSE;
	var $sess_use_database			= FALSE;
	var $sess_table_name			= '';
	var $sess_expiration			= 7200;
	var $sess_expire_on_close		= FALSE;
	var $sess_match_ip				= FALSE;
	var $sess_match_useragent		= TRUE;
	var $sess_cookie_name			= 'ci_session';
	var $cookie_prefix				= '';
	var $cookie_path				= '';
	var $cookie_domain				= '';
	var $cookie_secure				= FALSE;
    var $sess_time_to_update		= 300;
	var $sess_actime_span			= 300;
	var $sess_logtime_span			= 300;
	var $encryption_key				= '';
	var $flashdata_key				= 'flash';
	var $time_reference				= 'time';
	var $gc_probability				= 5;
	var $userdata					= array();
	var $CI;
	var $now;
	var $sess_from_url				= FALSE;

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */
	public function __construct($params = array())
	{


		log_message('debug', "my own Session Class Initialized");

		// Set the super object to a local variable for use throughout the class
		$this->CI =& get_instance();



		// Set all the session preferences, which can either be set
		// manually via the $params array above or via the config file
		foreach (array('sess_encrypt_cookie', 'sess_use_database', 'sess_table_name', 'sess_expiration', 'sess_expire_on_close', 'sess_match_ip', 'sess_match_useragent', 'sess_cookie_name', 'cookie_path', 'cookie_domain', 'cookie_secure', 'sess_actime_span','sess_logtime_span','time_reference', 'cookie_prefix', 'encryption_key') as $key)
		{
			$this->$key = (isset($params[$key])) ? $params[$key] : $this->CI->config->item($key);
		}

		if ($this->encryption_key == '')
		{
			show_error('In order to use the Session class you are required to set an encryption key in your config file.');
		}

		// Load the string helper so we can use the strip_slashes() function
		$this->CI->load->helper('string');

		$this->CI->load->helper('date');

		// Do we need encryption? If so, load the encryption class
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$this->CI->load->library('encrypt');
		}

		// Are we using a database?  If so, load it
		if ($this->sess_use_database === TRUE AND $this->sess_table_name != '')
		{
			if(!isset($this->CI->db_master)){
				$this->CI->db_master = $this->CI->load->database('style',TRUE);
			}
			if(!isset($this->CI->db_slave)){
//				$this->CI->db_slave = $this->CI->load->database('style_slave',TRUE);
				$this->CI->db_slave = $this->CI->db_master;
			}
		}

		// Set the "now" time.  Can either be GMT or server time, based on the
		// config prefs.  We use this to set the "last activity" time。_get_time改为了datetime格式。
		$this->now = $this->_get_time();

		// Set the session length. If the session expiration is
		// set to zero we'll set the expiration two years from now.
		if ($this->sess_expiration == 0)
		{
			$this->sess_expiration = (60*60*24*365*2);
		}

		// Set the cookie name
		$this->sess_cookie_name = $this->cookie_prefix.$this->sess_cookie_name;

		// Run the Session routine. If a session doesn't exist we'll
		// create a new one.  If it does, we'll update it.
		if ( ! $this->sess_read())
		{
			$this->sess_create();
			log_message('debug', "Session create");
		}
		else
		{
			$this->sess_update();
			log_message('debug', "Session update");
		}

		// Delete 'old' flashdata (from last request)
		$this->_flashdata_sweep();

		// Mark all new flashdata as old (data will be deleted before next request)
		$this->_flashdata_mark();


		log_message('debug', "Session routines successfully run");
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	bool
	 */
	function sess_read()
	{
		// Fetch the cookie or param in url;
		//url参数sid优先级高于cookie
		$session_id = FALSE;

		if($this->CI->input->cookie($this->sess_cookie_name))
		{
			$session_id = $this->CI->input->cookie($this->sess_cookie_name);
		}
		if($this->CI->input->get_post('sid'))
		{
			$session_id = $this->CI->input->get_post('sid');
			$this->sess_from_url = TRUE;
		}
		//echo $session_id;
		// No cookie or param?  Goodbye cruel world!...
		if ($session_id === FALSE)
		{
			log_message('debug', 'A session cookie or param was not found.');
			return FALSE;
		}


		/*通过cookie判断session内容的这一段弃用
		// Decrypt the cookie data
		if ($this->sess_encrypt_cookie == TRUE)
		{
			$session = $this->CI->encrypt->decode($session);
		}
		else
		{
			// encryption was not used, so we need to check the md5 hash
			$hash	 = substr($session, strlen($session)-32); // get last 32 chars
			$session = substr($session, 0, strlen($session)-32);

			// Does the md5 hash match?  This is to prevent manipulation of session data in userspace
			if ($hash !==  md5($session.$this->encryption_key))
			{
				log_message('error', 'The session cookie data did not match what was expected. This could be a possible hacking attempt.');
				$this->sess_destroy();
				return FALSE;
			}
		}

		// Unserialize the session array
		$session = $this->_unserialize($session);

		// Is the session data we unserialized an array with the correct format?
		if ( ! is_array($session) OR ! isset($session['session_id']) OR ! isset($session['ip_address']) OR ! isset($session['user_agent']) OR ! isset($session['last_activity']))
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Is the session current?
		if (($session['last_activity'] + $this->sess_expiration) < $this->now)
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address())
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Does the User Agent Match?
		if ($this->sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 50)))
		{
			$this->sess_destroy();
			return FALSE;
		}
		*/
		// Is there a corresponding session in the DB?
		if ($this->sess_use_database === TRUE)
		{
			$this->CI->db_slave->where('session_id', $session_id);


			if ($this->sess_match_ip == TRUE)
			{
				$this->CI->db_slave->where('ip1', $this->CI->input->ip_address());
			}

			if ($this->sess_match_useragent == TRUE)
			{
				$this->CI->db_slave->where('user_agent', trim(substr($this->CI->input->user_agent(), 0, 50)));
			}


			$query = $this->CI->db_slave->get($this->sess_table_name);
			// No result?  Kill it!

			if ($query->num_rows() == 0)
			{
				$this->sess_destroy();
				return FALSE;
			}



			//设置session内容
			$session = array();

			$row = $query->row();

			foreach ($row as $key=>$val)
			{
				$session[$key] = $val;
			}

			//检查登录session是否过期
				$_log_span = time() - human_to_unix($session['login_time']);
				$_act_span = time() - human_to_unix($session['last_activity']);


				//如果登录，检查login过期时间，regtime和lasttime分别设置一个过期时间，如果过期，需要重新登录）

				// 判断last_activity和login_time到现在时间,如果任何一方超过过期时间，重新登录（用户登录&&logtime>span,未登录logtime为0)
				if($session['status']==1){ //登录用户
					if ( ($_log_span > $this->sess_actime_span) or ($_act_span > $this->sess_actime_span)  )
					{
						//分配新的session
						$this->sess_destroy();
						log_message('debug','登录用户超时');
						return FALSE;

					}
				}



			// Is there custom data?  If so, add it to the main session array

			if (isset($row->user_data) AND $row->user_data != '')
			{
				$custom_data = $this->_unserialize($row->user_data);

				if (is_array($custom_data))
				{
					foreach ($custom_data as $key => $val)
					{
						$session[$key] = $val;
					}
				}
			}
		}

		// Session is valid!
		$this->userdata = $session;

		unset($session);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Write the session data
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{



		// set the custom userdata, the session data we will set in a second
		$custom_userdata = $this->userdata;
		$_update_data = array();
		//'session_id','username','status','sess_create_time','login_time','logout_time','last_activity','activity_times','pass_wrong_times','view_tea_record','ip1','ip2','ip3','user_agent'

		// Before continuing, we need to determine if there is any custom data to deal with.
		// Let's determine this by removing the default indexes to see if there's anything left in the array
		// and set the session data while we're at it
		// 临时措施，避免session锁定，如果只更新last activity，则不执行update
		//$need_update_to_db = false;
		foreach (array('session_id','userid','username','usertype','status','sess_create_time','login_time','logout_time','last_activity','activity_times','pass_wrong_times','view_tea_record','ip_address','ip2','ip3','user_agent') as $val)
		{
			unset($custom_userdata[$val]);
			/*
			if ($val != 'last_activity' && $val != 'user_agent' && array_key_exists($val, $this->userdata) && strlen($this->userdata[$val]) > 0) {
			 	$need_update_to_db = true;
			}
			 */
			$_update_data[$val] = array_key_exists($val, $this->userdata)?$this->userdata[$val]:'';
		}

		// Did we find any custom data?  If not, we turn the empty array into a string
		// since there's no reason to serialize and store an empty array in the DB
		if (count($custom_userdata) === 0)
		{
			$custom_userdata = '';
		}
		else
		{
			// Serialize the custom data array so we can store it
			$custom_userdata = $this->_serialize($custom_userdata);
		}
		$_update_data['user_data'] = $custom_userdata;

		// Run the update query
		//if ($need_update_to_db) {
			$this->CI->db_master->where('session_id', $this->userdata['session_id']);
			$this->CI->db_master->update($this->sess_table_name, $_update_data);
		//}
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		$sessid .= $this->CI->input->ip_address();
		//session_id','username','status','sess_create_time','login_time','logout_time','last_activity','activity_times','pass_wrong_times','view_tea_record','ip1','ip2','ip3','user_agent'
		$this->userdata = array(
							'session_id'	=> md5(uniqid($sessid, TRUE)),
							'ip1'			=> $this->CI->input->ip_address(),
							'user_agent'	=> substr($this->CI->input->user_agent(), 0, 50),
							'last_activity'	=> $this->now,
							'sess_create_time'	=> $this->now,
							'activity_times'	=> 1,
							'status'				=> '0',
							'username'			=> '',
							'usertype'			=>''
							);
		//update by conzi ,所有机器访问的情况不记录session
		if($this->userdata['ip1'] !== '0.0.0.0'){

			// Save the data to the DB if needed
			if ($this->sess_use_database === TRUE)
			{
				$this->CI->db_master->query($this->CI->db_master->insert_string($this->sess_table_name, $this->userdata));
			     //$this->CI->db_master->insert("user_trace",array("sid"=>$this->userdata['session_id'],"trace"=>(!empty($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:'')));
			}

		}
		// Write the cookie
		//mbo:cookie里面存的是session_id;
		$this->_set_cookie();
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{

        // We only update the session every five minutes by default
        if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now)
        {
            return;
        }

        // Save the old session id so we know which record to
        // update in the database if we need it
        $old_sessid = $this->userdata['session_id'];
        $new_sessid = '';
        while (strlen($new_sessid) < 32)
        {
            $new_sessid .= mt_rand(0, mt_getrandmax());
        }

        // To make the session ID even more secure we'll combine it with the user's IP
        $new_sessid .= $this->CI->input->ip_address();

        // Turn it into a hash
        $new_sessid = md5(uniqid($new_sessid, TRUE));

        // Update the session data in the session data array
        $this->userdata['session_id'] = $new_sessid;
        $this->userdata['last_activity'] = $this->now;

        // _set_cookie() will handle this for us if we aren't using database sessions
        // by pushing all userdata to the cookie.
        $cookie_data = NULL;

        // Update the session ID and last_activity field in the DB if needed
        if ($this->sess_use_database === TRUE)
        {
            // set cookie explicitly to only have our session data
            $cookie_data = array();
            foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
            {
                $cookie_data[$val] = $this->userdata[$val];
            }

            $this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid,'activity_times'=>$this->userdata['activity_times']+1), array('session_id' => $old_sessid)));
        }

        // Write the cookie
        $this->_set_cookie($cookie_data);
		/**
		 * 更新last_activity，activity_times,user_agent,检查ip1（如果ip1变了，检查ip2，如果ip2变了，检查ip3，发现不同存到空白处）


			//更新表

			$_update_data = array();
			$_update_data['last_activity'] = $this->now;
			$_update_data['user_agent']	= substr($this->CI->input->user_agent(), 0, 50);
			$_update_data['activity_times'] = $this->userdata['activity_times']+1;


			$_old_ips = array(
				'ip1' => $this->userdata['ip1'],
				'ip2' => $this->userdata['ip2'],
				'ip3' => $this->userdata['ip3']
			);
			$_c_ip = $this->CI->input->ip_address();

			$ip_key = array_search($_c_ip, $_old_ips);
			if (!$ip_key) //ip changed
			{
				if($_old_ips['ip2']== NULL)
				{
					$_update_data['ip2'] = $_c_ip;
				}
				else
				{
					$_update_data['ip3'] = $_c_ip;
				}
			}
			//update db
			$this->CI->db_master->where('session_id', $this->userdata['session_id'])->update($this->sess_table_name,$_update_data);

			if($this->CI->db_master->affected_rows()<1)
			{
				log_message('debug','update sess_table wrong during sess_update');
			}
			//记录访问路径
           // $this->CI->db_master->query('update user_trace set trace=concat(trace,?) where sid=?',array("->".$_SERVER["REQUEST_URI"],$this->userdata['session_id']));


			//如果从get请求过来的，设置cookie
			if($this->sess_from_url)
			{
				$this->_set_cookie();
			}
         *
         */

	}
/**
 * update session db from $this->userdata;
 */

	function update_sess_db()
	{
			$this->CI->db_master->where('session_id', $this->userdata['session_id']);
			$this->CI->db_master->update($this->sess_table_name, $this->userdata);
	}
	// --------------------------------------------------------------------

	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		// Kill the session DB row

		if ($this->sess_use_database === TRUE AND isset($this->userdata['session_id']))
		{
			$this->CI->db_master->where('session_id', $this->userdata['session_id']);
			$this->CI->db_master->update($this->sess_table_name,array('status'=>0));
		}


		// Kill the cookie
		setcookie(
					$this->sess_cookie_name,
					addslashes(serialize(array())),
					(now() - 31500000),
					$this->cookie_path,
					$this->cookie_domain,
					0
				);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a specific item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function userdata($item)
	{
		//echo $item;
		//print_r($this->userdata);
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	mixed
	 */
	function all_userdata()
	{
		return ( ! isset($this->userdata)) ? FALSE : $this->userdata;
	}

	// --------------------------------------------------------------------

	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_userdata($newdata = array(), $newval = '')
	{
		log_message("debug","hi:".$newval);
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}

		$this->sess_write();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}

		$this->sess_write();
	}

	// ------------------------------------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Add or change flashdata, only available
	 * until the next request
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_flashdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$flashdata_key = $this->flashdata_key.':new:'.$key;
				$this->set_userdata($flashdata_key, $val);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Keeps existing flashdata available to next request.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function keep_flashdata($key)
	{
		// 'old' flashdata gets removed.  Here we mark all
		// flashdata as 'new' to preserve it from _flashdata_sweep()
		// Note the function will return FALSE if the $key
		// provided cannot be found
		$old_flashdata_key = $this->flashdata_key.':old:'.$key;
		$value = $this->userdata($old_flashdata_key);

		$new_flashdata_key = $this->flashdata_key.':new:'.$key;
		$this->set_userdata($new_flashdata_key, $value);
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch a specific flashdata item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function flashdata($key)
	{
		$flashdata_key = $this->flashdata_key.':old:'.$key;
		return $this->userdata($flashdata_key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Identifies flashdata as 'old' for removal
	 * when _flashdata_sweep() runs.
	 *
	 * @access	private
	 * @return	void
	 */
	function _flashdata_mark()
	{
		$userdata = $this->all_userdata();
		foreach ($userdata as $name => $value)
		{
			$parts = explode(':new:', $name);
			if (is_array($parts) && count($parts) === 2)
			{
				$new_name = $this->flashdata_key.':old:'.$parts[1];
				$this->set_userdata($new_name, $value);
				$this->unset_userdata($name);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Removes all flashdata marked as 'old'
	 *
	 * @access	private
	 * @return	void
	 */

	function _flashdata_sweep()
	{
		$userdata = $this->all_userdata();
		foreach ($userdata as $key => $value)
		{
			if (strpos($key, ':old:'))
			{
				$this->unset_userdata($key);
			}
		}

	}
	// --------------------------------------------------------------------

	/**
	 * Get the "now" time
	 *
	 * @access	private
	 * @return	datetime type: 2011-09-15 12:23:03
	 */
	function _get_time()
	{
		$time = standard_date('DATE_MYSQL',time());
		return $time;

	}

	// --------------------------------------------------------------------

	/**
	 * Write the session cookie
	 *
	 * @access	public
	 * @return	void
	 */
	function _set_cookie($cookie_data = NULL)
	{
		//here,cookie_data is useless because what I  stored  into cookie is not session_data(cookie_data) but session_id
		//author mabo
		if (is_null($cookie_data))
		{
			$cookie_data = $this->userdata;
		}

		// Serialize the userdata for the cookie
		$cookie_data = $this->_serialize($cookie_data);

		if ($this->sess_encrypt_cookie == TRUE)
		{
			$cookie_data = $this->CI->encrypt->encode($cookie_data);
		}
		else
		{
			// if encryption is not used, we provide an md5 hash to prevent userside tampering
			$cookie_data = $cookie_data.md5($cookie_data.$this->encryption_key);
		}

		$expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

		// Set the cookie
		setcookie(
					$this->sess_cookie_name,
					$this->userdata['session_id'],
					$expire,
					$this->cookie_path,
					$this->cookie_domain,
					$this->cookie_secure
				);
	}

	// --------------------------------------------------------------------

	/**
	 * Serialize an array
	 *
	 * This function first converts any slashes found in the array to a temporary
	 * marker, so when it gets unserialized the slashes will be preserved
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	function _serialize($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else
		{
			if (is_string($data))
			{
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}

		return serialize($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Unserialize
	 *
	 * This function unserializes a data string, then converts any
	 * temporary slash markers back to actual slashes
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	function _unserialize($data)
	{
		$data = @unserialize(strip_slashes($data));

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}

			return $data;
		}

		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}

	public function need_login()
	{
		if($this->userdata['status']!=1)
		{
			redirect('/');
		}
	}
	// --------------------------------------------------------------------


}
// END Session Class

/* End of file Session.php */
/* Location: ./system/libraries/Session.php */
