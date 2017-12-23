#!/bin/sh

# 自动结算脚本，实现功能：
# http://i.aifudao.com:8080/w/projects/bp/mrd_fudao_payoff/
# 不包括其中的核查部分
# @author mbo
i=0;
cd ../
while [ 1 ]
do

#  php index.php rfudao student_payoff
#  php index.php rfudao relation
#  php index.php rfudao class_relation
  php index.php rfudao class_booked_student_pay
  php index.php rfudao class_student_pay

#课堂费用除按次辅导外，都手动结算
#  php index.php rfudao class_teacher_pay


#新的计费方式
   php index.php sys_auto_payoff pay_check

# vip计时
   php index.php sys_auto_payoff vip_time_pay_off

#新的关系计算方式

   php index.php sys_auto_payoff scan_and_set_relation

 #答题计豆
  php index.php rfudao question_session_pay
  php index.php rfudao question_session_comment_pay

 #辅导计豆
  php index.php rfudao student_doudou_pay
  php index.php rfudao doudou_pay

  sleep 1m
  i=`expr $i + 1`
  echo "runtime:"$i >> shell/log_`date +%Y%m%d`.txt

done

