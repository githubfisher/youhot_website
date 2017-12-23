<?php
/*
 * echo json content
 * @param string $output
 * @return json
*/
function echo_json($output, $is_json = false)
{
    header('Content-Type: text/javascript; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
    if ($is_json) {
        echo $output;
    } else {
        echo json_encode($output);
    }
}

function echo_text($output)
{
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
    echo '<!DOCTYPE html>
        <html>
        <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        </head><body>
        ';
    echo $output;
    echo '</body></html';
}

/**
 *
 * 根据interface生成需要输出的json数组
 * @param array $interface
 * @return array
 */
function filter_data($data, $filter_rule = NULL, $permit_null = true)
{
    if ($filter_rule == NULL) {
        return $data;
    }
    $_res = array();
    foreach ($filter_rule as $key) {

        if (array_key_exists($key, $data)) {
            if(!$permit_null && $data[$key]==''){  //Don't use empty . It may be 0 sometimes;
                continue;
            }
            $_res [$key] = $data [$key];
        }
    }
    return $_res;

}

/*求出2个字符串的日期的想差秒数*/
function get_time_span($time1, $time2)
{
    return strtotime($time1) - strtotime($time2);
}

/*
 * 根据时间生成timerange
 * @param datetime $paras,$parame
 * @return true/false
 */
function time_range($params, $parame)
{
    $et = standard_date('DATE_HI', human_to_unix($parame));
    $st = standard_date('DATE_TIMERANGE_START', human_to_unix($params));
    return $st . '~' . $et;
}

/**
 *
 * 从timerange返回starttime，endtime
 * @param string $timerange
 * @param bool $unix
 */
function range_time($timerange, $unix = FALSE)
{
    //$timerange = '2011-10-17-18:00~20:00';
    //var_dump($timerange);
    $_t = explode('~', $timerange);
    $s = explode('-', $_t [0]);
    $y = $s [0];
    $m = $s [1];
    $d = $s [2];
    $shi = $s [3];
    $shie = explode(':', $shi);
    $sh = $shie [0];
    $si = $shie [1];
    $ehi = $_t [1];
    $ehie = explode(':', $ehi);
    $eh = $ehie [0];
    $ei = $ehie [1];

    $st = mktime($sh, $si, 0, $m, $d, $y);
    $et = mktime($eh, $ei, 0, $m, $d, $y);

    $r = array();

    if ($unix) {
        $r ['starttime'] = $st;
        $r ['endtime'] = $et;
        return $r;
    }

    $r ['starttime'] = standard_date('DATE_MYSQL', $st);
    $r ['endtime'] = standard_date('DATE_MYSQL', $et);
    return $r;

}

/**
 *
 * 从teacher/studen数组里面取得某user信息
 * @param arrar $dataarr
 * @param string $username
 * @param field $type
 * @return array or string
 */
function get_uinfo($dataarr, $username, $type = '')
{
    $teacher = array();
    if (!is_array($dataarr)) {
        return '';
    }
    foreach ($dataarr as $row) {
        if ($row ['username'] == $username) {
            $teacher = $row;
            break;
        }
    }
    if ($type == '') {
        return $teacher;
    } else {
        if (array_key_exists($type, $teacher)) {
            //echo $teacher[$type];
            return $teacher [$type];
        } else {
            return '';
            log_message('debug', 'did not find teacher[type]');
        }
    }

}

/**
 *
 * 48*7 二进制标识代码（从monday开始）
 * @param string $timerange $timerange = '2011-10-17-18:00~20:00';
 * @param string $origin_code
 * @param string $type default 'free'
 */
function weekcode_begin_monday($timerange, $origin_code = '', $type = 'free')
{

    $time = range_time($timerange, TRUE);
    //print_r($time);


    $today_clock0 = standard_date('DATE_YMD', $time ['starttime']) . ' 00:00:00';
    $today_time0 = human_to_unix($today_clock0);

    $start_index_in_today = floor(($time ['starttime'] - $today_time0) / BP_FREETIME_SECONDS_SPAN); //00---0
    $end_index_in_today = floor(($time ['endtime'] - $today_time0) / BP_FREETIME_SECONDS_SPAN); //29--0


    //echo $time['starttime'];
    $weekday = weekday($time ['starttime']);
    $today_index0 = ($weekday - 1) * BP_SPANS_IN_ONEDAY;
    $k = $timerange_index [0] = ($today_index0 + $start_index_in_today);
    $e = $timerange_index [1] = ($today_index0 + $end_index_in_today);

    $rawcode = code_setfree($timerange_index);

    if ($origin_code != '') {
        //echo "hh";
        if ($type == 'free') {
            return $rawcode | $origin_code;
        }
        if ($type == 'busy') {
            return (~$rawcode) & $origin_code;
        }

    }
    if ($type == 'free') {
        //	var_dump($rawcode);
        return $rawcode;
    }
    if ($type == 'busy') {
        return '';
    }

}

/**
 *
 *
 * 设置freetime标记
 * @param array $indexrange [3,6]
 * @return chars
 */
function code_setfree($indexrange)
{
    $chars = array_fill(0, 48, chr(0));//48位空闲位
    $k = $indexrange [0];
    $e = $indexrange [1];
    log_message('debug', $k . ',' . $e);
    for ($i = $k; $i <= $e; $i++) {
        $c = floor($i / 7);//第几个字符
        $o = $i % 7;//每个字符使用7位来表示第n个顺位是不是空闲

        $chars [$c] = $chars [$c] | chr((1 << (6 - $o)));//7位，chr第8位为符号位，不用
    }

    return implode('', $chars);
}


/**
 *  取得时间设置的数组，每一位0表示空闲，1表示已占用,共48*7位
 */
function get_timeset_array($code)
{
    $chars = str_split($code);
    $status = array();
    if ($code != "") {
        for ($i = 0; $i < BP_FREETIME_SPANS_IN_ONEWEEK; $i++) {
            $c = floor($i / 7);
            $o = $i % 7;
            $status[] = (ord($chars [$c]) >> (6 - $o)) & 1;
        }
    }

    return $status;
}


/**
 *
 * getfreeindex得出free的box index值
 * @param string $code
 * @return array  [['start'=>index,'end'=>index],[]]
 */
function code_getfree($code)
{

    $chars = str_split($code);

    $status = 0;
    $freeindex = 0;
    $free_boxes = array();
    if ($code != "") {

        for ($i = 0; $i < BP_FREETIME_SPANS_IN_ONEWEEK; $i++) {
            $c = floor($i / 7);
            $o = $i % 7;

            $v = (ord($chars [$c]) >> (6 - $o)) & 1;
            //			echo '<br>i:'.$i.' c:'.$c.' o:'.$o.' v:'.$v.' .st:'.$status;
            //			echo '<br>';
            if ($v == 1) {
                switch ($status) {
                    case 0 :
                        // start a free time range
                        $free_boxes [$freeindex] ['start'] = $i;
                        $status = 1;
                        //when code is 101, though status is 0, the last index must end itself;
                        if ($i == (BP_FREETIME_SPANS_IN_ONEWEEK - 1)) {
                            $free_boxes [$freeindex] ['end'] = $i; //end at the end
                        }
                        break;
                    case 1 :
                        // continuous free time range, so nothing to do
                        //when code is 111,  the last index must end the freetime;
                        if ($i == (BP_FREETIME_SPANS_IN_ONEWEEK - 1)) {
                            $free_boxes [$freeindex] ['end'] = $i; //end at the end
                        }
                        break;
                    default :
                        break;
                }
            } elseif ($v == 0) {
                switch ($status) {
                    case 0 :
                        // continous busy time range, so nothing to do
                        break;
                    case 1 :
                        // stop a free time range
                        $free_boxes [$freeindex] ['end'] = $i - 1; //结束值是上一个点
                        $status = 0;
                        $freeindex++;
                        break;
                    default :
                        break;
                }
            }

        }
    }
    return $free_boxes;

}


/**
 *
 * 根据索引值返回unix秒数（时间戳）
 * @param num $index
 * @param string $type start/end
 * @return unix时间戳
 */
function get_unix_fr_index($index, $type = 'start')
{
    $today_weekday = weekday(time());
    $today_ymd = standard_date('DATE_YMD', TIME());
    $t0 = human_to_unix($today_ymd . " 00:00:00"); //today 0 oclock
    $i0 = ($today_weekday - 1) * BP_SPANS_IN_ONEDAY;
    $myunix = (BP_FREETIME_SPANS_IN_ONEWEEK + $index - $i0) % BP_FREETIME_SPANS_IN_ONEWEEK * BP_FREETIME_SECONDS_SPAN + $t0;
    if ($type == 'end') {
        $myunix += 29 * 60 + 59;
    }
    return $myunix;
}

/**
 *
 * 根据datetime返回距离今天的索引值
 * @param datetime $datetime
 * @return num index
 */
function get_index_from_datetime($datetime)
{
    //echo $datetime.'<br>';
    $today_secs = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $obj_secs = human_to_unix($datetime);

    $span = $obj_secs - $today_secs;

    $index = floor($span / BP_FREETIME_SECONDS_SPAN);
    //echo $index.'<br>';
    return $index;
}

function get_freetime_fr_code($code)
{
    //echo "<pre>";
    $freeindex = code_getfree($code);
    //var_dump($freeindex);
    $free_time = array();
    $today_ymd = standard_date('DATE_YMD', TIME());
    $t0 = human_to_unix($today_ymd . " 00:00:00");
    foreach ($freeindex as $key => $value) {

        $free_time [$key] ['start'] = standard_date('DATE_MYSQL', get_unix_fr_index($value ['start']));
        $free_time [$key] ['end'] = standard_date('DATE_MYSQL', get_unix_fr_index($value ['end'], 'end'));
    }

    return $free_time;

}

/**
 * add string selected if the given 2 value match
 *
 * @access  public
 * @param      string
 * @param      string
 */
if (!function_exists('selected')) {
    function selected($value, $target)
    {
        if ($target == $value) {
            return ' class = "current"';
        }
    }
}

/**
 * get querey from data
 * @param array $data
 * @param string $tbl
 * @param string Type
 * @return query (?)
 */
if (!function_exists('get_query_from_array')) {
    function get_query_from_array($array, $tbl, $type = 'insert')
    {
        $query = '';
        if ($type == 'insert') {
            $query .= 'INSERT INTO ' . $tbl . '(' . implode(',', array_keys($array)) . ') VALUES (' . implode(',', array_fill(0, count($array), "?")) . ')';

        }

        return $query;
    }
}
/**
 * 显示老师状态
 * @param num $status
 * @return html
 */
if (!function_exists('display_status')) {
    function display_status($status)
    {
        $html = '';
        /*
         * 3表示正在辅导学生，即忙，不能打扰，（fp-status=1&&session里面有status=1的配对）2表示在线，可以随时发起辅导邀请，（fp-status=1）1表示在正在使用WEB服务，但是不可以发起辅导邀请，（bp在线，fp =0）0：表示不在线，也没有使用WEB服务（两边都是0）>
         */
        switch ($status) {
            case bp_teacher_status_busy :
                $html = '<span class="s-busy" title="老师正在辅导中"></span>';
                break;
            case bp_teacher_status_free :
                $html = '<span class="s-free" title="老师在线，你可以登录客户端免费试讲或正式试讲"></span>';
                break;
            case bp_teacher_status_online :
                $html = '<span class="s-online"></span>';
                break;
            /*
			case bp_teacher_self_status_invisible:
				$html = '<span class="s-invisible">隐身</span>';
				break;
			case bp_teacher_self_status_arranged_visable:
				$html = '<span class="s-arr-visible" title="试讲可见"></span>';
				break;
			case bp_teacher_self_status_pay_student_visable:
				$html = '<span class="s-pay-visible" title="正式学生可见"></span>';
				break;
			case bp_teacher_self_status_vip_visable:
				$html = '<span class="s-vip-visible" title="vip学生可见"></span>';
				break;
            */
            default :
                $html = '<span class="s-offline"></span>';
                break;
        }
        return $html;
    }
}
/**
 * 显示预约状态
 * @param num $status
 * @return html
 */
if (!function_exists('display_apt_status')) {
    function display_apt_status($status)
    {
        $html = '';

        switch ($status) {
            case APPOINT_CANCEL :
                $html = '<span class="as-cancel">已取消</span>';
                break;
            case APPOINT_REFUSE :
                $html = '<span class="as-refuse">已拒绝</span>';
                break;
            case APPOINT_CONFIRM :
                $html = '<span class="as-confirm">已确认</span>';
                break;
            default :
                $html = '<span class="as-waiting">待确认</span>';
                break;
        }
        return $html;
    }
}
/**
 * 显示交易类型
 * @param num $status
 * @return html
 */
if (!function_exists('display_deal_type')) {
    function display_deal_type($status)
    {
        $html = '';

        switch ($status) {
            case 0 :
                $html = '<span class="red strong">支出</span>';
                break;
            case 1 :
                $html = '<span class="green strong">收入</span>';
                break;
            case PAYTYPE_PAID:
                $html = '<span class="gray strong">已付</span>';
                break;
            default :
                $html = '<span class="unkown">未知</span>';
                break;
        }
        return $html;
    }
}
/**
 * 显示交易状态
 * @param num $status
 * @return html
 */
if (!function_exists('display_deal_status')) {
    function display_deal_status($status)
    {
        $html = '';

        switch ($status) {
            case DEAL_STATUS_UNCOMPLETED :
                $html = '<span class="red">未完成</span>';
                break;
            case DEAL_STATUS_COMPLETED :
                $html = '<span class="green">完成</span>';
                break;
            case DEAL_STATUS_TEAPAY_TO_CHECK:
                $html = '<span class="gray">预付</span>';
                break;
            case DEAL_STATUS_TEAPAY_IN_ADVANCE:
                $html = '<span class="gray">预付(不入账)</span>';
                break;
            default :
                $html = '<span class="unkown">未知</span>';
                break;
        }
        return $html;
    }
}

if (!function_exists('display_deal_replay_link')) {
    function display_deal_replay_link($deal_id)
    {
        $html = '<span>&nbsp;</span>';
        $id = get_session_id_from_dealid($deal_id);

        if (is_numeric($id)) {
            $html = replay_link($id, '回放');
        }


        return $html;
    }
}

function display_pay_from($from)
{

    $value = '其他';
    switch ($from) {
        case PAY_FROM_TENPAY:
            $value = '财付通';
            break;
        case PAY_FROM_ALI:
            $value = '支付宝';
            break;
        case PAY_FROM_99BILL:
            $value = '电话卡';
            break;
        case PAY_FROM_UNIONPAY:
            $value = '银联支付';
            break;
        case PAY_FROM_ALI_IN_CLIENT:
            $value = '客户端支付宝';
            break;
        case PAY_FROM_UPMP_IN_CLIENT:
            $value = 'upmp/client';
            break;
        case PAY_FROM_CHARGE_CARD:
        case PAY_FROM_BUYED_CHARGE_CARD:
        case PAY_FROM_ACTION_CHARGE_CARD:
        case PAY_FROM_BUY_CHARGE_CARD:
            $value = '充值卡';
            break;
        case PAY_FROM_DOUDOU_CHARGE:
            $value = '豆豆兑换';
            break;
        case PAY_FROM_VIP_CHARGE:
            $value = 'vip充值';
            break;
        case PAY_FROM_COOPERATION_DOUDOU_PAY:
            $value = '合作方购买豆豆';
            break;
        case PAY_FROM_ADMIN :
        case PAY_BACK_FROM_ADMIN :
        case PAY_FROM_ADMIN_FOR_USER_CHARGE :
        case PAY_FROM_ADMIN_FOR_USER_MONEY_RETURN :
        case PAY_FROM_ADMIN_FOR_TEACHER_MONEY_RETURN :
            $value = '管理员';
            break;
        case PAY_FROM_FUDAO:
            $value = '辅导';
            break;
        case PAY_FROM_FUDAO_COMMENT:
            $value = '辅导评价';
            break;
        case PAY_FROM_DOUDOU_TO_MONEY:
            $value = '豆豆转余额';
            break;
        case PAY_FROM_VIP_DAYI:
            $value = 'vip答疑';
            break;

        case PAY_FROM_CLASS :
            $value = '课堂';
            break;

        case PAY_FROM_MONTH_RETURN:
        case PAY_FROM_FREE_RETURN:
            $value = 'VIP时长';
            break;
        case PAY_FROM_DOUDOU_BUYING:
        case PAY_FROM_DOUDOU_CHARGING:
            $value = '买豆豆';
            break;
    }

    return $value;
}

function get_session_id_from_dealid($deal_id)
{
    $id = str_replace("stu_session", "", $deal_id);
    $id = str_replace("tea_session", "", $id);
    if (!is_numeric($id)) {
        $id = '';
    };
    return $id;
}

function get_payrange($rank)
{
    $CI = &get_instance();
    $CI->load->model('teacher_model', 'teacher');
    $res = $CI->teacher->get_rank($rank); //根据id获得rank信息
    //var_dump($res);
    return sprintf('%s-%s', $res ['lowpay'], $res ['highpay']);
}

/**
 * 返回/abc/s?ss=22
 */
function current_whole_url()
{
    return $_SERVER ['REQUEST_URI'];
}

function encoded_current_url()
{
    return urlencode(base64_encode($_SERVER ['REQUEST_URI']));
}


//    使用curl进行get请求
function curl_get($url, $params = false, $timeout = 100)
{
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    if (is_array($params)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
    } else {
        $url .= (strpos($url, '?') === false ? '?' : '&') . $params;
    }
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    if (substr($url, 0, 5) == 'https') {
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
    }
    $result = curl_exec($curlHandle);
    $errno = curl_errno($curlHandle);
    $error = curl_error($curlHandle);
    curl_close($curlHandle);

    if ($errno) {
        log_message('error', "curl error. url:$url, err:$error, errno:{$errno}, output:$result");
        return false;
    }

    return $result;
}

function func_curl($o_url, $topost_fpath = false, $is_getfile = false)
{
    global $g_mms_host;

    $url = $o_url;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $hh_post = false;
    if (false !== $topost_fpath) {
        $hh_post = fopen(FILEDB_PATH . $topost_fpath, "rb");
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_INFILE, $hh_post);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize(FILEDB_PATH . $topost_fpath));

        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, array('file1' => '@'.$topost_fpath) );
    }

    $output = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if (false !== $hh_post) {
        fclose($hh_post);
    }

    if ($error != "") {
        log_message('error', "curl error. url:$url, err:$error, output:$output");
        return FALSE;
    }

    if ($is_getfile == false && (false === $output || stristr($output, "xml") == FALSE)) {
        log_message('error', "curl failed. url:$url, err:$error, output:$output");
        return FALSE;
    }
    return $output;
}

