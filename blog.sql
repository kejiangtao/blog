/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : blog

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2017-01-24 13:03:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bl_atme`
-- ----------------------------
DROP TABLE IF EXISTS `bl_atme`;
CREATE TABLE `bl_atme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wid` int(11) NOT NULL COMMENT '提到我的微博ID',
  `uid` int(11) NOT NULL COMMENT '所属用户ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `wid` (`wid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='@提到我的微博';

-- ----------------------------
-- Records of bl_atme
-- ----------------------------
INSERT INTO `bl_atme` VALUES ('12', '64', '2');
INSERT INTO `bl_atme` VALUES ('13', '64', '2');
INSERT INTO `bl_atme` VALUES ('14', '65', '1');

-- ----------------------------
-- Table structure for `bl_comment`
-- ----------------------------
DROP TABLE IF EXISTS `bl_comment`;
CREATE TABLE `bl_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '评论内容',
  `time` int(10) unsigned NOT NULL COMMENT '评论时间',
  `uid` int(11) NOT NULL COMMENT '评论用户的ID',
  `wid` int(11) NOT NULL COMMENT '所属微博ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `wid` (`wid`)
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='微博评论表';

-- ----------------------------
-- Records of bl_comment
-- ----------------------------
INSERT INTO `bl_comment` VALUES ('111', '给到公司的发稿', '1482136172', '2', '60');
INSERT INTO `bl_comment` VALUES ('121', '回复@xiaoke1：你好啊', '1484633524', '1', '65');
INSERT INTO `bl_comment` VALUES ('115', '金恒丰她赶紧哈规范改价格 ', '1482222103', '2', '60');
INSERT INTO `bl_comment` VALUES ('116', '1111111       \r\n                            黄金分割和                            \r\n                            嗯嗯嗯嗯', '1482223179', '2', '65');
INSERT INTO `bl_comment` VALUES ('117', '回复@xiaoke2：很反感的', '1482223233', '1', '65');
INSERT INTO `bl_comment` VALUES ('118', '回复@xiaoke2：[吃惊]', '1482223529', '1', '65');
INSERT INTO `bl_comment` VALUES ('119', '突然让他', '1482225228', '1', '57');
INSERT INTO `bl_comment` VALUES ('122', '[嘻嘻]', '1484709227', '1', '69');
INSERT INTO `bl_comment` VALUES ('143', '[哈哈]', '1484812367', '2', '70');

-- ----------------------------
-- Table structure for `bl_follow`
-- ----------------------------
DROP TABLE IF EXISTS `bl_follow`;
CREATE TABLE `bl_follow` (
  `follow` int(10) unsigned NOT NULL COMMENT '关注用户的ID',
  `fans` int(10) unsigned NOT NULL COMMENT '粉丝用户ID',
  `gid` int(11) NOT NULL COMMENT '所属关注分组ID',
  KEY `follow` (`follow`),
  KEY `fans` (`fans`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='关注与粉丝表';

-- ----------------------------
-- Records of bl_follow
-- ----------------------------
INSERT INTO `bl_follow` VALUES ('0', '2', '0');
INSERT INTO `bl_follow` VALUES ('1', '2', '0');
INSERT INTO `bl_follow` VALUES ('2', '1', '0');
INSERT INTO `bl_follow` VALUES ('6', '2', '0');

-- ----------------------------
-- Table structure for `bl_group`
-- ----------------------------
DROP TABLE IF EXISTS `bl_group`;
CREATE TABLE `bl_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '分组名称',
  `uid` int(11) NOT NULL COMMENT '所属用户的ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='关注分组表';

-- ----------------------------
-- Records of bl_group
-- ----------------------------
INSERT INTO `bl_group` VALUES ('1', '朋友', '1');
INSERT INTO `bl_group` VALUES ('2', '同学', '1');

-- ----------------------------
-- Table structure for `bl_keep`
-- ----------------------------
DROP TABLE IF EXISTS `bl_keep`;
CREATE TABLE `bl_keep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '收藏用户的ID',
  `time` int(10) unsigned NOT NULL COMMENT '收藏时间',
  `wid` int(11) NOT NULL COMMENT '收藏微博的ID',
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='收藏表';

-- ----------------------------
-- Records of bl_keep
-- ----------------------------
INSERT INTO `bl_keep` VALUES ('1', '1', '1481609438', '14');
INSERT INTO `bl_keep` VALUES ('2', '1', '1481609594', '16');
INSERT INTO `bl_keep` VALUES ('3', '1', '1481621044', '15');
INSERT INTO `bl_keep` VALUES ('4', '1', '1481692751', '11');
INSERT INTO `bl_keep` VALUES ('5', '1', '1482123734', '23');
INSERT INTO `bl_keep` VALUES ('8', '2', '1484638266', '65');
INSERT INTO `bl_keep` VALUES ('7', '2', '1484637143', '6');
INSERT INTO `bl_keep` VALUES ('9', '2', '1484788218', '67');
INSERT INTO `bl_keep` VALUES ('10', '2', '1484793575', '68');
INSERT INTO `bl_keep` VALUES ('11', '2', '1484793828', '63');
INSERT INTO `bl_keep` VALUES ('12', '2', '1484793932', '56');

-- ----------------------------
-- Table structure for `bl_letter`
-- ----------------------------
DROP TABLE IF EXISTS `bl_letter`;
CREATE TABLE `bl_letter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL COMMENT '发私用户ID',
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '私信内容',
  `time` int(10) unsigned NOT NULL COMMENT '私信发送时间',
  `uid` int(11) NOT NULL COMMENT '所属用户ID（收信人）',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='私信表';

