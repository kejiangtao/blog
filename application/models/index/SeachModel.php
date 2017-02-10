<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class SeachModel extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	//搜索用户
	public function SeachUser($where,$page,$offset,$keyword){
		
		/*分页处理  */
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/index.php/Seach/sechUser/'.$keyword;
		$config['per_page'] = $page;
		$this->db->where($where);
		$config['total_rows'] = $this->db->count_all_results('user');
		
		$config['first_link'] = '第一页';
		$config['last_link'] = '最后一页';
		$config['next_link'] = '下一页';
		$config['prev_link'] = '上一页';
		$this->pagination->initialize($config);
		$this->db->select('user.nickname,userinfo.sex, userinfo.location, userinfo.intro, userinfo.face80, userinfo.follow,userinfo.fans, userinfo.weibo, userinfo.uid',false);
		$data=$this->db->from('user')
		->limit($page, $offset)
		->order_by('user.userid','desc')
		->where($where)
		->join('userinfo','user.userid=userinfo.uid','left')
		->get()->result_array();
		
		return $data;
		
	}
	//搜索微博
	
	public function SeachWeibo($where,$page,$offset,$keyword){
		
			
			$this->db->where($where);
			/*分页处理  */
			$this->load->library('pagination');
			$config['base_url'] = base_url().'/index.php/Seach/sechWeibo/'.$keyword;
			$config['per_page'] = $page;
			$config['total_rows'] = $this->db->count_all_results('weibo');
			$config['first_link'] = '第一页';
			$config['last_link'] = '最后一页';
			$config['next_link'] = '下一页';
			$config['prev_link'] = '上一页';
		
			$this->pagination->initialize($config);
			$this->db->select('weibo.id,weibo.uid,weibo.content,weibo.isturn,weibo.time,weibo.turn,weibo.keep,weibo.comment,userinfo.face50,picture.mini,picture.medium,picture.max,keep.id as kid,keep.time as ktime',false);
			$data=$this->db->from('weibo')
			->where($where)
			->limit($page, $offset)
			->order_by('time','desc')
			->join('userinfo','weibo.uid=userinfo.uid','left')
			->join('picture','weibo.id=picture.wid','left')
			->join('keep','weibo.id=keep.wid','left')
			->get()->result_array();
			//echo $this->db->last_query();
			foreach ($data as $k=>&$v){
				$this->db->where('userid',$v['uid']);
				$this->db->select('nickname');
				$result = $this->db->get('user')->result_array();
				$v['nickname'] = $result[0]['nickname'];
				if ($v['isturn']) {
					$this->db->where('weibo.id',$v['isturn']);
					$this->db->select('weibo.id,weibo.uid,weibo.content,weibo.isturn,weibo.time,weibo.turn,weibo.keep,weibo.comment,userinfo.face50,user.nickname,picture.mini,picture.medium,picture.max',false);
					$tmp=$this->db->from('weibo')
					->order_by('time','desc')
					->join('user','weibo.uid=user.userid','left')
					->join('userinfo','weibo.uid=userinfo.uid','left')
					->join('picture','weibo.id=picture.wid','left')
					->get()->row_array();
		
					$data[$k]['isturn'] = $tmp ? $tmp : -1;
				}
			}
			return $data;
		
		}

}