function get_data($o_url, $topost_fpath = false, $is_getfile = false)
{

    global $g_mms_host;

    $url = $o_url;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $hh_post = false;
    if (false !== $topost_fpath) {
        $hh_post = fopen(FILEDB_PATH . $topost_fpath, "rb");
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_INFILE, $hh_post);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize(FILEDB_PATH . $topost_fpath));

        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, array('file1' => '@'.$topost_fpath) );
    }

    $output = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if (false !== $hh_post) {
        fclose($hh_post);
    }

    if ($error != "") {
        log_message('error', "curl error. url:$url, err:$error, output:$output");
        return FALSE;
    }

    if ($is_getfile == false && (false === $output)) {
        log_message('error', "curl failed. url:$url, err:$error, output:$output");
        return FALSE;
    }
    return $output;
}

function js_redirect($url)
{
    $str = sprintf('setTimeout(function(){ location.href="%s";},2000);', $url);
    return $str;
}

//curl post
function curl_post($url, $post_body = FALSE, $timeout = 300)
{
    // 1. 初始化
    $ch = curl_init();
    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    //curl post的时候很慢，网上查的解决方案http://blog.csdn.net/hzbigdog/article/details/10009043
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //强制使用IPV4协议解析域名

    if (($post_body !== FALSE) && ($post_body !== '')) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
    }

    // 3. 执行并获取HTML文档内容
    $output = curl_exec($ch);

    // 4. 释放curl句柄
    curl_close($ch);

    return $output;//文件内容或false
}