-- ----------------------------
-- Records of bl_letter
-- ----------------------------
INSERT INTO `bl_letter` VALUES ('1', '2', '你好啊', '1484708905', '1');
INSERT INTO `bl_letter` VALUES ('19', '2', '恩恩', '1484710896', '1');
INSERT INTO `bl_letter` VALUES ('20', '1', '呵呵', '1484711014', '2');
INSERT INTO `bl_letter` VALUES ('18', '1', '你好', '1484710864', '2');
INSERT INTO `bl_letter` VALUES ('21', '2', '代加工', '1484711099', '1');

-- ----------------------------
-- Table structure for `bl_picture`
-- ----------------------------
DROP TABLE IF EXISTS `bl_picture`;
CREATE TABLE `bl_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mini` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '小图',
  `medium` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '中图',
  `max` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '大图',
  `wid` int(11) NOT NULL COMMENT '所属微博ID',
  PRIMARY KEY (`id`),
  KEY `wid` (`wid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='微博配图表';

-- ----------------------------
-- Records of bl_picture
-- ----------------------------
INSERT INTO `bl_picture` VALUES ('1', 'http://127.0.0.1/Blog//Uploads/pic/14815189787607_mini.jpg', 'http://127.0.0.1/Blog//Uploads/pic/14815189787607_medium.jpg', 'http://127.0.0.1/Blog//Uploads/pic/14815189787607_max.jpg', '11');

-- ----------------------------
-- Table structure for `bl_user`
-- ----------------------------
DROP TABLE IF EXISTS `bl_user`;
CREATE TABLE `bl_user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `account` char(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '账号',
  `password` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `registime` int(10) unsigned NOT NULL COMMENT '注册时间',
  `createtime` char(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '创建时间',
  `updatetime` char(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '跟新时间',
  `updateip` char(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '跟新ip',
  `createip` char(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '创建ip',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否锁定（0：否，1：是）',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `account` (`account`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户表';

-- ----------------------------
-- Records of bl_user
-- ----------------------------
INSERT INTO `bl_user` VALUES ('1', 'xiaoke', 'c33367701511b4f6020ec61ded352059', 'xiaoke1', '0', '1481008509', '1481008509', '127.0.0.1', '127.0.0.1', '1');
INSERT INTO `bl_user` VALUES ('2', 'xiaoke1', 'e10adc3949ba59abbe56e057f20f883e', 'xiaoke2', '0', '1481520952', '1481520952', '127.0.0.1', '127.0.0.1', '1');
INSERT INTO `bl_user` VALUES ('4', 'xiaoke4', 'e10adc3949ba59abbe56e057f20f883e', 'xiaoke4', '0', '1484727425', '1484727425', '127.0.0.1', '127.0.0.1', '1');
INSERT INTO `bl_user` VALUES ('5', 'xiaoke5', 'e10adc3949ba59abbe56e057f20f883e', 'xiaoke5', '0', '1484727529', '1484727529', '127.0.0.1', '127.0.0.1', '1');
INSERT INTO `bl_user` VALUES ('6', 'xiaoke6', 'e10adc3949ba59abbe56e057f20f883e', 'xiaoke6', '0', '1484727737', '1484727737', '127.0.0.1', '127.0.0.1', '1');

-- ----------------------------
-- Table structure for `bl_userinfo`
-- ----------------------------
DROP TABLE IF EXISTS `bl_userinfo`;
CREATE TABLE `bl_userinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `truename` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '真实名称',
  `sex` enum('男','女') COLLATE utf8_unicode_ci NOT NULL DEFAULT '男' COMMENT '性别',
  `location` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '所在地',
  `constellation` char(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '星座',
  `intro` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '一句话介绍自己',
  `face50` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '50*50头像',
  `face80` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '80*80头像',
  `face180` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '180*180头像',
  `style` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default' COMMENT '个性模版',
  `follow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注数',
  `fans` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `weibo` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '微博数',
  `uid` int(11) NOT NULL COMMENT '所属用户ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户信息表';

-- ----------------------------
-- Records of bl_userinfo
-- ----------------------------
INSERT INTO `bl_userinfo` VALUES ('2', '柯江涛', '男', '上海 徐汇', '处女座', 'hehehe', 'http://127.0.0.1/Blog//Uploads/face/1_min.jpg', 'http://127.0.0.1/Blog//Uploads/face/1_max.jpg', 'http://127.0.0.1/Blog//Uploads/face/1_max.jpg', 'default', '2', '2', '4', '1');
INSERT INTO `bl_userinfo` VALUES ('3', null, '男', '上海 徐汇', '处女座', 'ghfgjfhgfjgh', 'http://127.0.0.1/Blog//Uploads/face/2_min.jpg', 'http://127.0.0.1/Blog//Uploads/face/2_large.jpg', 'http://127.0.0.1/Blog//Uploads/face/2_max.jpg', 'default', '4', '2', '12', '2');
INSERT INTO `bl_userinfo` VALUES ('4', null, '男', '', '', '', 'http://127.0.0.1/Blog//Uploads/face/4_min.jpg', 'http://127.0.0.1/Blog//Uploads/face/4_large.jpg', 'http://127.0.0.1/Blog//Uploads/face/4_max.jpg', 'default', '0', '0', '1', '4');
INSERT INTO `bl_userinfo` VALUES ('5', null, '男', '', '', '', '', '', '', 'default', '0', '0', '0', '5');
INSERT INTO `bl_userinfo` VALUES ('6', null, '男', '', '', '', '', '', '', 'default', '0', '1', '0', '6');

-- ----------------------------
-- Table structure for `bl_weibo`
-- ----------------------------
DROP TABLE IF EXISTS `bl_weibo`;
CREATE TABLE `bl_weibo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '微博内容',
  `isturn` int(11) NOT NULL DEFAULT '0' COMMENT '是否转发（0：原创， 如果是转发的则保存该转发微博的ID）',
  `time` int(10) unsigned NOT NULL COMMENT '发布时间',
  `turn` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '转发次数',
  `keep` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏次数',
  `comment` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏次数',
  `uid` int(11) NOT NULL COMMENT '所属用户的ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='微博表';

-- ----------------------------
-- Records of bl_weibo
-- ----------------------------
INSERT INTO `bl_weibo` VALUES ('56', '1', '0', '1482129009', '4', '1', '0', '1');
INSERT INTO `bl_weibo` VALUES ('57', '2', '56', '1482129017', '1', '0', '1', '2');
INSERT INTO `bl_weibo` VALUES ('58', ' // @xiaoke2 : \r\n                            2                            \r\n                            3', '56', '1482129030', '1', '0', '0', '1');
INSERT INTO `bl_weibo` VALUES ('59', '广东省分公司的', '56', '1482135999', '0', '0', '0', '2');
INSERT INTO `bl_weibo` VALUES ('60', 'fhedtrhrthyryhjuyj', '0', '1482136161', '7', '0', '2', '1');
INSERT INTO `bl_weibo` VALUES ('61', '给到公司的发稿', '60', '1482136172', '1', '0', '0', '2');
INSERT INTO `bl_weibo` VALUES ('62', ' // @xiaoke2 : \r\n                            给到公司的发稿                            \r\n                            ', '60', '1482136242', '0', '0', '0', '2');
INSERT INTO `bl_weibo` VALUES ('70', 'hahaha d', '0', '1484728315', '1', '0', '1', '4');
INSERT INTO `bl_weibo` VALUES ('63', ' // @xiaoke1 : \r\n                             // @xiaoke2: \r\n                            2                            \r\n                            3                            \r\n                            4', '56', '1482221429', '0', '1', '0', '2');
INSERT INTO `bl_weibo` VALUES ('64', '金恒丰她赶紧哈规范改价格 ', '60', '1482222103', '2', '0', '0', '2');
INSERT INTO `bl_weibo` VALUES ('65', ' // @xiaoke2 : \r\n                            金恒丰她赶紧哈规范改价格                             \r\n                            黄金分割和', '60', '1482222129', '1', '1', '4', '1');
INSERT INTO `bl_weibo` VALUES ('66', ' // @xiaoke2 : \r\n                            金恒丰她赶紧哈规范改价格                             \r\n                            哈哈哈', '60', '1482222356', '0', '0', '0', '2');
INSERT INTO `bl_weibo` VALUES ('67', '分段函数的发稿是的分公司的分的发稿让他滚突然   ', '60', '1482223051', '0', '1', '0', '2');
INSERT INTO `bl_weibo` VALUES ('68', ' // @xiaoke1 : \r\n                             // @xiaoke2: \r\n                            金恒丰她赶紧哈规范改价格                             \r\n                            黄金分割和                            \r\n                            嗯嗯嗯嗯', '60', '1482223179', '0', '1', '0', '2');
INSERT INTO `bl_weibo` VALUES ('69', 'utrurtyurtyurtu', '0', '1484629969', '0', '0', '1', '2');
INSERT INTO `bl_weibo` VALUES ('71', '1111111111[哈哈]', '0', '1484792976', '0', '0', '0', '2');
INSERT INTO `bl_weibo` VALUES ('72', 'hahah', '70', '1484795065', '0', '0', '0', '2');
