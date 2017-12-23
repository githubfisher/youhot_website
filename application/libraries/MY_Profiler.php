<?php

class MY_Profiler extends CI_Profiler
{
    protected $output_html;

    public function __construct($config = array())
    {
        parent::__construct($config);
//		var_dump(get_class_methods('CI_Profiler'));
        $this->output_html = $config['profiler_output_html'];
    }

    /**
     * Compile Queries
     *
     * @return    string
     */
    protected function _compile_queries()
    {
        $dbs = array();


//var_dump(get_object_vars($this->CI));
        // Let's determine which databases are currently connected to
        foreach (get_object_vars($this->CI) as $key => $CI_object) {

            if (is_object($CI_object) && is_subclass_of(get_class($CI_object), 'CI_DB')) {
                if (in_array($CI_object, $dbs)) {
//					echo 'dup:'.$CI_object->name;
                    continue;
                }
                $CI_object->name = $key;
                $dbs[] = $CI_object;

            }

//			//added http://www.gotphp.com/codeigniter-multiple-database-support/5468/
//			if (is_object($CI_object) && is_subclass_of(get_class($CI_object), $this->CI->config->config['subclass_prefix'] . 'Model') )
//			{
//				 foreach (get_object_vars($CI_object) as $CI_sub_object)
//				 {
//					 if (is_object($CI_sub_object) && is_subclass_of(get_class($CI_sub_object), 'CI_DB') )
//					 {
//					 	$dbs[] = $CI_sub_object;
//					 }
//				 }
//			}
        }

$data = array();
        if (count($dbs) == 0) {

            $output = "\n\n";
            $output .= '<fieldset id="ci_profiler_queries" style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
            $output .= "\n";
            $output .= '<legend style="color:#0000FF;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_queries') . '&nbsp;&nbsp;</legend>';
            $output .= "\n";
            $output .= "\n\n<table style='border:none; width:100%'>\n";
            $output .= "<tr><td style='width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px'>" . $this->CI->lang->line('profiler_no_db') . "</td></tr>\n";
            $output .= "</table>\n";
            $output .= "</fieldset>";


            return $output;
        }

        // Load the text helper so we can highlight the SQL
        $this->CI->load->helper('text');

        // Key words we want bolded
        $highlight = array('SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'OR&nbsp;', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', '(', ')');

        $output = "\n\n";

        foreach ($dbs as $db) {
            $output .= '<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
            $output .= "\n";
            $output .= '<legend style="color:#0000FF;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_database') . ':&nbsp; ' . $db->name . '->' . $db->database . '&nbsp;&nbsp;&nbsp;' . $this->CI->lang->line('profiler_queries') . ': ' . count($db->queries) . '&nbsp;&nbsp;&nbsp;</legend>';
            $output .= "\n";
            $output .= "\n\n<table style='width:100%;'>\n";

            if (count($db->queries) == 0) {
                $output .= "<tr><td style='width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;'>" . $this->CI->lang->line('profiler_no_queries') . "</td></tr>\n";
            } else {
                foreach ($db->queries as $key => $val) {
                    $time = number_format($db->query_times[$key], 4);
                    $data[$time] = $val;
                    $val = highlight_code($val, ENT_QUOTES);

                    foreach ($highlight as $bold) {
                        $val = str_replace($bold, '<strong>' . $bold . '</strong>', $val);
                    }

                    $output .= "<tr><td style='padding:5px; vertical-align: top;width:1%;color:#900;font-weight:normal;background-color:#ddd;'>" . $time . "&nbsp;&nbsp;</td><td style='padding:5px; color:#000;font-weight:normal;background-color:#ddd;'>" . $val . "</td></tr>\n";

                }
            }

            $output .= "</table>\n";
            $output .= "</fieldset>";

        }

        if (!$this->output_html) {
            return array('queries' => json_encode($data));
        }
        return $output;
    }


