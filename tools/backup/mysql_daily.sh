#!/bin/sh
#ͨ��mysqldump���ݶ����
# @autho mbo
# @date 20120216

SQL_name='aifudao cdb blog aifudao2'   #���ݿ����ƣ�
#SQL_name='cdb'   #���ݿ����ƣ�
SQL_pwd='qlszstsKKDD'                        #���ݿ����룻
BACKUP_tmp=/home/aifudao/backup/tmp     #�����ļ���ʱ���Ŀ¼��
BACKUP_path=/home/aifudao/backup           #�����ļ�ѹ��������Ŀ¼��
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
#���������ݴ������ɾ��ԭ�����ļ���
tar --remove-files -cvzf $BACKUP_path/neil_mysql_backup-$(date +%y-%m-%d).tar.gz $BACKUP_tmp/* >> $LogFile 2>&1
echo "backup succeed $BACKUP_path/neil_mysql_backup-$(date +%y-%m-%d).tar.gz" >> $LogFile
echo "-------------------------------------------" >>$LogFile
exit 0
