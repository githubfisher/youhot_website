#!/bin/sh

##文件转换服务定时程序

i=0;
cd ../

while [ 1 ]
do
  log=shell/logs/convert_log_`date +%Y%m%d`.txt
  snaplog=shell/logs/snap_log_`date +%Y%m%d`.txt

  i=`expr $i + 1`

  tt=`date`
  echo $tt" round "$i "..." >> $log

  t1=`date +'%s'`
  php index.php tool check_untrans_files >> $log
  t2=`date +'%s'`
  php index.php tool check_file_convert_status >> $log
  t3=`date +'%s'`

#文件截图，慢慢做
  php index.php tool resource_snap>> $snaplog
   php index.php tool update_resource_snap>> $snaplog

  t4=`date +'%s'`

  dt1=`expr $t2 - $t1`
  dt2=`expr $t3 - $t2`
  dt3=`expr $t4 - $t3`

  tt=`date`
  echo $tt" round "$i" timeused "$dt1"s/"$dt2"s/"$dt3"s , done." >> $log

  sleep 10

done

