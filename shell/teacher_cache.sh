#!/bin/sh

# 把老师数据缓存为静态json文件
# 除了运行这个脚本外，还需要设置crontab每天自动更新一次老师数据
# @author mbo 
cd ../
while [ 1 ]
do

  php index.php  tools teacher generate_teacher_list_with_status >> shell/logs/log_tcache_`date +%Y%m%d`.txt
  i=`date +%Y%m%d%H%M%S`
  echo "teacher cache runtime:"$i >> shell/logs/log_tcache_`date +%Y%m%d`.txt

 # 1秒钟刷新1次，
 sleep 1 

done

