#!/bin/sh

# 用户相关的处理脚本
# @author conzi
i=0;
cd ../
while [ 1 ]
do

   log=shell/user_log_`date +%Y%m%d`.txt

  #自动分配新学生
 # php index.php tools user student_assign >> $log

  #自动登录的学生，自动分配老师
  php index.php tools user update_user_teacher >> $log


  i=`expr $i + 1`
  echo "runtime:"$i >> $log

  sleep 1m
done

