<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends MY_Controller{
	public function __construct()
	{
		parent::__construct();
		
	}
	/* 用户首页显示  */
	public function index()
	{	
				
		$this->load->model('index/WeiboModel');

		$user  = $this->UserModel->get_userinfo('userid',$this->__userid);
		$page = 10;
		$offset = $this->uri->segment(3)?$this->uri->segment(3):0;
		$weibo = $this->WeiboModel->weibolist($this->__userid,$page,$offset);
		
		$data ['weibo'] = $weibo;
		//var_dump($weibo);exit;
		$data['user'] = $user;
		$this->load->view('index/header.html',$data);
		$this->load->view('index/nav.html');
		$this->load->view('index/index.html');
		$this->load->view('index/bottom.html');
	}
	
	/*用户发布微博  */
	
	public function sendWeibo(){
		
		$data = array(
			 $this->input->post('content'),
			 time(),
			 $this->__userid
			);
		$sql = "insert into bl_weibo (content,time,uid) values (?,?,?)";
		$this->db->query($sql,$data);
		$wid= $this->db->insert_id();
		if ($wid) {
			if (!empty($_POST['max'])) {
				$img = array(
					$this->input->post('mini'),
					 $this->input->post('medium'),
					 $this->input->post('max'),
					 $wid
					);
				$sql = "insert into bl_picture (mini,medium,max,wid) values (?,?,?,?)";
				$this->db->query($sql,$img);
			}
			/*微博数增一  */
			$this->db->set('weibo', 'weibo+1',FALSE);
			$this->db->where('uid', $this->__userid);
			$this->db->update('userinfo'); 
			//处理@用户
			$this->_atmeHandel($this->input->post('content'), $wid);
			redirect( $_SERVER['HTTP_REFERER']);
			
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
		
	}
	/**
	 * @用户处理
	 */
	Private function _atmeHandel ($content, $wid) {
		$preg = '/@(\S+?)\s/is';
		preg_match_all($preg, $content, $arr);
	
		if (!empty($arr[1])) {
			foreach ($arr[1] as $v) {
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
	 * 收藏微博
	 */
	Public function keep () {
		
	
		$wid = $this->input->post('wid', 'intval');
	
		$uid = $this->__userid;
		//检测用户是否已经收藏该微博
		$where = array('wid' => $wid, 'uid' => $uid);
		$this->db->get_where('keep', $where);
		
		if ($this->db->affected_rows()) {
			echo -1;
			exit();
		}
	
		//添加收藏
		$data = array(
				'uid' => $uid,
				'time' => $_SERVER['REQUEST_TIME'],
				'wid' => $wid
		);
	   $this->db->insert('keep',$data);
		if ($this->db->affected_rows()) {
			//收藏成功时对该微博的收藏数+1
			$this->db->set('keep', 'keep+1',FALSE);
			$this->db->where('id', $wid);
			$this->db->update('weibo');
			echo 1;
		} else {
			echo 0;
		}
	}
	/**
	 * 转发微博
	 */
	Public function turn () {
		
		//原微博ID
		$id = $this->input->post('id', 'intval');
		$tid = $this->input->post('tid', 'intval');
		//转发内容
		$content = $this->input->post('content');
	
		//提取插入数据
		$data = array(
				'content' => $content,
				'isturn' => $tid ? $tid : $id,
				'time' => time(),
				'uid' => $this->__userid
		);
	
		//var_dump($data);exit;
		//插入数据至微博表
		$this->db->insert('weibo',$data);
		
		if ($this->db->affected_rows()) {
			//原微博转发数+1
			$this->db->set('turn', 'turn+1',FALSE);
			$this->db->where('id', $id);
			$this->db->update('weibo');
			if ($tid) {
				$this->db->set('turn', 'turn+1',FALSE);
				$this->db->where('id', $tid);
				$this->db->update('weibo');
			}
	
			//用户发布微博数+1
			$this->db->set('weibo', 'weibo+1',FALSE);
			$this->db->where('uid', $this->__userid);
			$this->db->update('userinfo');
			//处理@用户
			$this->_atmeHandel($data['content'], $id);
	
			//如果点击了同时评论插入内容到评论表
			if ($this->input->post('becomment')) {
				$data1 = array(
						'content' => $content,
						'time' => time(),
						'uid' => $this->__userid,
						'wid' => $id
				);
				//插入评论数据后给原微博评论次数+1
				$this->db->insert('comment',$data1);
				if ($this->db->affected_rows()) {
					
					$this->db->set('comment', 'comment+1',FALSE);
					$this->db->where('id', $id);
					$this->db->update('weibo');
				}
			}
	
			redirect( $_SERVER['HTTP_REFERER']);
		} else {
			redirect( $_SERVER['HTTP_REFERER']);
		}
	}
	/**
	 * 评论
	 */
	Public function comment () {
		
		//提取评论数据
		$data = array(
				'content' => $this->input->post('content'),
				'time' => time(),
				'uid' => $this->__userid,
				'wid' => $this->input->post('wid', 'intval')
		);
			$this->db->insert('comment',$data);
		if ($this->db->affected_rows()) {
			//读取评论用户信息
			$this->db->where('uid',$data['uid']);
			$this->db->select('userinfo.uid,userinfo.face50,user.nickname');
			$user = $this->db->from('userinfo')
					->join('user','userinfo.uid=user.userid','left')
					->get()->row_array();
			
			//被评论微博的发布者用户名
			$uid = $this->input->post('uid', 'intval');
			$this->db->where('userid',$uid);
			$this->db->select('nickname');
			$username = $this->db->get('user')->result_array();			
			//评论数+1
			$this->db->set('comment', 'comment+1',FALSE);
			$this->db->where('id', $data['wid']);
			$this->db->update('weibo');
			//评论同时转发时处理
			if ($_POST['isturn']) {
				//读取转发微博ID与内容
				$this->db->where('id',$data['wid']);
				$this->db->select('id,content,isturn');
				$weibo = $this->db->get('weibo')->row_array();
				$content = $weibo['isturn'] ? $data['content'] . ' // @' . $username['nickname'] . ' : ' . $weibo['content'] : $data['content'];
	
				//同时转发到微博的数据
				$cons = array(
						'content' => $content,
						'isturn' => $weibo['isturn'] ? $weibo['isturn'] : $data['wid'],
						'time' => $data['time'],
						'uid' => $data['uid']
				);
				$this->db->insert('weibo',$cons);
				if ($this->db->affected_rows()) {
					//$db->where(array('id' => $weibo['id']))->setInc('turn');
					$this->db->set('turn', 'turn+1',FALSE);
					$this->db->where('id', $data['wid']);
					$this->db->update('weibo');
				}
	
				echo 1;
				exit();
			}
	
			//组合评论样式字符串返回
			$str = '';
			$str .= '<dl class="comment_content">';
			$str .= '<dt><a>';
			$str .= '<img src="';
			if ($user['face50']) {
				$str .=  $user['face50'];
			} else {
				$str .= '?Blog/Public/Images/noface.gif';
			}
			$str .= '" alt="' . $user['nickname'] . '" width="30" height="30"/>';
			$str .= '</a></dt><dd>';
			$str .= '<a href="" class="comment_name">';
			$str .= $user['nickname'] . '</a> : ' . replace_weibo($data['content']);
			$str .= '&nbsp;&nbsp;( ' . time_format($data['time']) . ' )';
			$str .= '<div class="reply">';
			$str .= '<a href=""></a>';
			$str .= '</div></dd></dl>';
	
			$this->set_msg($uid, 1);
			echo $str;
	
		} else {
			echo 'false';
		}
	
	}
	
	/**
	 * 异步获取评论内容
	 */
	Public function getComment () {
		
		$wid = $this->input->post('wid', 'intval');
		
		$page = $this->input->post('page')?$this->input->post('page', 'intval'):1;
			/*每页数量  */
		$count = 10;
		/*偏移量  */
		$offest = ($page-1)*$count;
	
		$this->db->where('wid' , $wid);
		$total =  $this->db->count_all_results('comment'); 
		$this->db->select('comment.id,comment.uid,comment.content,comment.time,comment.wid,user.nickname,userinfo.face50',false);
		$data=$this->db->from('comment')
		->limit($count, $offest)
		->where('wid' , $wid)
		->order_by('time','desc')
		->join('userinfo','comment.uid=userinfo.uid','left')
		->join('user','comment.uid=user.userid','left')
		->get()->result_array();
		//echo $this->db->last_query();
		//var_dump($data);exit;
	
		if ($data) {
			$str = '';
			foreach ($data as $v) {
				$str .= '<dl class="comment_content">';
				$str .= '<dt><a href="">';
				$str .= '<img src="';
				
				if ($v['face50']) {
					$str .=  $v['face50'];
				} else {
					$str .= '/Blog/Public/Images/noface.gif';
				}
				$str .= '" alt="' . $v['nickname'] . '" width="30" height="30"/>';
				$str .= '</a></dt><dd>';
				$str .= '<a href="' . base_url('/' . $v['uid']) . '" class="comment_name">';
				$str .= $v['nickname'] . '</a> : ' . replace_weibo($v['content']);
				$str .= '&nbsp;&nbsp;( ' . time_format($v['time']) . ' )';
				$str .= '<div class="reply">';
				$str .= '<a href=""></a>';
				$str .= '</div></dd></dl>';
			}
	
			 if ($total > $count) {
				$str .= '<dl class="comment-page">';
					
				switch ($page) {
					case $page > 1 && $page < ceil($total/$count) :
						$str .= '<dd onclick="pageshow(this)" page="' . ($page - 1) . '" wid="' . $wid . '">上一页</dd>';
						$str .= '<dd onclick="pageshow(this)" page="' . ($page + 1) . '" wid="' . $wid . '">下一页</dd>';
						break;
	
					case $page < ceil($total/$count) :
						$str .= '<dd onclick="pageshow(this)" page="' . ($page + 1) . '" wid="' . $wid . '">下一页</dd>';
						break;
	
					case $page == ceil($total/$count) :
						$str .= '<dd onclick="pageshow(this)" page="' . ($page - 1) . '" wid="' . $wid . '">上一页</dd>';
						break;
				}
	
				$str .= '</dl>';
			} 
	
			echo $str;
	
		} else {
			echo 'false';
		}
	}
	/*微博图片处理  */
	public function uploadPic(){
		//var_dump($_FILES);
		$config['upload_path'] = './Uploads/pic';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['overwrite'] = true;
		$type = explode('.', $_FILES['picture']['name']);
		$config['file_name'] = time().rand(1000,9999).'.'.$type[1];
		$this->load->library('upload',$config);
		$this->upload->do_upload('picture');
		if($this->upload->display_errors()){
			echo "<script type='text/javascript'>";
			echo "parent.$('#errMsg').html('".$this->upload->display_errors('','')."');";
			echo "</script>";
		}else{
			$imgUrl = $this->trumdPic($config['file_name']);
			echo "<script type='text/javascript'>";
			echo "parent.$('input[name=mini]').val('".$imgUrl['mini']."');";
			echo "parent.$('input[name=medium]').val('".$imgUrl['medium']."');";
			echo "parent.$('input[name=max]').val('".$imgUrl['max']."');";
			echo "</script>";
	
		}
	}
	/**
	 * 异步轮询推送消息
	 */
	Public function getMsg () {
	
		$this->load->driver('cache', array('adapter' => 'redis', 'backup' => 'file'));
		$uid = $this->__userid;
	
		$msg = $this->cache->get('usermsg' . $uid);
		
		if ($msg) {
			
			if ($msg['comment']['status']) {
				$msg['comment']['status'] = 0;
				$this->cache->file->save('usermsg' . $uid, $msg, 0);
				echo json_encode(array(
						'status' => 1,
						'total' => $msg['comment']['total'],
						'type' => 1
				));
				exit();
			}
	
			if ($msg['letter']['status']) {
				$msg['letter']['status'] = 0;
				$this->cache->file->save('usermsg' . $uid, $msg, 0);
				echo json_encode(array(
						'status' => 1,
						'total' => $msg['letter']['total'],
						'type' => 2
				));
				exit();
			}
	
			if ($msg['atme']['status']) {
				$msg['atme']['status'] = 0;
				$this->cache->file->save('usermsg' . $uid, $msg, 0);
				echo json_encode(array(
						'status' => 1,
						'total' => $msg['atme']['total'],
						'type' => 3
				));
				exit();
			}
		}
		echo json_encode(array('status' => 0));
	}
	
	/*图像处理类
	 * $path 图像路径
	 * $whidth 图像宽度，
	 * $height 图像高度
	 */
	public function trumdPic($filename){
		$imgurl = array();
		$type = explode('.', $filename);
		/* 180*180 */
		$config['image_library'] = 'gd2';
		$config['source_image'] = './Uploads/pic/'.$filename;
		$config['create_thumb'] = TRUE;
		$config['thumb_marker'] = '_max';
		$config['maintain_ratio'] = false;
		$config['width']     = '800';
		$config['height']   = '800';
		$this->load->library('image_lib', $config);
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		$imgurl['max'] = base_url().'/Uploads/pic/'.$type[0].'_max.'.$type[1];
		/* 80*80 */
	
		$config1['image_library'] = 'gd2';
		$config1['source_image'] = './Uploads/pic/'.$filename;
		$config1['create_thumb'] = TRUE;
		$config1['thumb_marker'] = '_medium';
		$config1['maintain_ratio'] = false;
		$config1['width']     = '380';
		$config1['height']   = '380';
		$this->load->library('image_lib', $config1);
		$this->image_lib->initialize($config1);
		$this->image_lib->resize();
		$imgurl['medium'] = base_url().'/Uploads/pic/'.$type[0].'_medium.'.$type[1];
		/* 50*50 */
		$config2['image_library'] = 'gd2';
		$config2['source_image'] = './Uploads/pic/'.$filename;
		$config2['create_thumb'] = TRUE;
		$config2['thumb_marker'] = '_mini';
		$config2['maintain_ratio'] = false;
		$config2['width']     = '120';
		$config2['height']   = '120';
	
		$this->load->library('image_lib', $config2);
		$this->image_lib->initialize($config2);
		$this->image_lib->resize();
		$imgurl['mini'] = base_url().'/Uploads/pic/'.$type[0].'_mini.'.$type[1];
		return $imgurl;
	}

}