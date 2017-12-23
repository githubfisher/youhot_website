#!/bin/sh

#这里是做个记录，拷贝到命令行直接执行。
yesday=`date -d "-1 day" "+%Y-%m-%d"`;
logfile="aifudao.access.$yesday.log";
logfile_gri="/home/aifudao/bp/shell/data/login_user_$yesday.txt";
logfile_neil="/home/aifudao/bp/shell/data/login_user_neil_$yesday.txt";
scp aifudao@grissom.aifudao.com:apache/logs/$logfile $logfile_gri;
scp aifudao@neil.aifudao.com:apache/logs/$logfile $logfile_neil;
cat $logfile_neil >> $logfile_gri;
awked_login_user="/home/aifudao/bp/shell/data/login_user.txt";
echo "" > $awked_login_user;
grep "/user/login?from=an_pad" $logfile_gri|awk -F '"' '{print $2"&"$6}' |awk -F '&' '{print $3"; "$7}'|awk -F ';' '{if($2 ~ /Dalvik/){print $1";"$5} else {print $0}}' >> $awked_login_user;

rm $logfile_gri;
rm $logfile_neil;

php /home/aifudao/bp/index.php tools user get_device_from_txt  >> /home/aifudao/bp/shell/logs/get_device_from_txt.log
