#!/bin/sh
#通过mysqldump备份多个库
# @autho mbo
# @date 20120216

SQL_name='aifudao cdb blog aifudao2'   #数据库名称；
#SQL_name='cdb'   #数据库名称；
SQL_pwd='qlszstsKKDD'                        #数据库密码；
BACKUP_tmp=/home/aifudao/backup/tmp     #备份文件临时存放目录；
BACKUP_path=/home/aifudao/backup           #备份文件压缩打包存放目录；
LogFile=/home/aifudao/backup/db_backup.log
echo "-------------------------------------------" >> $LogFile
echo $(date +"%y-%m-%d %H:%M:%S") >> $LogFile
echo "--------------------------" >> $LogFile
for i in $SQL_name
do
    mysqldump -uroot -p$SQL_pwd $i > $BACKUP_tmp/$i-$(date +%y%m%d%H%M).sql
    echo "$i" backup ok >> $LogFile
    sleep 3
done
sleep 10
#将备份数据打包，并删除原备份文件；
tar --remove-files -cvzf $BACKUP_path/neil_mysql_backup-$(date +%y-%m-%d).tar.gz $BACKUP_tmp/* >> $LogFile 2>&1
echo "backup succeed $BACKUP_path/neil_mysql_backup-$(date +%y-%m-%d).tar.gz" >> $LogFile
echo "-------------------------------------------" >>$LogFile
exit 0
