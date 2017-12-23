<?php

/**
 * MY_Log ，扩展默认的Log库，增加将错误日志分流的功能
 *
 * @author        tconzi@gmail.com
 */
class MY_Log extends CI_Log
{

    /**
     * Write Log File
     *
     * Generally this function will be called using the global log_message() function
     *
     * @param    string    the error level
     * @param    string    the error message
     * @param    bool    whether the error is a native PHP error
     * @return    bool
     */

    public function write_log($level = 'error', $msg, $php_error = FALSE)
    {
        if ($this->_enabled === FALSE) {
            return FALSE;
        }

        $level = strtoupper($level);
        $prefix = "";
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $prefix = $_SERVER ['REQUEST_URI'] . " - ";
        }

        if (!isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold)) {
            return FALSE;
        }

        $log_fix = ((bool) defined('STDIN')) ? 'cli-' : '';
        //区分命令行日志和http日志

        $filepath = $this->_log_path . $log_fix . 'log-' . date('Y-m-d') . EXT;

//NOTE : Patch by conzi at 2012-03-08
//BEGIN patch
        if ($level == 'ERROR') {
            $filepath = $this->_log_path . $log_fix . 'error-' . date('Y-m-d') . EXT;

        }
//END patch


        $message = '';


        if (!file_exists($filepath)) {
            $message .= "<" . "?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?" . ">\n\n";
//            file_put_contents($filepath, $message);
//            chmod($filepath, FILE_WRITE_MODE);
        }

        if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return FALSE;
        }

        $message .= $level . ' ' . (($level == 'INFO') ? ' -' : '-') . ' ' . $prefix . date($this->_date_fmt) . ' --> ' . $msg . "\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);


        return TRUE;
    }

}
// END Log Class

/* End of file MY_Log.php */
