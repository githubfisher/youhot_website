<?php

/* 检查进程的执行时间，返回秒数 */
function  get_pid_time($pid){
    $i_pinfo = file_get_contents('/proc/'.$pid.'/stat');
    $args = explode(" ", $i_pinfo);
    $JIFFIES = $args[21];//系统执行时间

    $m_pinfo = file_get_contents('/proc/stat');
    $args = explode("\n", $m_pinfo);
    foreach($args as $v){
        $v = trim($v);
        if(strpos($v, "btime") !== false){ //系统启动时间
            $args_1 = explode(" ", $v);
            $UPTIME = $args_1[1];
            break;
        }
    }
    if($UPTIME == '' || $JIFFIES==''){
        return 0;
    }

    $START_SEC = $UPTIME + $JIFFIES/100;
    return strtotime('now') - (int)$START_SEC;
}


/*检查所有php index.php ...的进程 */

echo strtotime('now')." begin check == \n";
exec('ps -ef |grep index.php |grep -v grep|grep -v color|awk \'{print $2}\'', $output);
foreach($output as $item){
    $time = get_pid_time($item);
    if($time > 1200){
        //大于20分钟
        exec('ps '.$item , $o);
        echo $o[1]."\n";
        exec('kill '.$item);
        echo ($item)."\t".$time."\n";
    }
}
echo "== end \n\n";

