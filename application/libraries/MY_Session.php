<?php


class MY_Session extends CI_Session
{

    public function __construct($params = array())
    {
        log_message('debug', "Session Class Initialized");

        // Set the super object to a local variable for use throughout the class
        $this->CI =& get_instance();

        // Set all the session preferences, which can either be set
        // manually via the $params array above or via the config file
        foreach (array('sess_encrypt_cookie', 'sess_use_database', 'sess_table_name', 'sess_expiration', 'sess_expire_on_close', 'sess_match_ip', 'sess_match_useragent', 'sess_cookie_name', 'cookie_path', 'cookie_domain', 'cookie_secure', 'sess_time_to_update', 'time_reference', 'cookie_prefix', 'encryption_key', 'sess_activity_to_update') as $key) {
            $this->$key = (isset($params[$key])) ? $params[$key] : $this->CI->config->item($key);
        }

        if ($this->encryption_key == '') {
            show_error('In order to use the Session class you are required to set an encryption key in your config file.');
        }

        // Load the string helper so we can use the strip_slashes() function
        $this->CI->load->helper('string');

        // Do we need encryption? If so, load the encryption class
        if ($this->sess_encrypt_cookie == TRUE) {
            $this->CI->load->library('encrypt');
        }

        // Are we using a database?  If so, load it
        if ($this->sess_use_database === TRUE AND $this->sess_table_name != '') {
//			$this->CI->load->database('style');
            if (!isset($this->CI->db_master)) {
                $this->CI->db_master = $this->CI->load->database('style', TRUE);
            }
            if (!isset($this->CI->db_slave)) {
//				$this->CI->db_slave = $this->CI->load->database('style_slave',TRUE);
                $this->CI->db_slave = $this->CI->db_master;
            }
            $this->CI->db = $this->CI->db_master; //临时设置一下,不用修改所有session里面的->db调用.
        }

        // Set the "now" time.  Can either be GMT or server time, based on the
        // config prefs.  We use this to set the "last activity" time
        $this->now = $this->_get_time();
        // Set the session length. If the session expiration is
        // set to zero we'll set the expiration two years from now.
        if ($this->sess_expiration == 0) {
            $this->sess_expiration = (60 * 60 * 24 * 365 * 2);
        }

        // Set the cookie name
        $this->sess_cookie_name = $this->cookie_prefix . $this->sess_cookie_name;

        // Run the Session routine. If a session doesn't exist we'll
        // create a new one.  If it does, we'll update it.
        if (!$this->sess_read()) {
            $this->sess_create();
            log_message('debug', "Session create");
        } else {
            $this->sess_update();
            log_message('debug', "Session update");
        }

        // Delete 'old' flashdata (from last request)
        $this->_flashdata_sweep();

        // Mark all new flashdata as old (data will be deleted before next request)
        $this->_flashdata_mark();

        // Delete expired sessions if necessary
        $this->_sess_gc();

        log_message('debug', "Session routines successfully run");
    }

    // --------------------------------------------------------------------

    /**
     * Fetch the current session data if it exists
     *
     * @access    public
     * @return    bool
     */
    function sess_read()
    {
        log_debug('sess read');
        // Fetch the cookie
        $session = $this->CI->input->cookie($this->sess_cookie_name);

        // No cookie?  Goodbye cruel world!...
        if ($session === FALSE) {
            log_message('debug', 'A session cookie was not found.');
            return FALSE;
        }

        // Decrypt the cookie data
        if ($this->sess_encrypt_cookie == TRUE) {
            $session = $this->CI->encrypt->decode($session);
        } else {
            // encryption was not used, so we need to check the md5 hash
            $hash = substr($session, strlen($session) - 32); // get last 32 chars
            $session = substr($session, 0, strlen($session) - 32);

            // Does the md5 hash match?  This is to prevent manipulation of session data in userspace
            if ($hash !== md5($session . $this->encryption_key)) {
                log_debug('session read fail at hash' . json_encode($session));
                log_message('error', 'The session cookie data did not match what was expected. This could be a possible hacking attempt.');
                $this->sess_destroy();
                return FALSE;
            }
        }

        // Unserialize the session array
        $session = $this->_unserialize($session);

        // Is the session data we unserialized an array with the correct format?
        if (!is_array($session) OR !isset($session['session_id']) OR !isset($session['ip_address']) OR !isset($session['user_agent']) OR !isset($session['last_activity'])) {
            log_debug('session read fail at init' . json_encode($session));
            $this->sess_destroy();
            return FALSE;
        }

        // Is the session current?
        if (($session['last_activity'] + $this->sess_expiration) < $this->now) {
            log_debug('session read fail at last_activity' . json_encode($session));
            $this->sess_destroy();
            return FALSE;
        }

        // Does the IP Match?
        if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address()) {
            log_debug('session read fail at ip' . json_encode($session));
            $this->sess_destroy();
            return FALSE;
        }

