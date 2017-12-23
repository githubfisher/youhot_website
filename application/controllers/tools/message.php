<?php
/**
 * 执行脚本, 消息相关的处理
 * 部署在apollo上，通过shell运行
 *
 */

class Message extends Normal_Controller {

	public function __construct() {
		parent::__construct ();
		$this->load->model('message_model','message');
	}



   function getRedis(){
    	$this->load->library('Redis', array(),'redis' );
    	return $this->redis->getRedis();
    }

	function send_imessage($data){
		$msg = json_decode($data['body'], true);
		if($msg['from'] == '' || $msg['to'] == '' || $msg['message'] == '') {
			$this->message->send_done($data['id'],3);
			log_message('debug', '[tools:message][send_imessage]success,数据不正确 id:'.$data['id']);
		}
		else if($this->message->send_ims($msg['from'],$msg['to'],$msg['message']) == DB_OPERATION_OK){
			$this->message->send_done($data['id'],1);
			log_message('debug', '[tools:message][send_imessage]success,消息发送成功'.$data['id']);
		}
		else{
			log_message('debug', '[tools:message][send_imessage]fail, we will try again! id:'.$data['id']);
		}
	}

	function fp_err_con_info($data){
		$msg = json_decode($data['body'], true);
		$info = '';
		switch ($msg['message']) {
			case '1':
				//服务器获取sessionid出错，
				$info = sprintf('因服务器错误(1)，@%s 与你的辅导没有正常建立,你可以主动与他联系', $msg['from']);
				# code...
				break;
			case '2':
				$info = sprintf('因学生费用不足，你与@%s 的辅导没有正常进行，你可以通知他续费后重新进行辅导', $msg['from']);
				break;

			case '4': //老师忙
			case '7': //老师掉线
				$info = sprintf('因你忙或不在线，@%s 的辅导请求没能被响应，你可以尝试与他联系', $msg['from']);
				break;
			case '5':
				$info = sprintf('因你没有响应学生@%s 的辅导请求，辅导没有正常进行，你可以尝试与他联系', $msg['from']);
				break;
			case '8':
			case '9':
				$info = sprintf('因你的网络或程序异常(%d)，你与@%s 的辅导没有正常进行，你可以与他联系重试', $msg['message'], $msg['from']);
				break;
			case '11':
			case '12':
				$info = sprintf('因学生的网络或程序异常(%d)，你与@%s 的辅导没有正常进行，你可以与他联系重试', $msg['message'], $msg['from']);
				break;
			case '22':
			 /* 暂时关闭（刘华），科信通审核通过后再打开  刘华
				$info = sprintf('因学生豆豆不足，你与@%s 的辅导没有正常进行，你可以通知他兑换豆豆后重新进行辅导', $msg['from']);
				$info_mabo = sprintf('学生豆豆不足，@%s 老师与@%s 的辅导没有正常进行，请及时跟进', $msg['to'], $msg['from']);
				$this->message->send('admin', 'fu-mabo', $info_mabo);
				$this->load->model('op_user_review_model', 'review');
				$this->load->model('mobile_msg_model', 'sms');

				$follower= $this->review->get_user_follower($msg['from']);

				if($follower){
					$this->sms->send_message($follower, $info_mabo);
					$this->message->send('admin', get_maped_username($follower), $info_mabo);
				}

				$this->message->send('admin', $msg['from'],'【系统提醒】因你的豆豆不足，暂无法进行更多的答疑。你可以打开“我的豆豆”了解如何获得更多豆豆，也可以参考：http://www.aifudao.com/blog/archives/929 ');
				*/
				break;
			case '3'://拒绝
			case '6'://接受后取消
				//老师拒绝
				break;
		}

		if($msg['from'] == '' || $msg['to'] == '') {
			$this->message->send_done($data['id'],3); // 设置数据错误，不再处理
			log_message('debug', '[tools:message][fp_error_console_info]success,数据不正确 id:'.$data['id']);
		}
		else if(!empty($info)){
			if($this->message->send_ims('admin',$msg['to'],'【系统消息】'.$info) == DB_OPERATION_OK){ //系统消息
				$this->message->send_done($data['id'],1);
				log_message('debug', '[tools:message][fp_error_console_info]success,消息发送成功'.$data['id'].$info );
			}else{
				log_message('debug', '[tools:message][fp_error_console_info]fail :'.$data['id'].$info );
			}
		}
		else{
			$this->message->send_done($data['id'],2);//忽略
		}
	}

// 主函数，定时检查
	function check_cmd(){
		$query = $this->message->db_master->where('status',0)
										  ->limit(40)
										  ->get(TBL_CMD);
		$res = get_query_result($query);
		foreach ($res as $row) {
			if(method_exists($this, $row['cmd'])){
				$this->$row['cmd']($row);
			}else{
				$this->message->send_done($row['id'],2); //未定义方法的，忽略掉
			}
		}
	}





// 更新首页推荐辅导
	function fudao_rec_update(){
		$this->load->helper('file_helper');
		$this->load->model('fp_session_model', 'fpsess');
		$rows = $this->fpsess->get_rec_fudao(0,6);
		@write_file('images/conf/fudao_rec.php', json_encode($rows));
	}
// 更新首页老师推荐

