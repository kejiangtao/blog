<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class WeiboModel extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function weibolist($uerid,$page,$offset){
		//取得当前用户的ID与当前用户所有关注好友的ID
		$uid = array($uerid);
		$where = array('fans' => $uerid);
		
		if ($this->input->get('gid')) {
			$gid = $this->input->get('gid');
			$where['gid'] = $gid;
			$uid = array();
		}
		$this->db->where($where);
		$this->db->select('follow');
		$result = $this->db->get('follow')->result_array();
		
		if ($result) {
			foreach ($result as $v) {
				$uid[] = $v['follow'];
			}
		} 
		//var_dump($uid);exit;
		$this->db->where_in('weibo.uid',$uid);
		/*分页处理  */
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/index.php/User/index/';
		$config['per_page'] = $page;
		$config['total_rows'] = $this->db->count_all_results('weibo');
		$config['first_link'] = '第一页';
		$config['last_link'] = '最后一页';
		$config['next_link'] = '下一页';
		$config['prev_link'] = '上一页';
		
		$this->pagination->initialize($config);
		$this->db->select('weibo.id,weibo.uid,weibo.content,weibo.isturn,weibo.time,weibo.turn,weibo.keep,weibo.comment,userinfo.face50,picture.mini,picture.medium,picture.max',false);
		$data=$this->db->from('weibo')
		->limit($page, $offset)
		->order_by('time','desc')
		->join('userinfo','weibo.uid=userinfo.uid','left')
		->join('picture','weibo.id=picture.wid','left')
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
	//评论模型
	public function commentlist($wid,$page,$offset){
		/*分页处理  */
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/index.php/Operate/comment/';
		$config['per_page'] = $page;
		if(!$wid){
			return;
		}
		$this->db->where_in('wid',$wid);
		$config['total_rows'] = $this->db->count_all_results('comment');
		$config['first_link'] = '第一页';
		$config['last_link'] = '最后一页';
		$config['next_link'] = '下一页';
		$config['prev_link'] = '上一页';
	
		$this->pagination->initialize($config);
		$this->db->select('comment.id,comment.wid,comment.content,,comment.time,userinfo.face50,userinfo.uid,user.nickname',false);
		$data=$this->db->from('comment')
		->limit($page, $offset)
		->order_by('time','desc')
		->where_in('wid',$wid)
		->join('userinfo','comment.uid=userinfo.uid','left')
		->join('user','comment.uid=user.userid','left')
		->get()->result_array();
		
		return $data;
	}
	//@我的微博
	public function atmelist($wid,$page,$offset){
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/index.php/Operate/atme/';
		$config['per_page'] = $page;
		if(!$wid){
			return;
		}
		$this->db->where_in('wid',$wid);
		$config['total_rows'] = $this->db->count_all_results('atme');
		$config['first_link'] = '第一页';
		$config['last_link'] = '最后一页';
		$config['next_link'] = '下一页';
		$config['prev_link'] = '上一页';
		$this->pagination->initialize($config);
		$this->db->select('weibo.id,weibo.uid,weibo.content,weibo.isturn,weibo.time,weibo.turn,weibo.keep,weibo.comment,userinfo.face50,user.nickname,picture.mini,picture.medium,picture.max',false);
		$data=$this->db->from('weibo')
		->limit($page, $offset)
		->order_by('time','desc')
		->where_in('weibo.id',$wid)
		->join('userinfo','weibo.uid=userinfo.uid','left')
		->join('user','weibo.uid=user.userid','left')
		->join('picture','weibo.id=picture.wid','left')
		->get()->result_array();
		//var_dump($data);exit;
	
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
		//var_dump($data);exit;
		return $data;
	}
	//收藏模型
	public function keeplist($wid,$page,$offset){
		/*分页处理  */
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/index.php/Operate/keep/';
		$config['per_page'] = $page;
		if(!$wid){
			return;
		}
		$this->db->where_in('wid',$wid);
		$config['total_rows'] = $this->db->count_all_results('keep');
		$config['first_link'] = '第一页';
		$config['last_link'] = '最后一页';
		$config['next_link'] = '下一页';
		$config['prev_link'] = '上一页';
		$this->pagination->initialize($config);
		$this->db->select('weibo.id,weibo.uid,weibo.content,weibo.isturn,weibo.time,weibo.turn,weibo.keep,weibo.comment,userinfo.face50,user.nickname,picture.mini,picture.medium,picture.max,keep.id as kid,keep.time as ktime',false);
		$data=$this->db->from('weibo')
		->limit($page, $offset)
		->order_by('keep.time','desc')
		->where_in('weibo.id',$wid)
		->join('userinfo','weibo.uid=userinfo.uid','left')
		->join('user','weibo.uid=user.userid','left')
		->join('picture','weibo.id=picture.wid','left')
		->join('keep','weibo.id=keep.wid','left')
		->get()->result_array();
		//var_dump($data);exit;
	
		foreach ($data as $k=>&$v){
			$this->db->where('userid',$v['uid']);
			$this->db->select('nickname');
			$result = $this->db->get('user')->result_array();
			$v['nickname'] = $result[0]['nickname'];
			if ($v['isturn']) {
				$this->db->where('weibo.id',$v['isturn']);
				$this->db->select('weibo.id,weibo.uid,weibo.content,weibo.isturn,weibo.time,weibo.turn,weibo.keep,weibo.comment,userinfo.face50,user.nickname,picture.mini,picture.medium,picture.max,keep.id as kid,keep.time as ktime',false);
				$tmp=$this->db->from('weibo')
				->order_by('keep.time','desc')
				->join('user','weibo.uid=user.userid','left')
				->join('userinfo','weibo.uid=userinfo.uid','left')
				->join('picture','weibo.id=picture.wid','left')
				->join('keep','weibo.id=keep.wid','left')
				->get()->row_array();
		
				$data[$k]['isturn'] = $tmp ? $tmp : -1;
			}
		}
		//var_dump($data);exit;
		return $data;
	}
}