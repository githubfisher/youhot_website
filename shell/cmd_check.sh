#!/bin/sh

# 检查php进程是否僵死
while [ 1 ]
do
 php cmd_runing_check.php >> logs/check_log_`date +%Y%m%d`.txt

 sleep 20m

done

