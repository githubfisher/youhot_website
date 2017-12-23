#!/bin/sh

# 日历安排提醒
# 课程提醒
# @author conzi
cd ../
while [ 1 ]
do

  php index.php  tools calendar calendar_notice>> shell/log_ims_`date +%Y%m%d`.txt
  php index.php  tools calendar class_check>> shell/log_ims_`date +%Y%m%d`.txt
  i=`date +%Y%m%d%H%M%S`
  echo "calendar notice and class check runtime:"$i >> shell/log_ims_`date +%Y%m%d`.txt

 # 5分钟检查一次，
 sleep 300

done

