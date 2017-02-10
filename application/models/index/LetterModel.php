<?php
//私信视图
defined('BASEPATH') OR exit('No direct script access allowed');
class LetterModel extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function letterlist($uid,$page,$offset){
		
		
		/*分页处理  */
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/index.php/Operate/Letter/';
		$config['per_page'] = $page;
		$this->db->where('uid',$uid);
		$config['total_rows'] = $this->db->count_all_results('letter');
		$config['first_link'] = '第一页';
		$config['last_link'] = '最后一页';
		$config['next_link'] = '下一页';
		$config['prev_link'] = '上一页';
		$this->pagination->initialize($config);
		$this->db->select('letter.id,letter.content,letter.time,userinfo.face50,userinfo.uid,user.nickname',false);
		$data=$this->db->from('letter')
		->limit($page, $offset)
		->order_by('time','desc')
		->where('letter.uid',$uid)
		->join('userinfo','letter.from=userinfo.uid','left')
		->join('user','letter.from=user.userid','left')
		->get()->result_array();
		
		return $data;
		
		
	}
	
	
	
}