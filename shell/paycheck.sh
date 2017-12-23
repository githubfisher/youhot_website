#!/bin/sh

# 第三方结算对帐脚本，主要用于核算数据是否正确,像快钱这样的，还可用于确认用户是否已支付：
# @author conzi

i=`date +%Y%m%d%H%M%S`;
cd ../
while [ 1 ]
do

# 快钱支付核帐用
  php index.php tools  account cmd_check_bill99_pay

  sleep 1m
  echo "runtime:"$i >> shell/pay_check_log_`date +%Y%m%d`.txt

done