/*通过curl获取文件
 //curl post
*/
function get_file_via_curl($url, $post_body = FALSE, $timeout = 300)
{
    // 1. 初始化
    $ch = curl_init();
    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    if (($post_body !== FALSE) && ($post_body !== '')) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
    }

    // 3. 执行并获取HTML文档内容
    $output = curl_exec($ch);

    // 4. 释放curl句柄
    curl_close($ch);

    return $output;//文件内容或false
}

/*通过CURL获取图片文件，直接存储到文件中 */
function download_image_file($url, $fp, $timeout = 300)
{
    // 1. 初始化
    $ch = curl_init();

    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    // 3. 执行
    $r = curl_exec($ch);

    // 4. 释放curl句柄
    curl_close($ch);

    return $r;
}

/*
 * 16进制字符串转成2进制字符串，在week_r_code读取时用
 * @param string $str
 */
function hexstr2binstr($str)
{
    $len = strlen($str);
    $resolved_str = '';
    for ($i = 2; $i < $len; $i += 2) {
        $cur_char = '0x' . substr($str, $i, 2);
        $resolved_str .= chr($cur_char);

    }
    unset ($len);
    return $resolved_str;
}

/**
 * 处理返回结果码
 * @param array $set $this->_data
 * @param string $resname ***res
 * @param num $code db result code
 */
function process_return_code(&$set, $resname, $code)
{
    if ($code === DB_OPERATION_OK) {
        $set [$resname] = bp_operation_ok;

    } else {
        switch ($code) {
            case DB_OPERATION_FAIL :
            case DB_INTERNAL_ERROR :
                $set [$resname] = bp_operation_db_got_fail;
                $set [bp_appointment_hint_field] = '读取db失败';
                break;
            case DB_OPERATION_USER_FORBIDDEN :
                $set [$resname] = bp_operation_user_forbidden;
                $set [bp_appointment_hint_field] = '您没有权限';
                break;
            case DB_DATA_INVALID :
                $set [$resname] = bp_operation_user_forbidden;
                $set [bp_appointment_hint_field] = '查无结果';
                break;
            default :
                $set [$resname] = bp_operation_unknown_error;
                $set [bp_appointment_hint_field] = bp_operation_unknown_error_hint;
                break;
        }

    }

}



function getLiContent($free_flag, $ap_flag, $myap_flag)
{
    $ft_body = "";
    $format_string = '<li class="%s %s">%s</li>';
    $free_flag_value = ($free_flag == TRUE) ? 'free' : '';
    $ap_flag_value = ($ap_flag == TRUE) ? bp_appointment_flag_charactor : '';
    $myap_flag_value = ($myap_flag == TRUE) ? 'myap' : '';

    return sprintf($format_string, $free_flag_value, $ap_flag_value, $myap_flag_value);
}

/*
 * 把秒换算成小时
 * @param string/int $seconds
 * @return string
 */
function seconds_to_human($seconds)
{
    if (empty($seconds)) return '';

    $seconds = (int)$seconds;

    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;

    return ($h > 0 ? $h . '小时' : '') . ($m > 0 ? $m . '分' : '') . ($s >= 0 ? $s . '秒' : '');
}

/**
 *
 * 返回中文的周几
 * @param int $weeknumber (0-6)
 */
function chinese_weekday($weeknumber)
{
    $weeks = array('一', '二', '三', '四', '五', '六', '日');
    $weeknumber = ($weeknumber >= 0 && $weeknumber <= 6) ? $weeknumber : 0;

    return $weeks[$weeknumber];
}


/**
 *
 * 判断手机号是否合法
 * @param string $phone
 */
function check_phone($phone = '')
{
    if (!is_numeric($phone)) {
        return false;
    }
    if ($phone > 13000000000 and $phone < 18999999999) {
        return true;
    }
    return false;
}


function check_username($username = '')
{
    if (empty($username)) {
        return false;
    }

    return (!preg_match("/^([-a-z0-9_-]){2,32}$/i", $username)) ? FALSE : TRUE;
}


function gender_uuid_magic_key($did = '')
{
    $key = '';
    if (strlen($did) < 8) {
        return $key;
    }
    $step = (int)(strlen($did) / 8);
    for ($i = 0; $i < 8; $i++) {
        $str = substr($did, $i * $step, $step);
        if ($i % 4 === 0) {
            $c = max(str_split($str));
        }
        if ($i % 4 === 1) {
            $c = min(str_split($str));
        }
        if ($i % 4 === 2) {
            $c = substr($str, 0, 1);
        }
        if ($i % 4 === 3) {
            $c = substr($str, -1);
        }

        $key .= $c;
    }
    return $key;
}

// 检查uuid的正确性, $type=1
function check_uuid($uuid = '', &$did = '', &$type = BP_DEVICE_TYPE_IOS)
{
    if (!is_string($uuid)) {
        return false;
    }


    //至少16位
    if (preg_match("/^([a-z0-9,-]){16,128}$/i", $uuid)) {

        $len = strlen($uuid);

        if ($len === 40) {
            $did = $uuid;
            $type = BP_DEVICE_TYPE_IOS;
            return TRUE; //ipad or iphone
        } else {
            $rand_len = (int)substr($uuid, -1);
            $did = substr($uuid, 0, -($rand_len + 9));
            if (gender_uuid_magic_key($did) === substr($uuid, -9, -1)) {
                if ($rand_len < 5) {//暂时是小于5的为安卓
                    $type = BP_DEVICE_TYPE_ANDROID;//android;
                }
                if ($rand_len == 5) {
                    $type = BP_DEVICE_TYPE_MALATA;
                }
                // if($rand_len == 6){
                // 	$type = BP_DEVICE_TYPE_READBOY;
                // }
                if ($rand_len == 7) {
                    $type = BP_DEVICE_TYPE_NEW_MALATA;
                }

                if ($rand_len == 8 or $rand_len == 9) {
                    $type = BP_DEVICE_TYPE_IOS;
                }

                return TRUE;
            }
        }
    }

    return false;
}

