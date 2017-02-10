<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Controller extends CI_Controller{
	protected  $__userid;
	public function __construct(){
		parent::__construct();
		$this->load->model('index/UserModel');
		$this->load->library('encrypt');
		$this->load->helper('cookie');
		$userid = $this->session->userid;
		$this->__userid = $userid;
		//处理自动登录
		if (isset($_COOKIE['auto']) && !$userid) {
			
			$value = explode('|', $this->encrypt->decode($_COOKIE['auto']));

			$ip = $this->input->ip_address();
			//echo $ip;die;	
			//本次登录IP与上一次登录IP一致时
			if ($ip == $value[1]) {
				
				$account = $value[0];
				$user  = $this->UserModel->get_yanzheng('account',$account);
				
				//检索出用户信息并且该用户没有被锁定时，保存登录状态
				if ($user && $user[0]['status']) {
					$this->session->set_userdata('userid',$user[0]['userid']);
				}
			}
		}
		if(!$this->session->userid){
			redirect('Blog/index');
		}
	
	}
	
	/**
	 * 往内存写入推送消息
	 * @param [int] $uid  [用户ID号]
	 * @param [int] $type [1：评论；2：私信；3：@用户]
	 * @param [boolean] $flush  [是否清0]
	 */
	function set_msg ($uid, $type, $flush=false) {
		
		$this->load->driver('cache', array('adapter' => 'redis', 'backup' => 'file'));
		
		$name = '';
		switch ($type) {
			case 1 :
				$name = 'comment';
				break;
	
			case 2 :
				$name = 'letter';
				break;
	
			case 3 :
				$name = 'atme';
				break;
		}
	
		if ($flush) {
			$data = $this->cache->get('usermsg' . $uid);
			$data[$name]['total'] = 0;
			$data[$name]['status'] = 0;
		
			$this->cache->save('usermsg' . $uid, $data, 0);
			return;
		}
	
		//内存数据已存时让相应数据+1
		
		if ($this->cache->get('usermsg' . $uid)) {
			
			$data = $this->cache->get('usermsg' . $uid);
		
			$data[$name]['total']++;
			$data[$name]['status'] = 1;
			
			$this->cache->save('usermsg' . $uid, $data, 0);
			
	
			//内存数据不存在时，初始化用户数据并写入到内存
		} else {
			$data = array(
					'comment' => array('total' => 0, 'status' => 0),
					'letter' => array('total' =>0, 'status' => 0),
					'atme' => array('total' => 0, 'status' => 0),
			);
			
			$data[$name]['total']++;
			$data[$name]['status'] = 1;
			$this->cache->save('usermsg' . $uid, $data, 0);
			
		}

		
	}
	
	
	/* 自定义提示 
	 * @param [string] $url  要跳转的地址
	 * @param [int] $messate  提示语句
	 *  @param [int] $type  error:错误，success:成功
	 * */
	function point($url,$message,$type){
		if($type == "success"){
			$color = "green";
		}else{
			$color = "red";
		}
		
		$html=<<<div
       <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	   <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
    	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    	<script type="text/javascript" src=' /Blog/Public/Js/jquery-1.7.2.min.js'></script>
    	
    	<head/>
    	<body>
		<div  style="width:40%;height:20%;text-align:center; border: 1px solid #bfb5b5; margin: 10% auto;">
       		<h2 style="font-size:24px;color:$color">$message</h2>
       		<div ><span id="down" style="color:$color">5</span>后跳转...</div>
       		<p >如果没有跳转，<a href="{$url}" style="font-size:24px;color:blue">请点击这里</a></p>
       	</div>
    
       	<script>
       		var i = 4;
       		var IntervalName = setInterval(function () {
       		 //需要定时执行的代码
       			$("#down").html(i);
       			i--
	        	if (i == 0 ) {
	            	clearInterval(IntervalName);
	       			window.location.href = "$url";
	        	};
    		}, 1000);        
			
			IntervalName;
       	</script>
		</body>
       </html>
div;
		return $html;
		}
	
}