	function teacher_rec_update(){
		$this->load->helper('file_helper');
		$this->load->model('teacher_model', 'teacher');
		// todo
		$list = $this->teacher->get_index_rec_teachers(0,12);
		@write_file('images/conf/teacher_rec.php', json_encode($list));

	}

	/**
	 * 定时脚本使用，不要访问
	*/
	public function check_queue_message(){
		$this->load->helper('file');

		$temp_file_path = '_msg_r_count.txt';
		$last_message_id = read_file($temp_file_path);

		$redis = $this->getRedis();
		if(!$last_message_id){
			$last_message_id = 1;
		}
		$new_id = $redis->llen('cmd_list');
		//$new_id = $redis->get('cmd_id');
		echo "$new_id: $last_message_id";

		if($last_message_id > $new_id){
			$last_message_id = $new_id;
			write_file($temp_file_path,$last_message_id);
		}
		if($last_message_id >= $new_id){
			return ;
		}

		$res = $redis->lrange('cmd_list', $last_message_id-1 , -1);
		// var_dump($res);

		if(!empty($res)){
			foreach ($res as $msg) {

				$cmd = json_decode($msg, true);

				if(is_array($cmd)  && ($cmd['cmd'] == 'remove_face'||$cmd['cmd'] =='remove_auth') ){
					//files = [file1, file2,file3];
					foreach ($cmd['files'] as $f ) {
						// echo "files : $f \n";
						$path ='images/'.($cmd['cmd'] == 'remove_face'?'face':'auth');
						if(file_exists($path.'/'.$f)){
							$ls = `rm -f $path/$f`;
							echo "[cmd][{$cmd['cmd']}]remove file :{$f}\n";

							log_message('debug','[queue][cmd]remove file :'.$f);
						}
					}
				}

				if(is_array($cmd)  && ($cmd['cmd'] == 'update_file') ){

					//files = [{bcs_path:xxx,path:images/conf/*.php}];
					foreach ($cmd['files'] as $file) {
						$file_data = get_file_via_curl($file['bcs_path']);
						$file_path = $file['path'];
						if(strpos($file_path,'images') ===  0  && !empty($file_data) ){
							if(file_exists($file_path)){
								$ls = `rm -f $file_path`;
								echo "[cmd][{$cmd['cmd']}]remove file :{$file_path}\n";
							}
							write_file($file_path, $file_data);
							log_message('debug','[queue][cmd]update_file :'.json_encode($file));
						}
					}

				}

				if(is_array($cmd)  && ($cmd['cmd'] == 'fudao_rec_update') ){
					$this->fudao_rec_update();
					log_message('debug','[queue][cmd]fudao_rec_update');
				}

				if(is_array($cmd)  && ($cmd['cmd'] == 'teacher_rec_update') ){
					$this->teacher_rec_update();
					log_message('debug','[queue][cmd]teacher_rec_update');
				}
			}
			if(!write_file($temp_file_path,$new_id)){
				//echo 'error_to_write to file';
			}
		}
	}