// --------------------------------------------------------------------
    /**
     * Compile memory usage
     *
     * Display total used memory
     *
     * @return    string
     */
    protected function _compile_memory_usage()
    {
        $data = array();
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_memory_usage" style="border:1px solid #5a0099;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#5a0099;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_memory_usage') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";
        if (function_exists('memory_get_usage') && ($usage = memory_get_usage()) != '') {
            $output .= "<div style='color:#5a0099;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_memory_usage') . ':' . number_format($usage / 1024) . ' KB</div>';
            $data['memu'] = floor($usage / 1024);
        } else {
            $output .= "<div style='color:#5a0099;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_no_memory_usage') . "</div>";
            $data['memu'] = 0;
        }
        if (function_exists('memory_get_peak_usage') && ($usage = memory_get_peak_usage()) != '') {
            $output .= "<div style='color:#5a0099;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_memory_peak_usage') . ':' . number_format($usage / 1024) . ' KB</div>';
            $data['mpu'] = floor($usage / 1024);
        } else {
            $output .= "<div style='color:#5a0099;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_no_memory_usage') . "</div>";
            $data['mpu'] = 0;
        }
        $output .= "<div style='color:#5a0099;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_memory_limit') . ':' . ini_get('memory_limit') . ' </div>';


        $output .= "</fieldset>";
        if (!$this->output_html) {
            return $data;
        }
        return $output;

    }
    // --------------------------------------------------------------------

    // --------------------------------------------------------------------

    /**
     * Auto Profiler
     *
     * This function cycles through the entire array of mark points and
     * matches any two points that are named identically (ending in "_start"
     * and "_end" respectively).  It then compiles the execution times for
     * all points and returns it as an array
     *
     * @return    array
     */
    protected function _compile_benchmarks()
    {
        $profile = array();
        foreach ($this->CI->benchmark->marker as $key => $val) {
            // We match the "end" marker so that the list ends
            // up in the order that it was defined
            if (preg_match("/(.+?)_end/i", $key, $match)) {
                if (isset($this->CI->benchmark->marker[$match[1] . '_end']) AND isset($this->CI->benchmark->marker[$match[1] . '_start'])) {
                    $profile[$match[1]] = $this->CI->benchmark->elapsed_time($match[1] . '_start', $key);
                }
            }
        }

        // Build a table containing the profile data.
        // Note: At some point we should turn this into a template that can
        // be modified.  We also might want to make this data available to be logged
        $data = array();
        $data['benchmark'] = json_encode($profile);
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_benchmarks" style="border:1px solid #900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#900;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_benchmarks') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";
        $output .= "\n\n<table style='width:100%'>\n";

        foreach ($profile as $key => $val) {
            if ($key == 'total_execution_time') {
                $data['total_et'] = floor($val*1000);
            }
            if(preg_match("/(controller_execution_time){1}(.*)/i",$key,$match)){
                $data['controller_et'] = floor($val*1000);
            }
            $key = ucwords(str_replace(array('_', '-'), ' ', $key));
            $output .= "<tr><td style='padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;'>" . $key . "&nbsp;&nbsp;</td><td style='padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;'>" . $val . "</td></tr>\n";
        }

        $output .= "</table>\n";
        $output .= "</fieldset>";

        if (!$this->output_html) {
            return $data;
        }
        return $output;
    }