        // Does the User Agent Match?
        if ($this->sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 50))) {
            log_debug('session read fail at user_agent' . json_encode($session));
            $this->sess_destroy();
            return FALSE;
        }

        // Is there a corresponding session in the DB?
        if ($this->sess_use_database === TRUE) {
            $this->CI->db->where('session_id', $session['session_id']);

            if ($this->sess_match_ip == TRUE) {
                $this->CI->db->where('ip_address', $session['ip_address']);
            }

            if ($this->sess_match_useragent == TRUE) {
                $this->CI->db->where('user_agent', $session['user_agent']);
            }

            $query = $this->CI->db->get($this->sess_table_name);

            // No result?  Kill it!
            if ($query->num_rows() == 0) {
                log_debug('sess ' . $session['session_id'] . ' did not find in db');
                $this->sess_destroy();
                return FALSE;
            }

            // Is there custom data?  If so, add it to the main session array
            $row = $query->row();
//			var_dump($row);
            if (isset($row->user_data) AND $row->user_data != '') {
                $custom_data = $this->_unserialize($row->user_data);

                if (is_array($custom_data)) {
                    foreach ($custom_data as $key => $val) {
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

    /**
     * Garbage collection
     *
     * This deletes expired session rows from database
     * if the probability percentage is met
     *
     * @access    public
     * @return    void
     */
    function _sess_gc()
    {
        if ($this->sess_use_database != TRUE) {
            return;
        }

        srand(time());
        if ((rand() % 100) < $this->gc_probability) {
            $expire = $this->now - $this->sess_expiration;

            $this->CI->db->where("last_activity < {$expire}");
            $query = $this->CI->db->get($this->sess_table_name);
            $gc_rows = get_row_array($query);
            $this->CI->db->where("last_activity < {$expire}");
            $this->CI->db->delete($this->sess_table_name);

            log_debug('Session garbage: removed sessions' . json_encode($gc_rows));
            log_message('debug', 'Session garbage collection performed.sess_expiration:' . $this->sess_expiration . ' expire:' . $expire . ' time:' . standard_date('DATE_MYSQL', $expire) . ' this->now:' . $this->now);
        }
    }

    /**
     * Destroy the current session
     *
     * @access    public
     * @return    void
     */
    function sess_destroy()
    {
        log_debug('sess destroy');
        // Kill the session DB row
        if ($this->sess_use_database === TRUE AND isset($this->userdata['session_id'])) {
            $this->CI->db->where('session_id', $this->userdata['session_id']);
            $this->CI->db->delete($this->sess_table_name);
        }

        // Kill the cookie
        setcookie(
            $this->sess_cookie_name,
            addslashes(serialize(array())),
            ($this->now - 31500000),
            $this->cookie_path,
            $this->cookie_domain,
            0
        );
    }

    /**
     * Update an existing session
     *
     * @access    public
     * @return    void
     */
    function sess_update()
    {
        log_debug('sess_update lc :' . $this->userdata['last_activity'] . ' time_to_update:' . $this->sess_time_to_update);
        // We only update the session every five minutes by default (According to sess_activity_to_update)
        if (($this->userdata['last_activity'] + $this->sess_activity_to_update) >= $this->now) {
            return;
        }

        // Save the old session id so we know which record to
        // update in the database if we need it
        $old_sessid = $this->userdata['session_id'];
        //last activity span > sess_activity_to_update , update last activity
        log_debug('session_id:'.$this->userdata['last_activity'].' + '.$this->sess_time_to_update.' vs '.$this->now);
        if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now) {
            log_debug('session_id: <<<<<');
            $this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now), array('session_id' => $old_sessid)));
            return;
        }
        log_debug('session_id: >>>>>>>');

        //Below is update session logic (sess can not live more than one day for sake of safety)
        $new_sessid = '';
        while (strlen($new_sessid) < 32) {
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
        if ($this->sess_use_database === TRUE) {
            // set cookie explicitly to only have our session data
            $cookie_data = array();
            foreach (array('session_id', 'ip_address', 'user_agent', 'last_activity') as $val) {
                $cookie_data[$val] = $this->userdata[$val];
            }

            $this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid)));


        }

        // Write the cookie
        $this->_set_cookie($cookie_data);
    }


}
// END Log Class

/* End of file MY_Log.php */
