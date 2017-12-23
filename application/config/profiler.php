<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Profiler Sections
| -------------------------------------------------------------------------
| This file lets you determine whether or not various sections of Profiler
| data are displayed when the Profiler is enabled.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/profiling.html
|
*/

$config['config']          = FALSE;
//$config['queries']         = FALSE;
//$config['benchmarks']          = FALSE;
$config['http_headers']         = FALSE;

/*
|--------------------------------------------------------------------------
| Profiler output type
|--------------------------------------------------------------------------
|
| If your PHP want to display profiler result on page you can set to true
| false record into db
|
*/
//$config['profiler_output_html'] = true;
$config['profiler_output_html'] = false;


/* End of file profiler.php */
/* Location: ./application/config/profiler.php */