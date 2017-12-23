<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Standard Date
 *
 * Returns a date formatted according to the submitted standard.
 *
 * @access    public
 * @param    string    the chosen format
 * @param    integer    Unix timestamp
 * @return    string
 */
if (!function_exists('standard_date')) {
    function standard_date($fmt = 'DATE_RFC822', $time = '')
    {
        $formats = array(
            'DATE_ATOM' => '%Y-%m-%dT%H:%i:%s%Q',
            'DATE_COOKIE' => '%l, %d-%M-%y %H:%i:%s UTC',
            'DATE_ISO8601' => '%Y-%m-%dT%H:%i:%s%Q',
            'DATE_RFC822' => '%D, %d %M %y %H:%i:%s %O',
            'DATE_RFC850' => '%l, %d-%M-%y %H:%m:%i UTC',
            'DATE_RFC1036' => '%D, %d %M %y %H:%i:%s %O',
            'DATE_RFC1123' => '%D, %d %M %Y %H:%i:%s %O',
            'DATE_RSS' => '%D, %d %M %Y %H:%i:%s %O',
            'DATE_W3C' => '%Y-%m-%dT%H:%i:%s%Q',
            'DATE_MYSQL' => '%Y-%m-%d %H:%i:%s',
            'DATE_TIME' => '%H:%i:%s',
            'DATE_HI' => '%H:%i',
            'DATE_TIMERANGE_START' => '%Y-%m-%d-%H:%i',
            'DATE_YMD' => '%Y-%m-%d',
            'DATE_YM' => '%Y-%m',
            'DATE_WEEKDAY' => '%W',
            'DATE_MONTHDAY' => '%m-%d',
            'DATE_DAY' => '%d',
            'DATE_NOZERODAY' => '%j',
            'DATE_HOUR' => '%H',
            'DATE_MINUTE' => '%i',
            'DATE_HOURMINUTE' => '%H:%i',
            'DATE_CHMD' => '%n月%j日',
            'DATE_CHYM' => '%Y年%m月',
            'DATE_CHMD_TIME' => '%n月%j日 %H:%i',
            'DATE_SOAP' => '%Y%m%d%H%i%s',

        );

        if (!isset($formats[$fmt])) {
            return FALSE;
        }

        return mdate($formats[$fmt], $time);
    }
}

// ------------------------------------------------------------------------

/**
 * Standard Date
 *
 * Returns a date formatted according to the submitted standard.
 *
 * @access    public
 * @param    datetime    unix datetime
 * @return    num weekday (1-7)
 */
if (!function_exists('weekday')) {
    function weekday($unix_datetime = '')
    {
        //echo human_to_unix($human_datetime);
        $d = date('N', $unix_datetime);
        return $d;
    }
}

// ------------------------------------------------------------------------

function sms_time_range($params, $parame)
{
    $et = standard_date('DATE_HI', human_to_unix($parame));
    $_ust = human_to_unix($params);
    $_wd = chinese_weekday(weekday($_ust) - 1);

    $day = date('Ymd', $_ust);
    $today = date('Ymd');

    $modify_day_info = '';

    if ($day == $today) {
        $modify_day_info = '今天';
    } elseif (($today + 1) == $day) {
        $modify_day_info = '明天';
    } else {
        $modify_day_info = sprintf('%s(周%s)', standard_date('DATE_CHMD', $_ust), $_wd);
    }
    $st = sprintf('%s %s', $modify_day_info, standard_date('DATE_HI', $_ust));
    unset($_ust, $modify_day_info, $day, $today);
    return $st . '~' . $et;
}

/*
    今天的：hh:ss  10:20:22
    昨天的：昨天10：20:22
    本年：7.8 10：20:22
    去年：2012.7.8 10：20:22
*/

function is_today($time)
{
    $_unix_time = human_to_unix($time);
    $day = date('Ymd', $_unix_time);

    $today = date('Ymd');

    if ($day == $today) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function beautify_date_in_follow($time)
{
    $_unix_time = human_to_unix($time);

    $day = date('Ymd', $_unix_time);
    $year = date('Y', $_unix_time);

    $today = date('Ymd');
    $yesterday = date("Ymd", strtotime("-1 day"));
    $byesterday = date("Ymd", strtotime("-2 day"));
    $this_year = date('Y');

    $modify_day_info = '';
    if ($year >= $this_year) {
        if ($day == $today) {
            $modify_day_info = '今天';
        } else if ($yesterday == $day) {
            $modify_day_info = '昨天';
        } else if ($byesterday == $day) {
            $modify_day_info = '前天';
        } else {
            $modify_day_info = standard_date('DATE_CHMD', $_unix_time);
        }
    } else if ($year > '2008') {
        $modify_day_info = standard_date('DATE_YMD', $_unix_time);
    } else {
        $modify_day_info = '';
    }
    if ($modify_day_info != '') {
        return $modify_day_info . ' ' . standard_date('DATE_HI', $_unix_time);
    } else {
        return '';
    }
}

function beautify_date_in_class($time)
{
    $_unix_time = human_to_unix($time);

    $day = date('Ymd', $_unix_time);
    $year = date('Y', $_unix_time);

    $today = date('Ymd');
    $yesterday = date("Ymd", strtotime("-1 day"));
    $byesterday = date("Ymd", strtotime("-2 day"));
    $this_year = date('Y');

    $modify_day_info = '';
    if ($year >= $this_year) {
        if ($day == $today) {
            $modify_day_info = '今天';
        } else if ($yesterday == $day) {
            $modify_day_info = '昨天';
        } else if ($byesterday == $day) {
            $modify_day_info = '前天';
        } else {
            $modify_day_info = standard_date('DATE_CHMD', $_unix_time);
        }
    } else if ($year > '2008') {
        $modify_day_info = standard_date('DATE_YMD', $_unix_time);
    } else {
        $modify_day_info = '';
    }

    return $modify_day_info;
}


// 实际上应该取下一周一的时间
function week_last_day($unix_datetime = false)
{
    if (is_string($unix_datetime)) {
        $unix_datetime = human_to_unix($unix_datetime);
    }
    if (!$unix_datetime) {
        $unix_datetime = now();
    }
    $day = get_week_day_time($unix_datetime, 7);
    return date('Y-m-d', $day + 86400);
}


function week_first_day($unix_datetime = false)
{
    if (is_string($unix_datetime)) {
        $unix_datetime = human_to_unix($unix_datetime);
    }
    if (!$unix_datetime) {
        $unix_datetime = now();
    }

    return date('Y-m-d', get_week_day_time($unix_datetime));
}

// 取得本周星期n的开始时间,7为周末
function get_week_day_time($time, $weekday = 1, $needtime = false)
{
    $index = weekday($time);

    $daytime = strtotime(date('Y-m-d', $time));

    $weekdaytime = $daytime - 86400 * ($index - $weekday);
    if ($needtime) {//如果需要不同天的同一时刻的表示，needtime设置为true
        $weekdaytime += ($time - $daytime);
    }
    return $weekdaytime;
}

// 取得一个时间的
function get_same_week_day_time($time, $now, $needtime = false)
{
    if (is_string($time)) {
        $time = human_to_unix($time);
    }
    if (is_string($now)) {
        $now = human_to_unix($now);
    }
    $index = weekday($time);
    $newday = get_week_day_time($now, $index, false);
    if ($needtime) {
        $newday = $newday + ($time - strtotime(date('Y-m-d', $time)));
    }
    return $newday;

}



/* End of file date_helper.php */
/* Location: ./system/helpers/date_helper.php */
