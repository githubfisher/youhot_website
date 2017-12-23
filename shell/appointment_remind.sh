#!/bin/sh

# 预约前15分钟提醒脚本，实现功能在对应的php里面，这里无限循环这个脚本：
# @author mbo
# @date 2012-02-15
i=0;
cd ../
while [ 1 ]
do
  php index.php rfudao apt_remind
  sleep 1m
  i=`expr $i + 1`
  echo "rfudao-apt_remind runtime:"$i >> shell/log_`date +%Y%m%d`.txt

done

