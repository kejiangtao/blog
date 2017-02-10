<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Operate extends MY_Controller{
	protected  $__userinfo;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('index/WeiboModel');
		$this->__userinfo = $this->UserModel->get_userinfo('userid',$this->__userid);
	}
	
	/**
	 * 用户个人页视图:评论
	 */
	Public function comment () {
			$data['user'] = $this->__userinfo;
		
			$this->db->where('uid',$this->__userid);
			$this->db->select('id');
			$wid = array();
			$weibo = $this->db->get('weibo')->result_array();
			$count= '' ;
			if($weibo){
				foreach($weibo as $vo){
					$wid[] = $vo['id']; 
				}
				$this->db->where_in('wid',$wid);
				$count  = $this->db->count_all_results('comment');
			}	
			
			$data['count'] = $count?$count:0;
			//清掉此类信息闪烁
			$this->set_msg ($this->__userid, 1, true);
			//分页处理
			$page = 10;
			$offset = $this->uri->segment(3)?$this->uri->segment(3):0;
			$comment = $this->WeiboModel->commentlist($wid,$page,$offset);
			$data['comment'] = $comment;
	
			$this->load->view('index/header.html',$data);
			$this->load->view('index/nav.html');
			$this->load->view('index/comment.html');
			$this->load->view('index/bottom.html');
	}
	/**
	 * 评论回复
	 */
	Public function reply () {
		
		$data = array(
				'content' => $this->input->post('content'),
				'time' => time(),
				'uid' => $this->__userid,
				'wid' => $this->input->post('wid', 'intval')
		);
		$this->db->insert('comment',$data);
		if ($this->db->affected_rows()) {
			//处理@用户
			$this->_atmeHandel($data['content'], $data['wid']);
			$this->db->set('comment', 'comment+1',FALSE);
			$this->db->where('id',$data['wid']);
			$this->db->update('weibo');
			echo 1;
		} else {
			echo 0;
		}
	}
	/**
	 * @用户处理
	 */
	Private function _atmeHandel ($content, $wid) {
		$arr = explode("：", $content);
		$arr1 = explode("@", $arr[0]);
		
		if (!empty($arr1[1])) {
			foreach ($arr1[1] as $v) {
				$this->db->where('nickname',$v);
				$this->db->select('userid');
				$uid=$this->db->get('user')->row_array();
				if ($uid) {
					$data = array(
							'wid' => $wid,
							'uid' => $uid['userid']
					);
						
					//写入消息推送
					$this->set_msg($uid['userid'], 3);
					$this->db->insert('atme',$data);
						
				}
			}
		}
	}
	/**
	 * 删除评论
	 */
	Public function delComment () {
		//var_dump($_POST);exit;
		$cid = $this->input->post('cid', 'intval');
		$wid = $this->input->post('wid', 'intval');
		$this->db->where('id',$cid);
		$this->db->delete('comment');
		if ($this->db->affected_rows()) {
			$this->db->set('comment', 'comment-1',FALSE);
			$this->db->where('id',$wid);
			$this->db->update('weibo');
			echo 1;
		} else {
			echo 0;
		}
	}
	/**
	 * @提到我的
	 */
	Public function atme () {
		$data['user'] = $this->__userinfo;
		$this->set_msg($this->__userid, 3, true);
		$this->db->where('uid',$this->__userid);
		$this->db->select('wid');
		$wid = $this->db->get('atme')->result_array();
		if($wid){
			foreach($wid as $k=>&$v){
				$wid[$k] = $v['wid'];
			}
		}
		$page = 10;
		$offset = $this->uri->segment(3)?$this->uri->segment(3):0;
		$weibo = $this->WeiboModel->atmelist($wid,$page,$offset);

		$data['atme'] = 1;
		$data['weibo'] = $weibo;
		$this->load->view('index/header.html',$data);
		$this->load->view('index/nav.html');
		$this->load->view('index/weiboList.html');	
		$this->load->view('index/bottom.html');
	}
	/**
	 * 收藏列表
	 */
	Public function keep () {
		$data['user'] = $this->__userinfo;
		$uid  = $this->__userid;
		$data['atme'] = 0;
		$wid = array();
		$this->db->where('uid',$this->__userid);
		$this->db->select('wid');
		$wid = $this->db->get('keep')->result_array();
	
		if($wid){
			foreach($wid as $k=>&$v){
				$wid[$k] = $v['wid'];
			}
			
			$this->db->where_in('wid',$wid);
			$count  = $this->db->count_all_results('keep');
		}
		$data['count'] = $count?$count:0;
		//分页处理
		$page = 10;
		$offset = $this->uri->segment(3)?$this->uri->segment(3):0;
		$weibo = $this->WeiboModel->keeplist($wid,$page,$offset);
		$data['weibo'] = $weibo;
		
		$this->load->view('index/header.html',$data);
		$this->load->view('index/nav.html');
		$this->load->view('index/weiboList.html');
		$this->load->view('index/bottom.html');
	}
	/**
	 * 异步取消收藏
	 */
	Public function cancelKeep () {
		if (!$this->input->is_ajax_request()) {
			halt('页面不存在');
		}
	
		$kid = intval($this->input->post('kid'));
		$wid = intval($this->input->post('wid'));
		$this->db->where('id',$kid);
		$this->db->delete('keep');
		if ($this->db->affected_rows()) {
			$this->db->set('keep', 'keep-1',FALSE);
			$this->db->where('id',$wid);
			$this->db->update('weibo');
	
			echo 1;
		} else {
			echo 0;
		}
	}
	/*	 * 私信列表
	 */

	
	Public function letter () {
		$this->load->model('index/LetterModel');
		$uid = $this->__userid;
		
		$data['user'] = $this->__userinfo;
		$this->set_msg($uid, 2, true);
		$this->db->where('uid',$uid);
		$count  = $this->db->count_all_results('letter');
		$data['count'] = $count?$count:0;
		//分页处理
		$page = 10;
		$offset = $this->uri->segment(3)?$this->uri->segment(3):0;
		$letter = $this->LetterModel->letterlist($uid,$page,$offset);

		$data['letter'] = $letter;
		
		$this->load->view('index/letter.html',$data);
		
		$this->load->view('index/bottom.html');
	}
	/**
	 * 异步删除私信
	 */
	Public function delLetter () {
		if (!$this->input->is_ajax_request()) {
			halt('页面不存在');
		}
	
		$lid = intval($this->input->post('lid'));
		$this->db->where("id",$lid);
		$this->db->delete("letter");
		if ($this->db->affected_rows()) {
			echo 1;
		} else {
			echo 0;
		}
	}
	
	/**
	 * 私信发送表单处理
	 */
	 Public function letterSend () {
	
		$name = $this->input->post('name');
		 $this->db->where('nickname',$name);
		 $this->db->select('userid');
		 $uid = $this->db->get('user')->result_array();;
		
		if (!$uid) {
			$html = $this->point(site_url('Operate/letter'),"用户不存在","error");
			echo $html;
			return;
		}
	
		$data = array(
				'from' =>$this->__userid,
				'content' => $this->input->post('content'),
				'time' => time(),
				'uid' => $uid[0]['userid']
		);
	   	$this->db->insert("letter", $data);
		if ($this->db->affected_rows()) {
	
			$this->set_msg($uid[0]['userid'], 2);
	
			$html = $this->point(site_url('Operate/letter'),"发送成功","success");
			echo $html;
			return;
		} else {
			$html = $this->point(site_url('Operate/letter'),"发送失败","error");
			echo $html;
			return;
		}
	} 