// 取得设备限制
function get_device_limit($device_type, $limit_type, $user_belong = 'aifudao')
{
    $limit = array();
    $limit[BP_DEVICE_TYPE_IOS] = array(BP_DEVICE_LIMIT_TYPE_FIRST_LOGIN => BP_STUDENT_FIRST_LOGIN_DOUDOU, BP_DEVICE_LIMIT_TYPE_TOTAL => BP_STUDENT_DOUDOU_TOTAL_LIMIT, BP_DEVICE_LIMIT_TYPE_MONTHLY => BP_STUDENT_DOUDOU_MONTHLY_LIMIT,);
    // 读书郎和android一样
    $limit[BP_DEVICE_TYPE_KYD] = $limit[BP_DEVICE_TYPE_ANDROID] = array(BP_DEVICE_LIMIT_TYPE_FIRST_LOGIN => BP_STUDENT_ANDROID_FIRST_LOGIN_DOUDOU, BP_DEVICE_LIMIT_TYPE_TOTAL => BP_STUDENT_ANDROID_DOUDOU_TOTAL_LIMIT, BP_DEVICE_LIMIT_TYPE_MONTHLY => BP_STUDENT_DOUDOU_MONTHLY_LIMIT,);
    $limit[BP_DEVICE_TYPE_MALATA] = array(BP_DEVICE_LIMIT_TYPE_FIRST_LOGIN => BP_STUDENT_MALATA_FIRST_LOGIN_DOUDOU, BP_DEVICE_LIMIT_TYPE_TOTAL => BP_STUDENT_ANDROID_DOUDOU_TOTAL_LIMIT, BP_DEVICE_LIMIT_TYPE_MONTHLY => BP_STUDENT_DOUDOU_MONTHLY_LIMIT,);
    $limit[BP_DEVICE_TYPE_NEW_MALATA] = array(BP_DEVICE_LIMIT_TYPE_FIRST_LOGIN => ($user_belong == 'malata' ? 100 : BP_STUDENT_ANDROID_FIRST_LOGIN_DOUDOU), BP_DEVICE_LIMIT_TYPE_TOTAL => ($user_belong == 'malata' ? 10000000 : BP_STUDENT_ANDROID_DOUDOU_TOTAL_LIMIT), BP_DEVICE_LIMIT_TYPE_MONTHLY => ($user_belong == 'malata' ? 10000000 : BP_STUDENT_DOUDOU_MONTHLY_LIMIT),);

    if (array_key_exists($device_type, $limit) and array_key_exists($limit_type, $limit[$device_type])) {
        return $limit[$device_type][$limit_type];
    }

    return 0;
}


//获得服务器的ip
function real_server_ip()
{
    $ip = explode("\n", trim(`ip addr |grep -o  'inet [0-9.]*.*\(eth\|em\)'|grep -v '192.168.10'|grep -o '[0-9.]\{7,24\}'`));
    //update at 10-12 by mbo. Filter internal addr out.
    if (empty($ip)) {
        $ip = array("127.0.0.1");
    }
    return $ip[0];
}


function user_link($username, $realname = '')
{
    if (empty($realname)) {
        $realname = $username;
    }
    return '<a href="/r/user?username=' . urlencode($username) . '" target="_blank">' . $realname . '</a>';
}

function teacher_link($username, $realname = '')
{
    if (empty($realname)) {
        $realname = $username;
    }
    return '<a href="/teacher/info?username=' . urlencode($username) . '" target="_blank">' . $realname . '</a>';
}


function student_link($username, $realname = '')
{
    if (empty($realname) || $realname == '未设') {
        $realname = $username;
    }

    return '<a href="/student/info?username=' . urlencode($username) . '" target="_blank">' . htmlspecialchars($realname) . '</a>';
}

//课堂链接
function course_link($course_id, $title = '')
{
    if (empty($title)) {
        $title = $course_id;
    }
    return '<a href="/classes/info?id=' . $course_id . '" target="_blank">' . htmlspecialchars($title) . '</a>';
}

function get_replay_url($course_id)
{
    $url = base_url() . rp_url_replay . str_replace('=', '', base64_encode($course_id));
    return $url;
}

function get_replay_url_in_full_screen($course_id)
{
    $url = base_url() . rp_url_replay_in_full_screen . $course_id;
    return $url;
}

function get_snap_url($course_id)
{
    $url = 'http://www.aifudao.com/kejian?sid=' . $course_id . '&action=page';
    return $url;
}

function replay_link($course_id, $title = '')
{
    return '<a href="' . get_replay_url($course_id) . '" target="_blank">' . htmlspecialchars($title) . '</a>';
}

function face_url($username, $timestamp = 0)
{
    if ($timestamp == 0 || $timestamp == '0') {
        return base_url() . 'img/face/' . urlencode($username);
    } else {
        return base_url() . 'img/face/' . urlencode($username) . '.' . $timestamp;
    }
}

function fix_subject_and_grade(&$row)
{
    if ($row['grade_id'] == 0) {
        $row['grade'] = '不限年级';
    }
    if ($row['subject_id'] == 0) {
        $row['subject'] = '不限科目';
    }
}


function fix_where_in_array(&$arr = array(), $default = '')
{
    if (empty($arr)) {
        $arr[] = $default;
    }
    $arr = array_unique($arr);
}

function get_query_result(&$query)
{
    $res = array();
    if ($query && $query->num_rows() > 0) {
        $res = $query->result_array();
        $query->free_result();
    }
    return $res;
}

function get_row_array(&$query)
{
    $res = array();
    if ($query && $query->num_rows() > 0) {
        $res = $query->row_array();
        $query->free_result();
    }
    return $res;
}


//将计费从按小时转为按课时
function hourpay_to_classpay($pay, $unit = 45)
{
    return round($pay * $unit / 60, 2);
}


function order_by_match($word = '', $source = array())
{
    if (empty($word)) {
        return $source;
    }
    global $key;
    $key = $word;
    function cmd($a, $b)
    {
        global $key;
        $p1 = strpos($a, $key);
        $p2 = strpos($b, $key);
        if ($p1 === false) {
            $p1 = -1;
        }
        if ($p2 === false) {
            $p2 = -1;
        }
        if ($p1 == $p2) {
            return strcmp($a, $b);
        }
        return $p1 > $p2 ? 1 : -1;
    }

    usort($source, 'cmd');
    return $source;
}


/**
 *  消息通知服务
 *　    具体的命令及消息体内容请参考《提问相关状态自动推送系统设计》
 **/
function ncenter_notify($body, $extend, $notify_type, $extend_number = false, $extend_msg = false, $cmd_version = 1)
{
    $_msg = "${notify_type}:${cmd_version}#${body}#${extend}\n";
    if (!empty($extend_msg) and false !== $extend_number) {
        $_msg = "${notify_type}:${cmd_version}#${body}#${extend}#${extend_number}#" . base64_encode($extend_msg) . "\n";
    }
    $CI = &get_instance();

    $res = bp_operation_ok;
    $conf = $CI->config->item('ims_server');

    $begin_time = microtime(1);

    $fsocket = @fsockopen('tcp://' . $conf['host'], $conf['port'], $errno, $errmsg, 1); // 超时从3秒改成1秒，不然会导致接受问题时太慢

    if (!$fsocket) {
        log_message('error', "[ncenter_notify] fsockopen() failed! errno: $errno  reason: $errmsg  \n");
        return bp_socket_connect_error;
    }

    $buffer = $_msg;

    if (!fputs($fsocket, $_msg)) {
        log_message('error', "[ncenter_notify] fputs() failed: can not write msg to servert \n");
        return bp_socket_connect_error;
    }

    stream_set_timeout($fsocket, 1); // 超时从3秒改成1秒，不然会导致接受问题时太慢
    $output = fread($fsocket, 1024);
    fclose($fsocket);

    $data = explode(':', trim($output), 2);

    $cost = microtime(1) - $begin_time;

    if (count($data) == 2) {
        switch ($data[1]) {
            case 'OK':
                $res = bp_operation_ok;
                log_message('debug', "cost[$cost],推送消息到服务器信息成功！\t $buffer \n");
                break;
            case 'Data-Error':
                $res = bp_operation_verify_fail;
                log_message('debug', "cost[$cost], 推送格式不正确！\t $buffer \n");
                break;
            case 'Not-Accept':
                $res = bp_socket_server_refuse;
                log_message('debug', "cost[$cost], 服务器没有接收推送数据！\t $buffer \n");
                break;
        }
    } else {
        $res = bp_operation_verify_fail;
    }

    return $res;


    /*
    $commonProtocol = getprotobyname("tcp");
    $socket = socket_create(AF_INET, SOCK_STREAM, $commonProtocol);

    if(!$socket){
        log_message('error', "can't create socket ! ");
        return bp_socket_connect_error;
    }

    socket_set_option($socket,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>1, "usec"=>0 ) );//发送超时1s
    socket_set_option($socket,SOL_SOCKET,SO_SNDTIMEO,array("sec"=>3, "usec"=>0 ) );//接收超时3s

// socket_set_nonblock($socket);
    if(!@socket_connect($socket, $conf['host'],$conf['port'])){
        log_message('error', "socket_connect() failed: reason: " . socket_strerror(socket_last_error()) ."\n");
        return bp_socket_connect_error;
    }

        $buffer = $_msg;
        if(socket_write($socket, $buffer ,strlen($buffer)) == FALSE){
           log_message('error', "socket_write() failed: reason: " . socket_strerror(socket_last_error()) ."\n");
               $res =  bp_socket_connect_error;
        }else {

            $o = socket_read($socket, 1024, PHP_NORMAL_READ);
            $data = explode(':',trim($o),2);
            if(count($data) ==2){
                switch ($data[1]) {
                    case 'OK':
                        $res = bp_operation_ok;
                        log_message('debug', "推送消息到服务器信息成功！$buffer \n");
                        break;
                    case 'Data-Error':
                        $res = bp_operation_verify_fail;
                        log_message('debug', "推送格式不正确！$buffer \n");
                        break;
                    case 'Not-Accept':
                        $res = bp_socket_server_refuse;
                        log_message('debug', "服务器没有接收推送数据！$buffer \n");
                        break;
                }
            }else{
                $res =  bp_operation_verify_fail;
            }
        }


        socket_close($socket);

        return $res;
        */

}