// --------------------------------------------------------------------

    /**
     * Compile $_GET Data
     *
     * @return    string
     */
    protected function _compile_get()
    {
        $data = array();
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_get" style="border:1px solid #cd6e00;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#cd6e00;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_get_data') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";

        if (count($_GET) == 0) {
            $output .= "<div style='color:#cd6e00;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_no_get') . "</div>";
        } else {
            $output .= "\n\n<table style='width:100%; border:none'>\n";

            foreach ($_GET as $key => $val) {
                if (!is_numeric($key)) {
                    $key = "'" . $key . "'";
                }
                $data[$key] = $val;

                $output .= "<tr><td style='width:50%;color:#000;background-color:#ddd;padding:5px'>&#36;_GET[" . $key . "]&nbsp;&nbsp; </td><td style='width:50%;padding:5px;color:#cd6e00;font-weight:normal;background-color:#ddd;'>";
                if (is_array($val)) {
                    $output .= "<pre>" . htmlspecialchars(stripslashes(print_r($val, true))) . "</pre>";
                } else {
                    $output .= htmlspecialchars(stripslashes($val));
                }
                $output .= "</td></tr>\n";
            }

            $output .= "</table>\n";
        }
        $output .= "</fieldset>";
        $data =array('get'=> json_encode($data) );
        if (!$this->output_html) {
            return $data;
        }
        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Compile $_POST Data
     *
     * @return    string
     */
    protected function _compile_post()
    {
        $data = array();
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_post" style="border:1px solid #009900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#009900;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_post_data') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";

        if (count($_POST) == 0) {
            $output .= "<div style='color:#009900;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_no_post') . "</div>";
        } else {
            $output .= "\n\n<table style='width:100%'>\n";

            foreach ($_POST as $key => $val) {
                if (!is_numeric($key)) {
                    $key = "'" . $key . "'";
                }
                $data[$key] = $val;
                $output .= "<tr><td style='width:50%;padding:5px;color:#000;background-color:#ddd;'>&#36;_POST[" . $key . "]&nbsp;&nbsp; </td><td style='width:50%;padding:5px;color:#009900;font-weight:normal;background-color:#ddd;'>";
                if (is_array($val)) {
                    $output .= "<pre>" . htmlspecialchars(stripslashes(print_r($val, TRUE))) . "</pre>";
                } else {
                    $output .= htmlspecialchars(stripslashes($val));
                }
                $output .= "</td></tr>\n";
            }

            $output .= "</table>\n";
        }
        $output .= "</fieldset>";
        $data =array('post'=> json_encode($data) );
        if (!$this->output_html) {
            return $data;
        }
        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Show query string
     *
     * @return    string
     */
    protected function _compile_uri_string()
    {
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_uri_string" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#000;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_uri_string') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";

        $data = array();
        if ($this->CI->uri->uri_string == '') {
            $data['uri'] = '';
            $output .= "<div style='color:#000;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->lang->line('profiler_no_uri') . "</div>";
        } else {
            $data['uri'] = $this->CI->uri->uri_string;
            $output .= "<div style='color:#000;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->uri->uri_string . "</div>";
        }

        $output .= "</fieldset>";
        if (!$this->output_html) {
            return $data;
        }
        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Show the controller and function that were called
     *
     * @return    string
     */
    protected function _compile_controller_info()
    {
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_controller_info" style="border:1px solid #995300;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#995300;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_controller_info') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";

        $output .= "<div style='color:#995300;font-weight:normal;padding:4px 0 4px 0'>" . $this->CI->router->fetch_class() . "/" . $this->CI->router->fetch_method() . "</div>";

        $output .= "</fieldset>";
        if (!$this->output_html) {
            return array('class_method' => $this->CI->router->fetch_class() . "/" . $this->CI->router->fetch_method());
        }
        return $output;
    }
// --------------------------------------------------------------------

    /**
     * Compile header information
     *
     * Lists HTTP headers
     *
     * @return    string
     */
    protected function _compile_http_headers()
    {
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_http_headers" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#000;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_headers') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";

        $output .= "\n\n<table style='width:100%'>\n";

        foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD', ' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR') as $header) {
            $val = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';
            $output .= "<tr><td style='vertical-align: top;width:50%;padding:5px;color:#900;background-color:#ddd;'>" . $header . "&nbsp;&nbsp;</td><td style='width:50%;padding:5px;color:#000;background-color:#ddd;'>" . $val . "</td></tr>\n";
        }

        $output .= "</table>\n";
        $output .= "</fieldset>";
        if (!$this->output_html) {
            return array('http_header' => $output);
        }
        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Compile config information
     *
     * Lists developer config variables
     *
     * @return    string
     */
    protected function _compile_config()
    {
        $output = "\n\n";
        $output .= '<fieldset id="ci_profiler_config" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
        $output .= "\n";
        $output .= '<legend style="color:#000;">&nbsp;&nbsp;' . $this->CI->lang->line('profiler_config') . '&nbsp;&nbsp;</legend>';
        $output .= "\n";

        $output .= "\n\n<table style='width:100%'>\n";

        foreach ($this->CI->config->config as $config => $val) {
            if (is_array($val)) {
                $val = print_r($val, TRUE);
            }

            $output .= "<tr><td style='padding:5px; vertical-align: top;color:#900;background-color:#ddd;'>" . $config . "&nbsp;&nbsp;</td><td style='padding:5px; color:#000;background-color:#ddd;'>" . htmlspecialchars($val) . "</td></tr>\n";
        }

        $output .= "</table>\n";
        $output .= "</fieldset>";
        if (!$this->output_html) {
            return array('config' => $output);
        }
        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Run the Profiler
     *
     * @return    string
     */
    public function run()
    {
        $output = "<div id='codeigniter_profiler' style='clear:both;background-color:#fff;padding:10px;'>";
        $fields_displayed = 0;

        $data = array();
        foreach ($this->_available_sections as $section) {
            if ($this->_compile_{$section} !== FALSE) {
                $func = "_compile_{$section}";
                $re = $this->{$func}();
                if ($this->output_html) {
                    $output .= $re;
                } else {

                    $data = array_merge($data, $re);
                }

                $fields_displayed++;
            }
        }

        if ($fields_displayed == 0) {
            $output .= '<p style="border:1px solid #5a0099;padding:10px;margin:20px 0;background-color:#eee">' . $this->CI->lang->line('profiler_no_profiles') . '</p>';
        }

        $output .= '</div>';

        if (!$this->output_html) {
//            var_export($data);
            $filter = array('benchmark','controller_et','total_et','get','post','memu','mpu','uri','queries','class_method');
            $inert_data = filter_data($data,$filter);
            unset($data);
            $this->CI->db_master->insert(TBL_PROFILER,$inert_data);
            return null;
        }

        return $output;
    }

}

//end