	// 定时脚本，判断query_msg是不是过期了，如果过期，重置一下记录文件
	function check_query_overtime(){
		exit();
		$this->load->helper('file');
		$temp_file_path = '_msg_count.txt';
		$last_message_id = read_file($temp_file_path);
		if(!$last_message_id){
			$last_message_id = 1;
		}
		write_file($temp_file_path,intval($last_message_id) -1);
		$this->check_queue_message();

		$new_id = read_file($temp_file_path);
		if(intval($new_id)  == intval($last_message_id) - 1){
			// 大于或等于都是正常的，只有不变才奇怪
			//队列重置
			log_error('[queue][cmd] query reset !');
			write_file($temp_file_path,1);
		}

	}

// 磁盘报警
	function mon_disk($server = 'neil'){
	  	exec(" df -P |awk '{print $5}'",$out);
		$this->load->model('mobile_msg_model','sms');
        $msg_flag = false;
        $msg_content = $server."hard space is almost full!! info: ";
        foreach($out as $line){
            $line = (int) str_replace('%','',$line);
            if($line >= 95){
                $msg_flag = true;
                $msg_content .= $line.'%';
                break;
            }
        }
        if($msg_flag){
            $this->_notify_administrator($msg_content);
        }

	}
/*
监控文件 /home/aifudao/bmon/bmon.out，看最后几行（注意最后一行可能不完整，所以需要舍弃），如果有KiB前面的数字大于1.3M，就发出报警，报警时格式如下：“grissom 带宽占用报警，信息为：214.35KiB    2290.0    310.72KiB    1880.0” 。


*/
// 宽带报警
//每一分钟执行一次
	function mon_network_load($server = 'neil',$debug=false){
	  	exec(" tail -60  /home/aifudao/bmon/bmon.out",$out);
        $msg_flag = false;
        $msg_content = $server."宽带占用报警，信息为： ";
        foreach($out as $key=>$line){
            $line = preg_replace('/\s+/',' ',$line);
            $_line = explode(' ',$line);
            if(count($_line)<4) {continue;}
            $_line[1] = (int) str_replace('Kib','',$_line[1]);
            $_line[3] = (int) str_replace('Kib','',$_line[3]);

            if($_line[1] >= 1300 or $_line[3]>=1300){
                $msg_flag = true;
                $msg_content .= $line;
                break;
            }
        }
        if($msg_flag){
            $this->_notify_administrator($msg_content,$debug);
        }

	}
//再监控一个文件，/home/aifudao/bmon/nmonitor.out，看最后几行（注意最后一行可能不完整，所以需要舍弃），如果出现行不包含“64 bytes from”，或者出现“64 bytes from 220.181.112.244: icmp_req=26 ttl=57 time=2.10 ms”中的time域大于20ms即报警，报警格式如下：“grissom 网络不稳定，ping不通或者超时 3 次，请立刻跟进处理。”（3为符合条件的行数）
//每一分钟执行一次
	function mon_ping($server = 'neil',$debug=false){
	  	exec(" tail -60  /home/aifudao/bmon/nmonitor.out",$out);
        $msg_flag = false;
        $msg_content = $server."网络不稳定，ping不通或者超时 3 次，请立刻跟进处理";

        $times = 0;
        foreach($out as $key=>$line){

            $has_64byte = preg_match('/64 bytes from /i',$line);

            if(!$has_64byte) {
               $times ++;
            }
            if(preg_match('/time=(\d+.\d+) ms/',$line,$time)){
                if( (float) $time[1] >20){
                    $times++;
                }
            }
            if($times >= 3){
                $msg_flag = true;
                break;
            }
        }
        if($msg_flag){
            $this->_notify_administrator($msg_content,$debug);
        }

	}

private function _notify_administrator($msg,$debug=false){
    if($debug){
        echo $msg;
        return;
    }
    $this->load->model('mobile_msg_model','sms');
    $this->sms->send_msg('13911190463', '短信监控:'. $msg .standard_date('DATE_MYSQL',time()));//mabo
    $this->sms->send_msg('13501149242', '短信监控:'. $msg .standard_date('DATE_MYSQL',time()));//liuhua1
    $this->sms->send_msg('18611953910', '短信监控:'. $msg .standard_date('DATE_MYSQL',time()));//liuhua2
}

/*
/*
	function mon_disk($server = 'neil'){
		$warning_limit  = 20000000;//20G
		$low_limit = 2500000;//2.5G
		$this->load->model('mobile_msg_model','sms');

		$disk = '/';
		if($server == 'apollo'){
			$disk = '/home';
			$warning_limit = 5000000; //5G
		}
	  	exec("df -P -k $disk |grep $disk |awk '{print $4}'",$out);
	    $size = (int)$out[0];
		if($size < $warning_limit && $size > $low_limit){
			$this->sms->send_message('conzi', "$server 硬盘空间仅剩$size K 不足 $warning_limit K,请尽快处理");
		}
		elseif($size < $low_limit){
			$this->sms->send_message('conzi',  "error!! $server 硬盘空间仅为$size K 不足 $low_limit K，请尽快处理");
		}

	}
*/

}