//抽取数组一列内容
function array_extract_one_column($array, $key)
{
    $_tmp = array();
    foreach ($array as $value) {
        $_tmp[] = $value[$key];
    }
    return $_tmp;
}

//拼合数组，不关心内容
function concat_array(&$arr1 = array(), $arr2 = array())
{
    foreach ($arr2 as $row) {
        $arr1[] = $row;
    }
    return $arr1;
}

//根据数据内的一个项来去重数据
function array_unique_by_key($arr = array(), $key = null)
{
    if (empty($key)) {
        return array_unique($arr);
    } else {
        $tmp = array();
        $res = array();
        foreach ($arr as $row) {
            if (!in_array($row[$key], $tmp)) {
                $tmp[] = $row[$key];
                $res[] = $row;
            }
        }
        return $res;
    }
}


function log_error($msg, $obj = array())
{
    if (!empty($obj)) {
        $msg .= "\t" . log_var($obj);
    }
    log_message('error', $msg);
}

function log_debug($msg, $obj = array())
{
    if (!empty($obj)) {
        $msg .= "\t" . log_var($obj);
    }
    log_message('debug', $msg);
}

// 输出一个变量值，比json_encode好的地方在于，可以直接输出中文而不做转义
function log_var($obj)
{
    $str = var_export($obj, true);
    return str_replace("\n", ' ', $str);
}


function get_file_ext($filename = "")
{
    $x = explode('.', $filename);
    $ext = strtolower(end($x));

    return strtolower($ext);
}


function trans_null($item)
{
    return is_null($item) ? '' : $item;
}


function get_ip_location($ip)
{
    $output = get_file_via_curl('http://www.youdao.com/smartresult-xml/search.s?type=ip&jsFlag=true&q=' . $ip);
    $output = @iconv("GB2312", "UTF-8//IGNORE", $output);


    if (!strpos($output, 'location')) {
        return '';
    }
    $location = '';
    preg_match("/\'location\':\'([^']*)/i", $output, $matches);
    if ($matches) {
        $location = $matches[1];
    }
    return $location;

}


//查找对应帐户，如果admin=1,则表示通过admin查fu，反之则反,默认为1
function get_maped_username($username, $admin = 1)
{

    $map = array('admin_mabo' => 'fu-mabo', 'admin_haimiao' => 'fu-haimiao', 'admin_jianshi' => 'fu-jianshi', 'admin_fp' => 'fu-fangpei', 'admin_hqian' => 'fu-hqian', 'admin_degang' => 'fu-degang', 'admin_weiqian' => 'fu-weiqian', 'admin_yinxing' => 'fu-yinxing', 'admin_shiyong' => 'fu-shiyong', 'admin_yutao' => 'fu-yutao', 'admin_liyang' => 'fu-liyang', 'admin_gm' => 'fu-gongmao', 'admin_xuezhong' => 'fu-xuezhong', 'admin_yqk' => 'fu-yqk', 'afdxo' => 'fu-liubiao', 'admin_liuhua' => 'fu-liuhua', 'admin_cxy' => 'fu-cxy', 'admin_qxz' => 'fu-shanshan', 'admin_chenqing' => 'fu-chenqing', 'admin_lina' => 'fu-lina', 'admin_xiaolei' => 'fu-xiaolei', 'admin_fdd' => 'fu-teacher2', 'admin_qxz' => 'fu-teacher3', 'admin_zr' => 'fu-teacher4', 'admin_lijun' => 'fu-teacher5', 'admin_ydm' => 'fu-ydm', 'admin_shj' => 'fu-shj', 'admin_yq' => 'fu-yongqiang', 'admin_guangyu' => 'fu-guangyu', 'admin_wenjie' => 'fu-wenjie', 'admin_gaojian' => 'fu-gaojian',);

    $to = false;
    if ($admin) {
        if (array_key_exists($username, $map)) {
            $to = $map[$username];
        }
        if (!$to) {
            $to = 'FU';
        }
    } else {
        $tmp = array_search($username, $map);
        if (!empty($tmp)) {
            $to = $tmp;
        }
    }

    return $to;
}

function get_partner_level($buying_ratio)
{
    if ($buying_ratio <= 71) {
        return '一级';
    } else if ($buying_ratio <= 72) {
        return '二级';
    } else {
        return '三级';
    }
}


// receipt verification from apple receipt validation server
function get_receipt_verification_data_from_apple_server($pay_id, $receipt_data, $sandbox = FALSE)
{
    $post_body = json_encode(array('receipt-data' => $receipt_data));
    if ($sandbox) {
        $info = get_file_via_curl('https://sandbox.itunes.apple.com/verifyReceipt', $post_body);
    } else {
        $info = get_file_via_curl('https://buy.itunes.apple.com/verifyReceipt', $post_body);
    }
    if ($info && strpos($info, '{') == 0) {
        $info = json_decode($info, TRUE);
        if (!isset($info['status'])) {
            return FALSE;
        } else {
            return $info;
        }
    }
    return FALSE;
}

