<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "home";
$route['404_override'] = '';
$route['collection/(:num)'] = "collection/detail/$1";
$route['collection/list'] = "collection/published_list";
$route['product/list'] = "product/list_for_me";

$route['product/(:num)'] = "product/preview/$1";

$route['product/detail'] = "product/preview";
$route['product/like'] = "product/like/1";
$route['product/dislike'] = "product/like/0";

$route['category/list'] = "category/get_list";
$route['category/orderedlist'] = "category/get_list/ordered";
$route['order/list'] = "order/get_list";
$route['ship/(:num)'] = "ship/detail/$1";
$route['ship/list'] = "ship/get_list";
$route['coupon/(:num)'] = "coupon/detail/$1";
$route['coupon/list'] = "coupon/get_list";
//$route['admin'] = "admin/product/product_list";
$route['admin/product/list'] = "admin/product/product_list";

$route['admin/user/(:num)/(\w+)'] = "admin/user/$2/$1";

$route['admin/collection/(:num)/edit'] = "admin/collection/edit/$1";
$route['admin/order/(:num)/edit'] = "admin/order/edit/$1";
$route['admin/product/(:num)/preview'] = "admin/product/preview/$1";
$route['admin/product/(:num)/edit'] = "admin/product/edit/$1";
$route['admin/stat/order'] = "admin/order/stat";
$route['admin/stat/product'] = "admin/product/stat";
$route['admin/stat/collection'] = "admin/collection/stat";
$route['admin/stat'] = "admin/product/stat";

$route['user/applycert'] = "user/applicant/apply";
$route['search/hotspot'] = "home/search_hot_spot";

$route['about'] = "home/about";

$route['livecasts'] = "livecast/get_list";

$route['color/(\w+)'] = "admin/product/color/$1";

/* End of file routes.php */
/* Location: ./application/config/routes.php */