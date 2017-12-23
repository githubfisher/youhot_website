<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package        CodeIgniter
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2006 - 2011 EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Memcached Caching Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Core
 * @author        ExpressionEngine Dev Team
 * @link
 */
class Cache
{
    //public $on = true; //Cache switcher
    public $on = false; //Leon-

    private $_memcached;    // Holds the memcached object

    protected $_memcache_conf = array();

    protected $_default_options = array(
        'default_host' => '127.0.0.1',
        'default_port' => 11212,
        'default_weight' => 1
    );

    public function __construct()
    {
        return $this->_memcached || $this->is_supported();
    }
    // ------------------------------------------------------------------------

    /**
     * Fetch from cache
     *
     * @param    mixed        unique key id
     * @return    mixed        data on success/false on failure
     */
    public function get($id)
    {
        if (!$this->on) return false;
        $data = $this->_memcached->get($id);
        return (is_array($data) && count($data) > 0) ? $data[0] : FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Save
     *
     * @param    string        unique identifier
     * @param    mixed        data being cached
     * @param    int            time to live
     * @return    boolean    true on success, false on failure
     */
    public function save($id, $data, $ttl = 60)
    {
        if (!$this->on) return false;
        return $this->_memcached->add($id, array($data, time(), $ttl), $ttl);  //is set better?
    }

    public function replace($id, $data, $ttl = 60)
    {
        if (!$this->on) return false;
        return $this->_memcached->replace($id, array($data, time(), $ttl), $ttl);
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param    mixed        key to be deleted.
     * @return    boolean    true on success, false on failure
     */
    public function delete($id)
    {
        if (!$this->on) return false;
        return $this->_memcached->delete($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the Cache
     *
     * @return    boolean        false on failure/true on success
     */
    public function clean()
    {
        if (!$this->on) return false;
        return $this->_memcached->flush();
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * @param    null        type not supported in memcached
     * @return    mixed        array on success, false on failure
     */
    public function cache_info($type = NULL)
    {
        if (!$this->on) return false;
        return $this->_memcached->getStats();
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param    mixed        key to get cache metadata on
     * @return    mixed        FALSE on failure, array on success.
     */
    public function get_metadata($id)
    {
        if (!$this->on) return false;
        $stored = $this->_memcached->get($id);

        if (count($stored) !== 3) {
            return FALSE;
        }

        list($data, $time, $ttl) = $stored;

        return array(
            'expire' => $time + $ttl,
            'mtime' => $time,
            'data' => $data
        );
    }

    // ------------------------------------------------------------------------

    /**
     * Setup memcached.
     */
    private function _setup_memcached()
    {
        if ($this->_memcached) {
            return;
        }

        // Try to load memcached server info from the config file.
        $CI =& get_instance();
        if ($CI->config->load('memcached', TRUE, TRUE)) {
            if (is_array($CI->config->config['memcached'])) {
                $this->_memcache_conf = NULL;

                foreach ($CI->config->config['memcached'] as $name => $conf) {
                    $this->_memcache_conf[$name] = $conf;
                }
            }
        }

        $this->_memcached = new Memcached();

        foreach ($this->_memcache_conf as $name => $cache_server) {
            if (!array_key_exists('hostname', $cache_server)) {
                $cache_server['hostname'] = $this->_default_options['default_host'];
            }

            if (!array_key_exists('port', $cache_server)) {
                $cache_server['port'] = $this->_default_options['default_port'];
            }

            if (!array_key_exists('weight', $cache_server)) {
                $cache_server['weight'] = $this->_default_options['default_weight'];
            }

            $this->_memcached->addServer(
                $cache_server['hostname'], $cache_server['port'], $cache_server['weight']
            );
        }
	$this->on = true;

    }

    // ------------------------------------------------------------------------


    /**
     * Is supported
     *
     * Returns FALSE if memcached is not supported on the system.
     * If it is, we setup the memcached object & return TRUE
     */
    public function is_supported()
    {
        if (!extension_loaded('memcached')) {
            log_message('error', 'The Memcached Extension must be loaded to use Memcached Cache.');

            return FALSE;
        }

        $this->_setup_memcached();
        return TRUE;
    }

    // ------------------------------------------------------------------------


    /**
     * delete list
     *
     */
    public function delete_list($list_key)
    {
        if (!$this->on) return false;
        log_debug('cache list exist? ' . $list_key . ' : ' . json_encode($this->get($list_key)));
        if ($plist_keys = $this->get($list_key)) {
            log_debug('list cache deleted' . $list_key);
            $this->delete($list_key);   //clean key
            return $this->_memcached->deleteMulti($plist_keys);  //clean list key
        }
        log_debug($list_key . ' cache not exist');
        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * set cache and record keys list
     * @param $key
     * @param $value
     * @param $expire
     * @return mixed
     */
    public function add_list($list_key, $key, $value, $expire = 0)
    {
	//$mt = microtime(true); // debug
        if (!$this->on) return false;
        $plist = $this->get($list_key);
//        log_debug('product list key content:'.json_encode($plist));
        if ($plist !== false) {
            $plist[] = $key;
            $plist = array_unique($plist);
            log_debug('list key replaced!' . $list_key);
            //logger('list key replaced!' . $list_key);
            $this->replace($list_key, $plist, 0);  //never expire
	    //$mt = debug('Cache_addList_replace_pList_use:', $mt); // debug
        } else {
            log_debug('list key seted!' . $list_key);
            //logger('list key seted!' . $list_key);
            $save_res = $this->save($list_key, array($key), 0);  //never expire
            log_debug('cache list save res:' . $list_key . ':' . json_encode($save_res));
            //logger('cache list save res:' . $list_key . ':' . json_encode($save_res));
	    //$mt = debug('Cache_addList_save_pList_use:', $mt); // debug
        }
        log_debug('list key examine:' . $list_key . ' : ' . json_encode($this->get($list_key)));
        //logger('list key examine:' . $list_key . ' : ' . json_encode($this->get($list_key)));
        $res =  $this->save($key, $value, $expire);
	//$mt = debug('Cache_addList_save_value_use:', $mt); // debug

        return $res;
    }

}

// End Class

/* End of file Cache_memcached.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcached.php */
