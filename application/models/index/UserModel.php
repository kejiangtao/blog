<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class UserModel extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	/*验证登录名和昵称  */
	 public function get_yanzheng($filed,$data){
	 	$this->db->where($filed,$data);
	 	$query = $this->db->get('user')->result_array();
	 	return $query;
	 }
	 /*注册用户  */
	 public function inset_user($table,$data){
	 	$data['createtime'] = time();
	 	$data['createip'] = $this->input->ip_address();
	 	$data['updatetime'] = time();
	 	$data['updateip'] = $this->input->ip_address();
	 	 $this->db->insert($table, $data);
	 	$re = $this->db->insert_id();
	 	return $re;
	 }
	 /*查找用户基本信息  */
	 public function get_userinfo($filed,$data){
	 	$this->db->where($filed,$data);
	 	$data=$this->db->from('user')
	 	->join('userinfo','user.userid=userinfo.uid','left')
	 	->get()->result_array();
	 	
	 	return $data;
	 }
	 /*修改基本信息  */
	 public function edit_userinfo($userid,$data,$data1){
	 	
	 	$where1 = array('userid',$userid);
	 	$this->db->update('user', $data, $where1);
	 	$chk = $this->db->affected_rows();
	 	if($data1){
	 		
	 		$this->db->where('uid',$userid);
	 		$userInfo = $this->db->get('userinfo')->result_array();
	 		if($userInfo){
	 			$where2 = array('id',$userInfo[0]['uid']);
	 			$this->db->update('userinfo', $data1, $where2);
	 		}else{
	 			$data1['uid'] = $userid;
	 			$this->db->insert('userinfo', $data1);
	 			
	 		}
	 	}
	 	
	 	return ;
	 }
	 
} 