function get_daily_doudou_by_rand($need_fix_by_s1, $zero_doudou_count, $nonzero_doudou_count, $is_sales_support_account, $is_has_charged_account, $is_account_from_agent)
{
    if ($is_sales_support_account) {
        return 0;
    }

    if ($is_has_charged_account || $is_account_from_agent) {
        if (mt_rand(0, 8) == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    if ($need_fix_by_s1) {
        if ($zero_doudou_count >= 3) {
            return 1;
        } else if ($nonzero_doudou_count >= 1) {
            return 0;
        }
    }
    return mt_rand(0, 1);
}

function is_experience_teacher($username)
{
    $CI = &get_instance();
    $exp_teas = $CI->config->item('experience_teacher_id');
    return in_array($username, $exp_teas);
}

function get_blocked_manufacturer_login_hint($user_agent)
{
    $ul = explode(' ', $user_agent, 12);
    $uc = count($ul);
    if (($uc != 7 && $uc != 8) || strstr($ul[0], 'Dalvik') == false || strstr($ul[1], 'Linux') == false || strstr($ul[3], 'Android') == false) {
        return false;
    }

    $build = $ul[$uc - 1];
    $ul[$uc - 1] = substr($build, 0, strlen($build) - 1);
    if ($uc == 7) {
        $ua_array = array($ul[5], $ul[6]);
    } else {
        $ua_array = array($ul[5], $ul[6], $ul[7]);
    }
    $model_in_user_agent = implode(' ', $ua_array);

    $CI = &get_instance();
    $user_agent_list = $CI->config->item('user_agent_to_model');
    $manu_list = $CI->config->item('model_to_manufacturer');
    $blocked_manu_list = $CI->config->item('blocked_manufacturer');

    if (array_key_exists($model_in_user_agent, $user_agent_list)) {
        $model = $user_agent_list[$model_in_user_agent];
        if (array_key_exists($model, $manu_list)) {
            $manu = $manu_list[$model];
            if (array_key_exists($manu, $blocked_manu_list)) {
                return $model . $blocked_manu_list[$manu];
            }
        }
    }

    return false;
}

function get_source_name_in_call_out_list($value)
{
    if ($value == 'client-ipad') return 'iPad'; else if ($value == 'client-iphone') return 'iPh'; else if ($value == 'client') return '苹果'; else if ($value == 'an') return 'aPh'; else if ($value == 'an_pad') return 'aPad'; else if ($value == 'web') return '网站'; else return '-';
}

function get_real_name_in_call_out_list($realname)
{
    if (!isset($realname) || $realname == '未设' || strlen($realname) < 2) {
        return '-';
    } else {
        return $realname;
    }
}

function get_question_status($status)
{
    switch ($status) {
        case IMS_MESSAGE_QUESTION_STATUS_PUSHED:
            return '已发给老师';
        case IMS_MESSAGE_QUESTION_STATUS_RESOLVING:
            return '老师在解答中';
        case IMS_MESSAGE_QUESTION_STATUS_RESOLVED:
            return '已解决';
        case IMS_MESSAGE_QUESTION_STATUS_CLOSED:
            return '已关闭';
        default:
            return '新提交';
    }
}

function find_a_tag_in_html($html, $tagname, $max_skip = 0)
{
    if ($max_skip > 0) {
        $p = stripos($html, '<' . $tagname . ' ');
        if ($p == false || $p > $max_skip) return false;
    }

    $s = stristr($html, '<' . $tagname . ' ');
    if ($s == false) {
        $s = stristr($html, '<' . $tagname . '>');
    }
    if ($s == false) return false;

    $e = stripos($s, '</' . $tagname . '>');
    if ($e == false) return false;

    return substr($s, 0, $e + strlen('</' . $tagname . '>'));
}

function get_property_in_tag($tag, $property)
{
    $e = strpos($tag, '>');
    if ($e == false) return false;

    $s = substr($tag, 0, $e);

    $s = stristr($s, $property . '="');
    if ($s == false) return false;

    $s = substr($s, strlen($property . '="'));

    $s_list = explode('"', $s);
    return $s_list[0];
}

function get_html_enclosed_in_tag($tag)
{
    $s = stristr($tag, '>');
    if ($s == false) return false;

    $s = substr($s, 1);

    $e = strrpos($s, '<');
    if ($e == false) return false;

    return substr($s, 0, $e);
}

function echo_with_datetime($to_echo)
{
    echo(date('Y-m-d H:i:s', time()) . " " . $to_echo);
}

function get_book_title($version, $subject, $caption)
{
    $title = trim($version) . "-" . trim($subject) . "-" . trim($caption);
    $title = str_replace("学期", "册", $title);
    return $title;
}

function memo_title($mid)
{
    if ($mid == 24) {
        return '放弃';
    } else if ($mid == 10) {
        return '成交';
    } else if ($mid == 23) {
        return '失败';
    } else if ($mid == 22) {
        return '待定';
    } else if ($mid == 15) {
        return '试听';
    } else if ($mid == 21) {
        return '意向';
    } else if ($mid == 20) {
        return '普通';
    } else if ($mid == 0) {
        return '未联系上';
    } else {
        return '未知';
    }
}

//显示可读的注册来源
function reg_source($f)
{
    if ($f == "client-ipad") {
        return "iPad";
    } else if ($f == "client-iphone") {
        return "iPhone";
    } else if ($f == "an") {
        return "安卓手机";
    } else if ($f == "an_pad") {
        return "安卓平板";
    } else if ($f == "pexpaper-iPad") {
        return "小学生书包";
    } else if ($f == "abooks-iPad") {
        return "小学生书包";
    } else if ($f == "hexpaper-iPad") {
        return "高中题库";
    } else if ($f == "readboy") {
        return "读书郎";
    } else if ($f == "0" or $f == 'web') {
        return "网页";
    } else {
        return $f;
    }
}

function cmp_student_by_last_record($a, $b)
{
    if (empty($a['last_record']) || count($a['last_record']) < 1) {
        return -1;
    } else if (empty($b['last_record']) || count($b['last_record']) < 1) {
        return +1;
    } else {
        return strcmp($a['last_record'][0]['time'], $b['last_record'][0]['time']);
    };
}

function cmp_string_array_item($a, $b)
{
    return strnatcasecmp($a, $b);
}

function has_primary_grade($grades, $grade_options)
{
    foreach ($grades as $item) {
        $gid = $item['gid'];
        if ($gid == $grade_options[0]['gid']) return true;
        foreach ($grade_options[0]['children'] as $grade) {
            if ($grade['gid'] == $gid) {
                return true;
            }
        }
    }
    return false;
}

function has_middle_grade($grades, $grade_options)
{
    foreach ($grades as $item) {
        $gid = $item['gid'];
        if ($gid == $grade_options[1]['gid']) return true;
        foreach ($grade_options[1]['children'] as $grade) {
            if ($grade['gid'] == $gid) {
                return true;
            }
        }
    }
    return false;
}

function has_high_grade($grades, $grade_options)
{
    foreach ($grades as $item) {
        $gid = $item['gid'];
        if ($gid == $grade_options[2]['gid']) return true;
        foreach ($grade_options[2]['children'] as $grade) {
            if ($grade['gid'] == $gid) {
                return true;
            }
        }
    }
    return false;
}


/*图像处理*/

function LoadPNG($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefrompng($imgname);

    /* See if it failed */
    if (!$im) {
        return false;
        // /* Create a blank image */
        // $im  = imagecreatetruecolor(150, 30);
        // $bgc = imagecolorallocate($im, 255, 255, 255);
        // $tc  = imagecolorallocate($im, 0, 0, 0);

        // imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        // /* Output an error message */
        // imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}

function LoadJpeg($imgname)
{
    /* 尝试打开 */
    $im = @imagecreatefromjpeg($imgname);

    /* See if it failed */
    if (!$im) {

        return false;
        // /* Create a black image */
        // $im  = imagecreatetruecolor(150, 30);
        // $bgc = imagecolorallocate($im, 255, 255, 255);
        // $tc  = imagecolorallocate($im, 0, 0, 0);

        // imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        // /* Output an error message */
        // imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}


function imageCreateFromAny($filepath)
{
    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
    $allowedTypes = array(1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if (!in_array($type, $allowedTypes)) {
        return false;
    }
    switch ($type) {
        case 1 :
            $im = imageCreateFromGif($filepath);
            break;
        case 2 :
            $im = imageCreateFromJpeg($filepath);
            break;
        case 3 :
            $im = imageCreateFromPng($filepath);
            break;
        case 6 :
            $im = imageCreateFromBmp($filepath);
            break;
    }
    return $im;
}

function mb_wordwrap($str, $width = 75, $break = "\n", $cut = false)
{
    $return = '';
    $br_width = mb_strlen($break, 'UTF-8');
    for ($i = 0, $count = 0; $i < mb_strlen($str, 'UTF-8'); $i++, $count++) {
        if (mb_substr($str, $i, $br_width, 'UTF-8') == $break) {
            $count = 0;
            $return .= mb_substr($str, $i, $br_width, 'UTF-8');
            $i += $br_width - 1;
        }

        if ($count > $width) {
            $return .= $break;
            $count = 0;
        }

        $return .= mb_substr($str, $i, 1, 'UTF-8');
    }

    return $return;
}

//end  nbmhf

// 获得某个日期的下一个月同日的日期，如果下个月没有当天，则返回最后一天，譬如2014-1-31日的下一个日期是2014-2-28
// 支持跨年日期，譬如2014-12-20的下一个日期是2015-1-20
function get_next_month_date($current_date, $do_change_time_when_less_than_one_month = false)
{
    $current_date = strtotime($current_date);
    $next_month_date = mktime(date("G", $current_date), date("i", $current_date), date("s", $current_date), date("n", $current_date) + 2, 0, date("Y", $current_date));
    $next_month_days = date("t", $next_month_date);
    if ($next_month_days < date("j", $current_date)) {
        if (!$do_change_time_when_less_than_one_month) {
            return date("Y-m-t H:i:s", $next_month_date);
        } else {
            return date("Y-m-t 23:59:59", $next_month_date);
        }
    } else {
        return date(date("Y-m", $next_month_date) . "-d H:i:s", $current_date);
    }
}

// end of get_next_month_date
//
// 获得某个日期的上一个月同日的日期，如果上个月没有当天，则返回最后一天，譬如2014-1-31日的下一个日期是2014-2-28
// 支持跨年日期，譬如2014-1-20的上一个日期是2013-12-20
function get_pre_month_date($current_date, $do_change_time_when_less_than_one_month = false)
{
    $current_date = strtotime($current_date);
    $pre_month_date = mktime(date("G", $current_date), date("i", $current_date), date("s", $current_date), date("n", $current_date), 0, date("Y", $current_date));
    $pre_month_days = date("t", $pre_month_date);
    if ($pre_month_days < date("j", $current_date)) {
        if (!$do_change_time_when_less_than_one_month) {
            return date("Y-m-t H:i:s", $pre_month_date);
        } else {
            return date("Y-m-t 23:59:59", $pre_month_date);
        }
    } else {
        return date(date("Y-m", $pre_month_date) . "-d H:i:s", $current_date);
    }
}

// end of get_pre_month_date

/*
date_default_timezone_set("Asia/Chongqing");
$date = "2014-1-31 12:20:15";
echo($date."\n");
echo(get_next_month_date($date, true)."\n");
 */
function get_admin_user_info_config($all = false, $only_worklist = false)
{
    $CI = &get_instance();
    $CI->load->model('admin_user_info_model', 'admin_user_info');
    return $CI->admin_user_info->admin_user_info($all, $only_worklist);
}

function follower_select($followers, $follower, $sel_id = 'follower-select')
{
    $_hts = array();
    $_consultants = array();
    foreach ($followers as $k => $admin_info) {
        if ($admin_info['is_consultant'] == 1) {
            $_consultants[$admin_info['username']] = $admin_info;
        }
        if ($admin_info['is_consultant'] == 2) {
            $_hts[$admin_info['username']] = $admin_info;
        }
    }

    $_html = '<select id="' . $sel_id . '" class="input-small">';
    $_html .= '<optgroup label="咨询师">';
    foreach ($_consultants as $u => $info) {
        $_html .= '<option value="' . urlencode($u) . '"';
        if ($u == $follower) {
            $_html .= ' selected';
        }
        $_html .= '>' . $info['realname'] . '</option>';
    }
    $_html .= '</optgroup>';
    $_html .= '<optgroup label="班主任">';
    foreach ($_hts as $u => $info) {
        $_html .= '<option value=' . urlencode($u);
        if ($u == $follower) {
            $_html .= ' selected';
        }
        $_html .= '>' . $info['realname'] . '</option>';
    }
    $_html .= '</optgroup></select>';
    return $_html;
}

/*
 * 每天的中午12：15~13：55 ，19：00~9：25
*/
function pool_entry_permited()
{
    $today = standard_date('DATE_YMD', time());
    $yesterday = standard_date('DATE_YMD', strtotime("-1 day"));
    $open_morning_start = human_to_unix($yesterday . ' 19:00:00');
    $open_morning_end = human_to_unix($today . ' 09:25:00');
    $open_noon_start = human_to_unix($today . ' 12:15:00');
    $open_noon_end = human_to_unix($today . ' 13:55:00');
    $now = time();

    if (($now > $open_noon_start and $now < $open_noon_end) or ($now > $open_morning_start and $now < $open_morning_end)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function unique_device_id($mac, $idfa)
{
    $_unique_ad_id = time();
    if ($mac != 'NOMAC' and $mac != '020000000000' and $mac != '02:00:00:00:00:00' and !empty($mac)) {
        $_unique_ad_id = $mac;
    }
    if ($idfa != 'NOIDFA' and !empty($idfa)) {
        $_unique_ad_id = $idfa;
    }
    return $_unique_ad_id;
}

function is_account_charge_deal($type, $from)
{
    if ($type == 1 && ($from == PAY_FROM_ADMIN_FOR_USER_CHARGE || $from == PAY_FROM_TENPAY || $from == PAY_FROM_ALI || $from == PAY_FROM_99BILL || $from == PAY_FROM_UNIONPAY || $from == PAY_FROM_ALI_IN_CLIENT || $from == PAY_FROM_UPMP_IN_CLIENT || $from == PAY_FROM_CHARGE_CARD)) {
        return true;
    } else {
        return false;
    }
}

//注意：year要使用date('o')类型,mysql里面是%X类型
function get_monday_date_of_week($week, $year)
{
    $t = strtotime('first day of january ' . $year);
    $t2 = strtotime('+' . ($week - 1) . ' weeks', $t);
    $t2 = strtotime('monday this week', $t2);
    return $t2;
}

function dayi_rank_list()
{
    $_options = array('未定级', 'pre-I级', 'pre-II级', 'I级', 'II级', 'III级', 'IV级', 'V级', 'VI级');
    return $_options;
}

function dayi_doudou_pay_weight($dayi_rank)
{
    $_value = array(0, 0.6, 0.8, 1, 1.2, 1.3, 1.4, 1.5, 2.0);
    return element($dayi_rank, $_value, 0);
}

function dayi_rank_title($dayi_rank)
{
    $dayi_rank_list = dayi_rank_list();
    return $dayi_rank_list[$dayi_rank];
}

function dayi_auditor_type_list()
{
    $_options = array('0' => '无审核权限', '2' => '可审核擅长学科全年级老师',);
    return $_options;
}

function auth_step_status_title($auth_step)
{
    switch ($auth_step) {
        case 0:
            return "";
        case 1:
            return "答疑老师认证申请已提交";
        case 2:
            return "答疑老师认证审核处理中";
        case 3:
            return "答疑老师认证审核已通过";
        case 4:
            return "答疑老师认证申请被驳回";
        case 11:
            return "一对一辅导老师认证申请已提交";
        case 12:
            return "一对一辅导老师认证审核处理中";
        case 13:
            return "一对一辅导老师认证审核已通过";
        case 14:
            return "一对一辅导老师认证申请被驳回";
        case 21:
            return "答疑老师升级申请已提交";
        case 22:
            return "答疑老师升级审核处理中";
        case 23:
            return "答疑老师升级审核已通过";
        case 24:
            return "答疑老师升级申请被驳回";
        case 31:
            return "一对一辅导老师升级申请已提交";
        case 32:
            return "一对一辅导老师升级审核处理中";
        case 33:
            return "一对一辅导老师升级审核已通过";
        case 34:
            return "一对一辅导老师升级申请被驳回";
        default:
            return "";
    }
}

function auth_step_status_class_name($auth_step)
{
    if ($auth_step % 10 == 1) {
        return "blue";
    } else if ($auth_step % 10 == 2) {
        return "blue";
    } else if ($auth_step % 10 == 3) {
        return "green";
    } else if ($auth_step % 10 == 4) {
        return "red";
    } else {
        return "";
    }
}

function dayi_upgrade_apply_session_limit($dayi_rank)
{
    switch ($dayi_rank) {
        case 0:
            return 50;
        case 1:
            return 500;
        case 2:
            return 1000;
        case 3:
            return 10000;
        case 4:
            return 30000;
        case 5:
            return 50000;
        case 6:
            return 100000;
        case 7:
            return 200000;
        default:
            return 300000;
    }
}

function dayi_upgrade_apply_good_ratio_limit($dayi_rank)
{
    switch ($dayi_rank) {
        case 0:
            return 0.7;
        case 1:
            return 0.8;
        case 2:
            return 0.85;
        case 3:
            return 0.90;
        case 4:
            return 0.90;
        case 5:
            return 0.90;
        case 6:
            return 0.95;
        case 7:
            return 0.95;
        default:
            return 0.96;
    }
}

function ranking_level_by_uranking($uranking)
{
    if ($uranking <= 3) {
        return 1;
    } else if ($uranking <= 10) {
        return 2;
    } else if ($uranking <= 30) {
        return 3;
    } else {
        return 9;
    }
}

function is_upgrade_apply_enabled($ranking_levels)
{
    if ($ranking_levels === false) return false;

    $gold = (array_key_exists('gold', $ranking_levels) ? $ranking_levels['gold'] : 0);
    $silver = (array_key_exists('silver', $ranking_levels) ? $ranking_levels['silver'] : 0);
    $normal = (array_key_exists('normal', $ranking_levels) ? $ranking_levels['normal'] : 0);

    $rs = $gold * 15 + $silver * 5 + $normal * 3;

    return ($rs >= 15);
}

/*
 * 如果想关闭某个优惠活动，直接从这里去掉即可；
 * 如果想增加某类优惠，直接将优惠名称和id放在这里，注意id不能重用，且必须同步修改下面的优惠函数，以支持正确计费
 */
function vvip_mode_list()
{
    $_options = array('不享有特别优惠' => 0, '暑假特惠一级（再减3元）' => 1, '暑假特惠二级（再减5元）' => 2, '暑假特惠三级（再减8元）' => 3, '暑假特惠四级（再减10元）' => 4, '暑假特惠五级（再减12元）' => 5, '暑假特惠六级（再减15元）' => 6, '暑假特惠七级（再减16元）' => 7, '暑假特惠八级（再减17元）' => 8,);
    return $_options;
}

function vvip_mode_name($vvip_mode)
{
    $options = vvip_mode_list();

    foreach ($options as $key => $value) {
        if ($value == $vvip_mode) {
            return $key;
        }
    }

    return '未享有任何特惠';
}

/*
 * 增加某项优惠时，必须修改这里增加优惠具体配置
 */
function vvip_discount_per_hour($vvip_mode)
{
    switch ($vvip_mode) {
        case 1:
            return 3.0 * 60 / 45;
        case 2:
            return 5.0 * 60 / 45;
        case 3:
            return 8.0 * 60 / 45;
        case 4:
            return 10.0 * 60 / 45;
        case 5:
            return 12.0 * 60 / 45;
        case 6:
            return 15.0 * 60 / 45;
        case 7:
            return 16.0 * 60 / 45;
        case 8:
            return 17.0 * 60 / 45;
        default:
            return 0;
    }
}


function app_status_title($app_status)
{
    switch ($app_status) {
        case APP_STATUS_NO_INIT:
            return '未设置';
        case APP_STATUS_APPLY:
            return '已经提交申请';
        case APP_STATUS_IN_PROCESS:
            return '正在生成App...';
        case APP_STATUS_REFUSE:
            return '已经驳回申请';
        case APP_STATUS_REDRAW:
            return '已经撤回申请';
        case APP_STATUS_PROCESSED:
            return '已经生成App';
        default:
            return '';
    }
}

/* 检查是否是万利达支持的UserAgent,  如果包含了支持列表中的模块，则返回true，否则返回false */
function is_malata_user_agent($user_agent)
{
    $malata_models = array('Q3', 'Q7', 'X7', 'S2800', 'T9000S', 'Q3+', 'Q8', 'X5', 'malataeduz500', 'Z8S', 'Z8', 'Q18', 'e7007', 'Q5', 'Q9', 'Q6', 'S2100');

    foreach ($malata_models as $model) {
        if (strpos($user_agent, $model) !== FALSE) {
            return TRUE;
        }
    }

    return FALSE;
}

/**
 * 描述解析
 * @param $desc
 * @return array
 */
function desc_decode($desc)
{
    $title_seperator = '[__T__]';
    $content_seperator = '[__C__]';
    $arr = explode($title_seperator, $desc);
    $data = array();
    foreach ($arr as $row) {
        if (empty($row)) continue;
        $tarr = explode($content_seperator, $row);
        $data[] = array(
            "title" => element(0, $tarr, ''),
            "content" => element(1, $tarr, '')
        );
    }
    return $data;
}

function desc_encode($desc_title, $desc_content)
{
    $title_seperator = '[__T__]';
    $content_seperator = '[__C__]';
    $data = array();
    foreach ($desc_title as $key => $row) {
        $tarr = $title_seperator . $row . $content_seperator . element($key, $desc_content, '');
        $data[] = $tarr;
    }
    return implode($data);
}

function get_admin_roles()
{
    /*
     * define('ADMIN_ROLE_ENTRANCE', 1);   //管理入口(管理员,设计师,助理)
define('ADMIN_ROLE_USER', 2);    //用户管理(管理员,设计师)
define('ADMIN_ROLE_FINANCE', 4);   //系统财务(管理员)
define('ADMIN_ROLE_STAT', 8);  //系统统计(管理员)
define('ADMIN_ROLE_ORDER', 16);     //订单跟踪(管理员)
define('ADMIN_ROLE_PRODUCT', 32);   //商品管理统计(管理员,设计师,设计师助理)
define('ADMIN_ROLE_COLLECTION', 64);//专题管理,统计(管理员,设计师,设计师助理)
define('ADMIN_ROLE_COUPON', 128);//优惠券  (管理员)
define('ADMIN_ROLE_ALL', 65535);
     */
    return array(
        ADMIN_ROLE_ENTRANCE => "管理入口"
    , ADMIN_ROLE_USER => "用户管理G"
    , ADMIN_ROLE_FINANCE => "系统财务G"
    , ADMIN_ROLE_STAT => "系统统计G"
    , ADMIN_ROLE_ORDER => "订单管理"
    , ADMIN_ROLE_PRODUCT => "商品管理"
    , ADMIN_ROLE_COLLECTION => "专题管理"
    , ADMIN_ROLE_COUPON => "优惠券管理"
    , ADMIN_ROLE_APPLICANT => "认证管理"
//    , ADMIN_ROLE_ALL => "超级权限"

    );
}

function get_order_status()
{
    return [
        ORDER_STATUS_DELETE=>"已删除",
        ORDER_STATUS_INIT=>"预定(未付款)",
        ORDER_STATUS_PRE_PAID=>"预定(已付款)",
        ORDER_STATUS_LAST_PAY_START=>"付尾款(开始)",
        ORDER_STATUS_LAST_PAID=>"付尾款(已付)",
        ORDER_STATUS_SHIP_START=>"已发货",
        ORDER_STATUS_SHIP_RECEIVED=>"已收货",
        ORDER_STATUS_END_SUCCEED=>"结束(成功)",
        ORDER_STATUS_END_FAIL=>"结束(失败)",
	ORDER_STATUS_CANCEL_UNPAY => "已取消（未支付）",
	ORDER_STATUS_CANCEL_PAY=>"已取消（已支付）",
    ];
}
function format_order_status($status){
    $s = get_order_status();
    return element($status, $s, '未知');
}

function get_list_mem_key($prefix,$params){
    $key = array($prefix,'list',serialize($params));
    return md5(implode(':',$key));
}
function get_info_mem_key($prefix,$id,$additional=''){
    return $prefix.':'.$id.':'.$additional;
}
function logger($msg, $level = 'Info', $file = '')
{
    if (empty($file)) {
        $logs = dirname(__FILE__).'/../../logs/log';
    } else {
	$logs = dirname(__FILE__).'/../../logs/'.$file;
    }
    $maxSize = 100000;
    if (file_exists($logs) && (abs(filesize($logs)) >= $maxSize)) {
        file_put_contents($logs, 'Max Size:'.$maxSize.' log cleaned'."\n");
    }
    file_put_contents($logs, date('Y-m-d H:i:s').' '.$level.': '.$msg."\n", FILE_APPEND);
}
function getRandomStr($string = '23456789ABCDEFGHJKLMNOPQRSTUVWXZY', $num = 8)
{
   $str = '';
   $length = strlen($string);
   do {
	$str .= substr($string, (rand(0,1000)%$length), 1);
   } while (strlen($str) < $num);

   return $str;
}

function getSignPackage() {
    $jsapiTicket = getJsApiTicket();
    //echo $jsapiTicket;die;
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = createNonceStr();
    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    $signature = sha1($string);
    $signPackage = array(
      "appId"     => APPID,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    
    return $signPackage;
  }

function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents(dirname(__FILE__)."/../../jsapi_ticket.json"));
    //echo '<pre>'; print_r($data);die;
    if ($data->expire_time < time()) {
      $accessToken = getAccessToken();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode(httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $fp = fopen(dirname(__FILE__)."/../../jsapi_ticket.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents(dirname(__FILE__)."/../../access_token.json"));
    //echo '<pre>'; print_r($data);die;
    if ($data->expire_time < time()) {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
      $res = json_decode(httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $fp = fopen(dirname(__FILE__)."/../../access_token.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

function httPost($url, $data=false, $timeout = 30) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
	if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);

	return $result;
}
function curl_es($data, $url = 'api/coru')
{
        if (isset($data['data']['create_time'])) {
            $data['data']['create_time'] = time();
        }
        $data = array('data' => $data);
        $data = json_encode($data);
        $url = 'http://10.26.95.72/index.php/'.$url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:'.strlen($data)
        ));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
}
function debug($info, $start)
{
        $mid = microtime(true); // debug
        logger($info.(round($mid - $start, 3) * 1000)); // debug

        return $mid;
}
function generateTree($items, $field='parent_id', $id='id')
{
    $tree = array();
    foreach($items as $item) {
        if(isset($items[$item[$field]])){
            $items[$item[$field]]['son'][] = &$items[$item[$id]];
        }else{
            $tree[] = &$items[$item[$id]];
        }
    }
    return $tree;
}
function getTreeData($tree)
{
    foreach($tree as $t) {
	$str = '<tr id="'.$t['id'].'a">';
        $str .= '<td>'.$t['id'].'</td>';
	$prefix = '|____';
	while ($t['level'] > 1) {
	    $prefix .= '|____';
	    $t['level']--;
   	}
	if (isset($t['son'])) {
            $str .= '<td>'.$prefix.$t["name"].'</td>';
  	} else {
            $str .= '<td>'.$prefix.'<b>'.$t["name"].'</b></td>';
	}
        $str .= '<td>'.$t["chinese_name"].'</td>';
        $str .= '<td>'.$t["weight"].'</td>';
        $str .= '<td>'.$t["tax_rate"].'</td>';
        if ($t["is_show"] == 1) {
            $str .= '<td>是</td>';
        } else {
            $str .= '<td>否</td>';
        }
        $str .= '<td><a href="javascript:void(0);" onclick="edit(\''.$t['id'].'\', \''.$t['is_show'].'\');">编辑</a></td></tr>';
        $str .= '<tr id="'.$t['id'].'b" class="edit2">';
        $str .= '<td>'.$t["id"].'</td>';
        $str .= '<td>'.$t["name"].'</td>';
        $str .= '<td><input type="text" id ="'.$t['id'].'chname" value="'.$t['chinese_name'].'"></td>';
        $str .= '<td><input type="text" id ="'.$t['id'].'wgt" value="'.$t['weight'].'"></td>';
        $str .= '<td><input type="text" id ="'.$t['id'].'trate" value="'.$t['tax_rate'].'"></td>';
        $str .= '<td><select id="'.$t['id'].'show" class="form-control" data-pid="" aria-invalid="false"><option value="1">是</option><option value="0">否</option></select></td>';
        $str .= '<td><a href="javascript:void(0);" onclick="sub(\''.$t['id'].'\');">提交</a>';
        $str .= '<a href="javascript:void(0);" onclick="cancel(\''.$t['id'].'\');">取消</a></td></tr>';
	echo $str;
        if(isset($t['son'])) {
            getTreeData($t['son']);
        }
    }
}
function getTreeData2($tree)
{
    foreach($tree as $t) {
	$prefix = '|____';
        while ($t['level'] > 1) {
            $prefix .= '|____';
            $t['level']--;
        }
	if (isset($t['son'])) {
	    echo '<option value="'.$t['id'].'__'.$t['name'].'">'.$prefix.$t['name'].'</option>';
	} else {
	    echo '<option value="'.$t['id'].'__'.$t['name'].'" style="color:red;font-weight:700;">'.$prefix.$t['name'].'</option>';
	}
        if(isset($t['son'])) {
            getTreeData2($t['son']);
        }
    }
}
