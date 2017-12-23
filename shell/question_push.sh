#!/bin/sh

# 问题推送

i=0;
cd ../
while [ 1 ]
do
#  php index.php sms

log=shell/log_`date +%Y%m%d`.txt
d=`date "+%m/%d %H:%S  %s"`
i=`expr $i + 1`

  echo "**BEGIN check_need_push_questions  at $d" >> $log
  php index.php tool check_need_push_questions >> $log
  echo "**END check_need_push_questions , runtime:$i " >> $log
  sleep 30

done

