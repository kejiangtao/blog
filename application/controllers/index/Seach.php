<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Seach extends MY_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('index/SeachModel');
	}
	/**
	 * 搜索找人
	 */
	Public function sechUser () {
		
		$keyword = $this->_getKeyword();
		$data['keyword'] = $keyword;
		if ($keyword){
			//检索出除自己外呢称含有关键字的用户
			$where = "userid != ".$this->__userid." and nickname like '%".$keyword."%'" ;
			$page = 2;
			$offset = $this->uri->segment(4)?$this->uri->segment(4):0;
			$result = $this->SeachModel->SeachUser($where,$page,$offset,$keyword);
			
			$result = $this->_getMutual($result);
	
			//分置搜索结果到视图
			
			
			$data['result'] = $result;
		}
	
		$this->load->view('index/header.html',$data);
		$this->load->view('index/sechUser.html');
		

	}
	
	/**
	 * 搜索微博
	 */
	Public function sechWeibo () {
		$keyword = $this->_getKeyword();
		$data['keyword'] = $keyword;
		if ($keyword) {
			//检索含有关键字的微博
			$where = "content like '%".$keyword."%'";
				
			$page = 10;
			$offset = $this->uri->segment(4)?$this->uri->segment(4):0;
			$result = $this->SeachModel->SeachWeibo($where,$page,$offset,$keyword);
			//var_dump($result);exit;
			//页码
			$data['weibo'] = $result;
			
		}
		$this->load->view('index/header.html',$data);
		$this->load->view('index/sechWeibo.html');
		$this->load->view('index/bottom.html');
	}
	
	/**
	 * 返回搜索关键字
	 */
	Private function _getKeyword () {
		//$keyword ="";
		if(isset($_GET['keyword'])){
			return $_GET['keyword'] == '搜索微博、找人' ? NULL : $this->input->get('keyword');
		}else{
			return $this->uri->segment(3) == '搜索微博、找人' ? NULL : $this->uri->segment(3);
		}
		
	}
	
	/**
	 * 重组结果集得到是否互相关注与是否已关注
	 * @param  [Array] $result [需要处理的结果集]
	 * @return [Array]         [处理完成后的结果集]
	 */
	Private function _getMutual ($result) {
		if (!$result) return false;
	
		  
	
		foreach ($result as $k => $v) {
			//是否互相关注
			$sql = '(SELECT `follow` FROM `bl_follow` WHERE `follow` = ' . $v['uid'] . ' AND `fans` = ' .$this->__userid . ') UNION (SELECT `follow` FROM `bl_follow` WHERE `follow` = ' . $this->__userid  . ' AND `fans` = ' . $v['uid'] . ')';
	
			$query = $this->db->query($sql);
			$mutual = $query->result_array();
			
			if (count($mutual) == 2) {
				$result[$k]['mutual'] = 1;
				$result[$k]['followed'] = 1;
			} else {
				$result[$k]['mutual'] = 0;
	
				//未互相关注是检索是否已关注
				
				$where = "follow =".$v['uid']." and fans =".$this->__userid;
				$this->db->where($where);
				$result[$k]['followed'] = $this->db->count_all_results('follow');
			}
		}
		return $result;
	}
	}
	
	
	
	
	