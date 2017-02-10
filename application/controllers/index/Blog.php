<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {

	
	/*登录显示页面  */
	public function index(){
		
		$this->load->view('index/login.html');
	}
	
	/*登录验证  */
	public function Login_ajax(){
		$this->load->model('index/UserModel');
		$this->load->library('session');
		$this->load->library('encrypt');
		$this->load->helper('cookie');
		$account = $this->input->post('account');
		$password = $this->input->post('password');
		$auto     = $this->input->post('auto');
		$user = $this->UserModel->get_yanzheng('account',$account);
		$re = array();
		
		if($user){
			if(!$user[0]['status']){
				$res['code'] = 0;
				$res['msg'] = "用户已被锁定";
			}
			else
			if($user[0]['password']==md5($password)){
				/*自动登录处理  */
				//处理下一次自动登录
				if ($auto) {
					$acc= $account;
					$ip = $this->input->ip_address();
					$value = $acc . '|' . $ip;
					$value = $this->encrypt->encode($value);
					set_cookie('auto', $value, 7*24*3600);
					
				}
				/*存入session  */
				$this->session->set_userdata('userid',$user[0]['userid']);
				$res['code'] = 1;
				$res['url'] = site_url();
				
				
			}else{
				$res['code'] = 0; 
				$res['msg'] = "密码错误";
			}
		}else{
			$res['code'] = 0;
			$res['msg'] = "用户不存在";
		}
		echo json_encode($res);
		exit;
	}
	/*退出登录  */
	public function loginOut(){
	
			//卸载SESSION
			session_unset();
			session_destroy();
		
			//删除用于自动登录的COOKIE
			@setcookie('auto', '', time() - 3600, '/');
		
			//跳转致登录页
			redirect('Blog/index');
	
	}
	/*注册  页面*/
	public function register(){
		
		
		$data['code'] = $this->captcha();	
		$this->load->view('index/register.html',$data);
	}
	/*提交表单 处理 */
	public function register_info(){
		$this->load->model('index/UserModel');
		$this->load->library('session');
		$data = array();
		$data['account'] = $this->input->post('account');
		$data['nickname'] = $this->input->post('nickname');
		$data['password'] = md5($this->input->post('password'));
		$re = $this->UserModel->inset_user('user',$data);
		if($re){
			$data1['uid'] = $re;
			$this->db->insert('userinfo',$data1);
			$this->session->set_userdata('user',$data);
			
			echo json_encode(array(
					'message' => $re,
					'url'	  =>site_url('User/index')		
			));
		}else{
			echo json_encode(array(
					'message' => $re,
			));
			exit;
		}
	}
	/*表单验证  */
	public function yanzheng(){
		$this->load->model('index/UserModel');
		$this->load->library('session');
		$account = $this->input->post('account');
		$nickname = $this->input->post('nickname');
		$verify = $this->input->post('verify');
		if($account){
			$query = $this->UserModel->get_yanzheng('account',$account);
			
			if($query){
				echo json_encode(array(
						'valid' => false,
				));
				exit;
			}else{
				echo json_encode(array(
						'valid' => true,
				));
			}
		}
		if($nickname){
			$query1 = $this->UserModel->get_yanzheng('nickname',$nickname);
			if($query1&&$this->session->userid==$query1[0]['userid']){
				echo json_encode(array(
						'valid' => true,
				));
				exit;
			}
			if($query1){
				echo json_encode(array(
						'valid' => false,
				));
				exit;
			}else{
				echo json_encode(array(
						'valid' => true,
				));
			}
		}
		
		if($verify){
			$yanzhengma = $this->session->code;
			
			if(strtolower($yanzhengma)!=strtolower($verify)){
				
				echo json_encode(array(
						'valid' => false,
				));
			}else{
				echo json_encode(array(
						'valid' => true,
				));
			}
		}
	}
	/*验证码  */
	public function captcha(){

		$this->load->library('session');
		 $this->load->helper('captcha');
		$vals = array(
		
				'img_width' => '80',
				'img_path'  => './captcha/',
				'font_path' => './system/fonts/texb.ttf',
				'img_url'   => 'http://localhost/Blog/captcha/',
				'word_length'   => 4,
		
					
		);
		$cap = create_captcha($vals);
		
		$this->session->set_userdata('code',$cap['word']);
		if($this->input->is_ajax_request()){
			echo 'http://localhost/Blog/captcha/'.$cap['filename'];
			exit;
		}else{
			return  'http://localhost/Blog/captcha/'.$cap['filename'];
		}
	}
}