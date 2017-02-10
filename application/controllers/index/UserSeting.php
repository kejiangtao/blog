<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class UserSeting extends MY_Controller{
	public function __construct(){
		parent::__construct();
	}
	/*修改资料页显示  */
	public function index(){
		$user  = $this->UserModel->get_userinfo('userid',$this->__userid);
		$data['user'] = $user; 
		$this->load->view('index/header.html',$data);
		$this->load->view('index/nav.html');
		$this->load->view('index/seting.html');
		$this->load->view('index/bottom.html');
	}
	/*基本资料修改  */
	public function edit_user(){
		//var_dump($_POST);
		$data = array();
		$data1 = array();
		$data['nickname'] = $this->input->post('nickname'); 
		if($this->input->post('truename')){
			$data1['truename'] = $this->input->post('truename');
		}
		if($this->input->post('sex')){
			$data1['sex'] = $this->input->post('sex');
		}
		if($this->input->post('province')||$this->input->post('city')){
			$data1['location'] = $this->input->post('province')." ".$this->input->post('city');
		}
		if($this->input->post('night')){
			$data1['constellation'] = $this->input->post('night');
		}
		if($this->input->post('intro')){
			$data1['intro'] = $this->input->post('intro');
		}
		 $this->UserModel->edit_userinfo($this->__userid,$data,$data1);
		
		echo 1;exit;
	}
	/* 保存图像地址到数据库 */
	public function faceImg(){
		$sql = "select * from bl_userinfo where uid = ?";
		$query = $this->db->query($sql,array($this->__userid));
		$row = $query->row_array();
		if($row){
			if($row['180']==$_POST['face180']&&$row['80']==$_POST['face80']&&$row['50']==$_POST['face50']){
				echo 1;exit;
			}
			$sql1 = "update bl_userinfo set face180 = ? , face80 = ? , face50 = ? WHERE uid = ?";
			$this->db->query($sql1, array($this->input->post('face180'), $this->input->post('face80'), $this->input->post('face50'),$this->__userid));
			echo $this->db->affected_rows();exit;
		}
		else{
			$data = $_POST;
			$data['uid'] = $this->__userid;
			$this->db->insert('userinfo', $data);
			echo $this->db->affected_rows();
			exit;
		}
		
	}
	/*头像上传  */
	public function uploadFace(){
		//var_dump($_FILES);
		$config['upload_path'] = './Uploads/face';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['overwrite'] = true;
		$type = explode('.', $_FILES['face']['name']);
		$config['file_name'] = $this->__userid.'.'.$type[1];
		$this->load->library('upload',$config);
		$this->upload->do_upload('face');
		if($this->upload->display_errors()){
			echo "<script type='text/javascript'>";
			echo "parent.$('#errMsg').html('".$this->upload->display_errors('','')."');";
			echo "</script>";
		}else{
			$imgUrl = $this->trumd($config['file_name']);
			echo "<script type='text/javascript'>";
			echo "parent.$('input[name=face50]').val('".$imgUrl['min']."');";
			echo "parent.$('input[name=face80]').val('".$imgUrl['large']."');";
			echo "parent.$('input[name=face180]').val('".$imgUrl['max']."');";
			echo "</script>";  
		
		}
	}
	/*修改密码验证  */
	public function oldpwd(){
		$pwd = $this->input->post('old');
		$sql = "select * from bl_user where userid = ? ";
		$query = $this->db->query($sql,array($this->__userid));
		
		$row = $query->row_array();
		if($row['password']==md5($pwd)){
			
			echo json_encode(array(
							'valid'=>true,
							)
					);
			
		}else{
			echo json_encode(array(
					'valid'=>false,
			)
			);
		}
		
	}
	/*修改密码  */
	public function edit_pwd(){
		$pwd = $this->input->post('newp');
		$sql = "update bl_user set password = ? where userid = ?";
		$this->db->query($sql,array(md5($pwd),$this->__userid));
		echo $this->db->affected_rows();
	}
	/*图像处理类
	 * $path 图像路径
	 * $whidth 图像宽度，
	 * $height 图像高度 
	 */
	public function trumd($filename){
		$imgurl = array();
		$type = explode('.', $filename);
		/* 180*180 */
		$config['image_library'] = 'gd2';
		$config['source_image'] = './Uploads/face/'.$filename;
		$config['create_thumb'] = TRUE;
		$config['thumb_marker'] = '_max';
		$config['maintain_ratio'] = false;
		$config['width']     = '180';
		$config['height']   = '180';		
		$this->load->library('image_lib', $config);
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		$imgurl['max'] = base_url().'/Uploads/face/'.$type[0].'_max.'.$type[1];
		/* 80*80 */
		
		$config1['image_library'] = 'gd2';
		$config1['source_image'] = './Uploads/face/'.$filename;
		$config1['create_thumb'] = TRUE;
		$config1['thumb_marker'] = '_large';
		$config1['maintain_ratio'] = false;
		$config1['width']     = '80';
		$config1['height']   = '80';		
		$this->load->library('image_lib', $config1);
		$this->image_lib->initialize($config1);
		$this->image_lib->resize();
		$imgurl['large'] = base_url().'/Uploads/face/'.$type[0].'_large.'.$type[1];
		/* 50*50 */
		$config2['image_library'] = 'gd2';
		$config2['source_image'] = './Uploads/face/'.$filename;
		$config2['create_thumb'] = TRUE;
		$config2['thumb_marker'] = '_min';
		$config2['maintain_ratio'] = false;
		$config2['width']     = '50';
		$config2['height']   = '50';
		
		$this->load->library('image_lib', $config2);
		$this->image_lib->initialize($config2);
		$this->image_lib->resize();
		$imgurl['min'] = base_url().'/Uploads/face/'.$type[0].'_min.'.$type[1];
		return $imgurl;
	}
}	