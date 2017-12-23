ps -ef|grep nohup

nohup sh payoff.sh &
nohup sh paycheck.sh &
nohup sh sms_runner.sh &
nohup sh appointment_remind.sh &
nohup sh fileconvert.sh &
nohup sh APNS_message.sh &
nohup sh question_push.sh &
nohup sh imessage.sh &
nohup sh cmd_check.sh &
nohup sh user.sh &

ps -ef|grep .sh
