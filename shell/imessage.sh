#!/bin/sh

# 消息发送, 命令query
# @author conzi
cd ../
while [ 1 ]
do
  php index.php  tools message check_cmd >> shell/log_ims_`date +%Y%m%d`.txt
  i=`date +%Y%m%d%H%M%S`
  echo "imsg runtime:"$i >> shell/log_ims_`date +%Y%m%d`.txt
  sleep 30
done

