#!/bin/sh

# 短信发送脚本
# @author mbo
i=0;
cd ../
while [ 1 ]
do

# 使用第三方短信平台
  php index.php sms listen

# 内网机
#  php index.php sms index
  #接收短信
  #php index.php sms receive_mms
  sleep 10
  i=`date +%Y%m%d%H%M%S`
  echo "sms runtime:"$i >> shell/logs/log_sms_`date +%Y%m%d`.txt

done

