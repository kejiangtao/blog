<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*分组管理控制器  */
class Group extends MY_Controller{
	public function __construct(){
		parent::__construct();
	}
	/*添加分组  */
	public function add(){
		$name = $this->input->post('name');
		$sql = "insert into bl_group (uid,name) values(?,?)";
		$this->db->query($sql,array($this->__userid,$name));
		$re = $this->db->affected_rows();
		if($re){
			echo json_encode(array(
					'status'=>$re,
					'msg'=>'添加成功'
			));
		}else{
			echo json_encode(array(
					'status'=>0,
					'msg'=>'添加失败'
			));
		}
	}
}