/**
	 * 异步添加关注
	 */
	Public function addFollow () {
		if (!$this->input->is_ajax_request()) {
			halt('页面不存在');
		}
		$data = array(
			'follow' => intval($this->input->post('follow')),
			'fans' => $this->__userid,
			'gid' => intval($this->input->post('gid'))
			);
		$this->db->insert('follow',$data);
		if ($this->db->affected_rows()) {
			//被关注人粉丝加一
			$this->db->set('fans', 'fans+1',FALSE);
			$this->db->where('uid',$data['follow']);
			$this->db->update('userinfo');
			//我的关注数加一
			$this->db->set('follow', 'follow+1',FALSE);
			$this->db->where('uid',$this->__userid);
			$this->db->update('userinfo');
			echo json_encode(array('status' => 1, 'msg' => '关注成功'));
		} else {
			echo json_encode(array('status' => 0, 'msg' => '关注失败请重试...'));
		}
	}
	
/**
	 * 异步移除关注与粉丝
	 */
	Public function delFollow () {
		if (!$this->input->is_ajax_request()) {
			halt('页面不存在');
		}

		$uid = intval($this->input->post('uid'));
		$type = intval($this->input->post('type'));

		$where = $type ? "follow = $uid and fans =" .$this->__userid : "fans = $uid and follow=".$this->__userid;
		$this->db->where($where);
		$this->db->delete("follow");
		if ($this->db->affected_rows()) {
			
			if ($type) {
				$this->db->set('follow', 'follow-1',FALSE);
				$this->db->where('uid', $this->__userid);
				$this->db->update('follow');
				$this->db->set('fans', 'fans-1',FALSE);
				$this->db->where('uid',$uid);
				$this->db->update('userinfo');
			} else {
				
				$this->db->set('fans', 'fans-1',FALSE);
				$this->db->where('uid', $this->__userid);
				$this->db->update('follow');
				$this->db->set('follow', 'follow-1',FALSE);
				$this->db->where('uid',$uid);
				$this->db->update('userinfo');
			}

			echo 1;
		} else {
			echo 0;
		}
	}
}