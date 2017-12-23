<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'style';
$active_record = TRUE;

//$db['style']['hostname'] = 'rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com';
//$db['style']['hostname'] = '106.3.40.123';
$db['style']['hostname'] = 'rm-bp1ey7z71284719w9.mysql.rds.aliyuncs.com';
$db['style']['username'] = 'style';
$db['style']['password'] = 'mhx4khl@DbDb';
$db['style']['database'] = 'style_test'; // style // demo
$db['style']['dbdriver'] = 'mysqli';
$db['style']['dbprefix'] = '';
$db['style']['pconnect'] = TRUE;
$db['style']['db_debug'] = FALSE;
$db['style']['cache_on'] = FALSE;
$db['style']['cachedir'] = '';
$db['style']['char_set'] = 'utf8mb4';
$db['style']['dbcollat'] = 'utf8mb4_general_ci';
$db['style']['swap_pre'] = '';
$db['style']['autoinit'] = TRUE;
$db['style']['stricton'] = FALSE;
//如果有多个slave，可以作均衡，用数组表示

$db['style_slave']['hostname'] = array('rm-bp1ey7z71284719w9.mysql.rds.aliyuncs.com');
//$db['style_slave']['hostname'] = array('106.3.40.123');
// $db['style_slave']['hostname'] = array('rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com');
$db['style_slave']['username'] = 'style';
$db['style_slave']['password'] = 'mhx4khl@DbDb';
$db['style_slave']['database'] = 'style_test'; // style // demo
$db['style_slave']['dbdriver'] = 'mysqli';
$db['style_slave']['dbprefix'] = '';
$db['style_slave']['pconnect'] = TRUE;
$db['style_slave']['db_debug'] = FALSE;
$db['style_slave']['cache_on'] = FALSE;
$db['style_slave']['cachedir'] = '';
$db['style_slave']['char_set'] = 'utf8mb4';
$db['style_slave']['dbcollat'] = 'utf8mb4_general_ci';
$db['style_slave']['swap_pre'] = '';
$db['style_slave']['autoinit'] = TRUE;
$db['style_slave']['stricton'] = FALSE;
//
//$db['cdb']['hostname'] = 'rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com';
////$db['cdb']['hostname'] = '106.3.40.123';
//$db['cdb']['username'] = 'style';
//$db['cdb']['password'] = 'mhx4khlDb';
//$db['cdb']['database'] = 'cdb';
//$db['cdb']['dbdriver'] = 'mysqli';
//$db['cdb']['dbprefix'] = '';
//$db['cdb']['pconnect'] = TRUE;
//$db['cdb']['db_debug'] = TRUE;
//$db['cdb']['cache_on'] = FALSE;
//$db['cdb']['cachedir'] = '';
//$db['cdb']['char_set'] = 'utf8';
//$db['cdb']['dbcollat'] = 'utf8_general_ci';
//$db['cdb']['swap_pre'] = '';
//$db['cdb']['autoinit'] = TRUE;
//$db['cdb']['stricton'] = FALSE;
//
//
//$db['cdb_slave']['hostname'] = array('rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com');
////$db['cdb_slave']['hostname'] = array('106.3.40.123');
////$db['cdb_slave']['hostname'] = array('rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com');
//$db['cdb_slave']['username'] = 'style';
//$db['cdb_slave']['password'] = 'mhx4khlDb';
//$db['cdb_slave']['database'] = 'cdb';
//$db['cdb_slave']['dbdriver'] = 'mysqli';
//$db['cdb_slave']['dbprefix'] = '';
//$db['cdb_slave']['pconnect'] = TRUE;
//$db['cdb_slave']['db_debug'] = TRUE;
//$db['cdb_slave']['cache_on'] = FALSE;
//$db['cdb_slave']['cachedir'] = '';
//$db['cdb_slave']['char_set'] = 'utf8';
//$db['cdb_slave']['dbcollat'] = 'utf8_general_ci';
//$db['cdb_slave']['swap_pre'] = '';
//$db['cdb_slave']['autoinit'] = TRUE;
//$db['cdb_slave']['stricton'] = FALSE;
//
////$db['sms']['hostname'] = '106.3.40.123';
//$db['sms']['hostname'] = 'rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com';
//$db['sms']['username'] = 'style';
//$db['sms']['password'] = 'mhx4khlDb';
//$db['sms']['database'] = 'style';
//$db['sms']['dbdriver'] = 'mysqli';
//$db['sms']['dbprefix'] = '';
//$db['sms']['pconnect'] = TRUE;
//$db['sms']['db_debug'] = FALSE;
//$db['sms']['cache_on'] = FALSE;
//$db['sms']['cachedir'] = '';
//$db['sms']['char_set'] = 'utf8';
//$db['sms']['dbcollat'] = 'utf8_general_ci';
//$db['sms']['swap_pre'] = '';
//$db['sms']['autoinit'] = TRUE;
//$db['sms']['stricton'] = FALSE;
//
//
////$db['file']['hostname'] = '106.3.40.123';
//$db['file']['hostname'] = 'rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com';
//$db['file']['username'] = 'style';
//$db['file']['password'] = 'mhx4khlDb';
//$db['file']['database'] = 'stylefiles';
//$db['file']['dbdriver'] = 'mysqli';
//$db['file']['dbprefix'] = '';
//$db['file']['pconnect'] = TRUE;
//$db['file']['db_debug'] = TRUE;
//$db['file']['cache_on'] = FALSE;
//$db['file']['cachedir'] = '';
//$db['file']['char_set'] = 'utf8';
//$db['file']['dbcollat'] = 'utf8_general_ci';
//$db['file']['swap_pre'] = '';
//$db['file']['autoinit'] = TRUE;
//$db['file']['stricton'] = FALSE;
//
//
////文件的同步请求
////$db['file_slave']['hostname'] = array('106.3.40.123','rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com');
////$db['file_slave']['hostname'] = array('106.3.40.123');
//$db['file_slave']['hostname'] = array('rds80956t2o7k843i0j2.mysql.rds.aliyuncs.com');
//$db['file_slave']['username'] = 'style';
//$db['file_slave']['password'] = 'mhx4khlDb';
//$db['file_slave']['database'] = 'stylefiles';
//$db['file_slave']['dbdriver'] = 'mysqli';
//$db['file_slave']['dbprefix'] = '';
//$db['file_slave']['pconnect'] = TRUE;
//$db['file_slave']['db_debug'] = TRUE;
//$db['file_slave']['cache_on'] = FALSE;
//$db['file_slave']['cachedir'] = '';
//$db['file_slave']['char_set'] = 'utf8';
//$db['file_slave']['dbcollat'] = 'utf8_general_ci';
//$db['file_slave']['swap_pre'] = '';
//$db['file_slave']['autoinit'] = TRUE;
//$db['file_slave']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */
