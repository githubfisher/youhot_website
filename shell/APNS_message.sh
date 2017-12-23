#!/bin/sh

# 苹果信息推送

i=0;
cd ../
while [ 1 ]
do
#  php index.php sms

log=shell/logs/apns_log_`date +%Y%m%d`.txt
d=`date "+%m/%d %H:%S  %s"`
#update by conzi ,use baidu message to send sms
  php index.php message scan_and_push >> $log
  sleep 60

  i=`expr $i + 1`
  echo "apn_push runtime:$i  at $d" >> $log

done

