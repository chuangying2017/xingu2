/*
Navicat MySQL Data Transfer

Source Server         : localhost_3308
Source Server Version : 50717
Source Host           : localhost:3308
Source Database       : xin

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2018-01-20 17:58:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for my_profit
-- ----------------------------
DROP TABLE IF EXISTS `my_profit`;
CREATE TABLE `my_profit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '会员id',
  `create_time` char(20) DEFAULT NULL COMMENT '创建时间',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '分红金额',
  `order_id` int(11) DEFAULT NULL COMMENT '订单id',
  `return_num` tinyint(2) DEFAULT NULL COMMENT '迭代次数',
  `type` tinyint(1) DEFAULT NULL COMMENT '1每日分红2动态分红3全球分红',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日分红';

-- ----------------------------
-- Records of my_profit
-- ----------------------------

-- ----------------------------
-- Table structure for web_admin
-- ----------------------------
DROP TABLE IF EXISTS `web_admin`;
CREATE TABLE `web_admin` (
  `id` mediumint(100) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `password` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `logip` char(50) CHARACTER SET gbk DEFAULT NULL COMMENT '上次登录的时间',
  `regtime` varchar(50) CHARACTER SET gbk DEFAULT NULL COMMENT '注册时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '用户状态',
  `groupid` tinyint(2) DEFAULT NULL COMMENT '用户组id',
  `lasttime` varchar(50) CHARACTER SET gbk DEFAULT NULL COMMENT '上次登录的时间',
  `lognum` mediumint(9) DEFAULT NULL COMMENT '登录次数',
  `sex` tinyint(1) DEFAULT '1',
  `abstract` varchar(200) CHARACTER SET gbk DEFAULT NULL,
  `email` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `address` varchar(100) CHARACTER SET gbk DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET gbk DEFAULT NULL,
  `errorlognum` mediumint(9) DEFAULT '0',
  `errorlogtime` varchar(50) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of web_admin
-- ----------------------------
INSERT INTO `web_admin` VALUES ('1', 'superadmin', '80df3fa624c38584523cfaba8983a05d', '192.168.1.86', '1477993282', '1', '16', '1499241014', '281', '1', '', 'as1@qq.com', '1', '13996669798', '0', '0');
INSERT INTO `web_admin` VALUES ('2', 'abcd123', '68cb582802363ea105f89d6d7b0e20ba', null, '1513220047', '1', '20', null, '2', '1', null, '5214336@qq.com', null, '13059551106', '0', '0');

-- ----------------------------
-- Table structure for web_article
-- ----------------------------
DROP TABLE IF EXISTS `web_article`;
CREATE TABLE `web_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `art_title` varchar(200) DEFAULT NULL,
  `art_info` varchar(255) DEFAULT NULL,
  `art_keyword` varchar(200) DEFAULT NULL,
  `art_content` text,
  `art_author` varchar(200) DEFAULT NULL,
  `art_time` int(10) unsigned DEFAULT '0',
  `art_type` smallint(5) unsigned DEFAULT NULL,
  `art_img` varchar(200) DEFAULT NULL,
  `art_order` int(10) unsigned DEFAULT NULL,
  `art_click` int(10) unsigned DEFAULT '0',
  `art_source` varchar(200) DEFAULT NULL,
  `art_status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `type_id` (`art_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Records of web_article
-- ----------------------------
INSERT INTO `web_article` VALUES ('1', '鑫谷SHIGOO火爆来袭', '', '', '<p style=\"text-align: center;\"><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\"><img width=\"266\" height=\"94\" title=\"1510646467.png\" style=\"width: 266px; height: 94px;\" alt=\"sdas.png\" src=\"/ueditor/php/upload/image/20171114/1510646467.png\"/></span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">北海凡科贸易有限公司旗下大数据互联网购物金融理财产品紫红蓝11月08日众筹火爆来袭！</span></p><p><span style=\"font-size: 14px;\">&nbsp;</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">一、免费注册、购买产品获得每天20%的分红；</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">购买众筹为100元、500元、1000元、1500元4个档次。</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">【例如：第一天购买100元产品，第二天继续购买100元产品，将获得120元的返利，第三天购买100元，继续提现120元，以此类推.........</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">如果第一天购买1000元产品，第二天购买1000以下的金额，返回本金，不计算利息，为了长久发展，每次提现扣取提现金额2%的网络维护费。作为平台资金流动。】</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">&nbsp;</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">投资金额100元月收益600元</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">投资金额500元月收益3000元</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">投资金额1000元月收益6000元</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">投资金额1500元月收益9000元</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">以此类推，从小到大定期开放额度。</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">&nbsp;</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">第二、推荐奖利</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">第一代5%</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">第二代3%</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">第三代1%</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">三代内不限人数，多劳多得，会员购买产品都有奖金分红。奖金5000元日封顶。短期日薪过千很容易。</span><br/></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">&nbsp;</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">第三、团队奖励</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">直接推荐20个会员团队达到150人就可以获得团队业绩6%奖励，直接推荐30个会员团队达到300人就可以获得团队业绩10%奖励。</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">&nbsp;</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">简单粗暴不玩退本金，进出自由，真正的人性化平台！</span></p><p><span style=\"font-family: 微软雅黑, sans-serif; font-size: 14px;\">为了平台长久发展， 一套资料注册一个帐号，禁止投机，封号后果自负。</span></p><p><span style=\"font-size: 14px;\">&nbsp;</span></p><p><br/></p>', '紫红蓝', '1512445996', '1', '', '0', '0', '紫红蓝', '1');

-- ----------------------------
-- Table structure for web_bank
-- ----------------------------
DROP TABLE IF EXISTS `web_bank`;
CREATE TABLE `web_bank` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '银行卡id',
  `bankcrad` varchar(30) DEFAULT NULL COMMENT '银行名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '1上架2下架',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='银行卡';

-- ----------------------------
-- Records of web_bank
-- ----------------------------
INSERT INTO `web_bank` VALUES ('2', '工商银行', '1', '3');
INSERT INTO `web_bank` VALUES ('3', '农商银行', '1', '5');
INSERT INTO `web_bank` VALUES ('4', '中国银行', '1', '2');
INSERT INTO `web_bank` VALUES ('5', '建设银行', '1', '0');
INSERT INTO `web_bank` VALUES ('6', '农业银行', '1', '0');
INSERT INTO `web_bank` VALUES ('7', '招商银行', '1', '0');
INSERT INTO `web_bank` VALUES ('8', '交通银行', '1', '0');
INSERT INTO `web_bank` VALUES ('9', '汇丰银行', '1', '0');
INSERT INTO `web_bank` VALUES ('10', '光大银行', '1', '0');
INSERT INTO `web_bank` VALUES ('11', '平安银行', '1', '0');
INSERT INTO `web_bank` VALUES ('12', '邮政银行', '1', '0');
INSERT INTO `web_bank` VALUES ('13', '民生银行', '1', '0');

-- ----------------------------
-- Table structure for web_bonus
-- ----------------------------
DROP TABLE IF EXISTS `web_bonus`;
CREATE TABLE `web_bonus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '会员id',
  `status` tinyint(1) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '奖金类型1一代、2二代、3三代、4团队',
  `create_date` char(32) DEFAULT NULL COMMENT '奖励时间',
  `money` double(10,2) DEFAULT '0.00' COMMENT '奖励金额',
  `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COMMENT='奖金表';

-- ----------------------------
-- Records of web_bonus
-- ----------------------------
INSERT INTO `web_bonus` VALUES ('24', '8', '0', '1', '1512456589', '2.00', null);
INSERT INTO `web_bonus` VALUES ('25', '5', '0', '2', '1512456589', '1.50', null);
INSERT INTO `web_bonus` VALUES ('26', '9', '0', '1', '1512456599', '2.00', null);
INSERT INTO `web_bonus` VALUES ('27', '8', '0', '2', '1512456599', '1.50', null);
INSERT INTO `web_bonus` VALUES ('28', '5', '0', '3', '1512456599', '0.50', null);
INSERT INTO `web_bonus` VALUES ('29', '1', '0', '4', '1512456608', '2.00', null);
INSERT INTO `web_bonus` VALUES ('30', '9', '0', '1', '1512456608', '4.00', null);
INSERT INTO `web_bonus` VALUES ('31', '8', '0', '2', '1512456608', '3.00', null);
INSERT INTO `web_bonus` VALUES ('32', '5', '0', '3', '1512456608', '1.00', null);
INSERT INTO `web_bonus` VALUES ('33', '1', '0', '4', '1512456630', '2.00', null);
INSERT INTO `web_bonus` VALUES ('34', '1', '0', '4', '1512456639', '15.00', null);
INSERT INTO `web_bonus` VALUES ('35', '7', '0', '5', '1512456677', '16.67', '3');
INSERT INTO `web_bonus` VALUES ('36', '8', '0', '5', '1512456677', '16.67', '4');
INSERT INTO `web_bonus` VALUES ('37', '6', '0', '5', '1512456677', '16.67', '5');
INSERT INTO `web_bonus` VALUES ('38', '5', '0', '5', '1512456677', '16.67', '6');
INSERT INTO `web_bonus` VALUES ('39', '4', '0', '5', '1512456677', '16.67', '7');
INSERT INTO `web_bonus` VALUES ('40', '3', '0', '5', '1512456677', '16.67', '8');
INSERT INTO `web_bonus` VALUES ('41', '2', '0', '5', '1512456677', '16.67', '9');
INSERT INTO `web_bonus` VALUES ('42', '9', '0', '5', '1512456677', '16.67', '10');
INSERT INTO `web_bonus` VALUES ('43', '10', '0', '5', '1512456678', '16.67', '11');
INSERT INTO `web_bonus` VALUES ('44', '13', '0', '5', '1512456678', '16.67', '12');
INSERT INTO `web_bonus` VALUES ('45', '1', '0', '4', '1512456753', '15.00', null);
INSERT INTO `web_bonus` VALUES ('46', '15', '0', '5', '1512457159', '16.67', '17');
INSERT INTO `web_bonus` VALUES ('47', '14', '0', '5', '1512457159', '33.33', '18');
INSERT INTO `web_bonus` VALUES ('48', '13', '0', '5', '1512457159', '66.67', '19');
INSERT INTO `web_bonus` VALUES ('49', '12', '0', '5', '1512457159', '100.00', '20');
INSERT INTO `web_bonus` VALUES ('50', '11', '0', '5', '1512457160', '150.00', '21');
INSERT INTO `web_bonus` VALUES ('51', '15', '2', '0', '1512457579', '120.00', null);
INSERT INTO `web_bonus` VALUES ('52', '15', '2', '0', '1512457724', '120.00', null);
INSERT INTO `web_bonus` VALUES ('53', '15', '2', '0', '1512457812', '120.00', null);
INSERT INTO `web_bonus` VALUES ('54', '14', '2', '0', '1512459513', '120.00', null);
INSERT INTO `web_bonus` VALUES ('55', '14', '0', '1', '1512461041', '4.00', null);
INSERT INTO `web_bonus` VALUES ('56', '14', '0', '1', '1512461060', '2.00', null);
INSERT INTO `web_bonus` VALUES ('57', '15', '0', '5', '1512461244', '16.67', '17');
INSERT INTO `web_bonus` VALUES ('58', '14', '0', '5', '1512461244', '33.33', '18');
INSERT INTO `web_bonus` VALUES ('59', '13', '0', '5', '1512461244', '66.67', '19');
INSERT INTO `web_bonus` VALUES ('60', '12', '0', '5', '1512461244', '100.00', '20');
INSERT INTO `web_bonus` VALUES ('61', '11', '0', '5', '1512461244', '150.00', '21');
INSERT INTO `web_bonus` VALUES ('62', '15', '0', '5', '1512461244', '16.67', '22');
INSERT INTO `web_bonus` VALUES ('63', '16', '0', '5', '1512461244', '16.67', '23');
INSERT INTO `web_bonus` VALUES ('64', '15', '0', '5', '1512461253', '16.67', '17');
INSERT INTO `web_bonus` VALUES ('65', '14', '0', '5', '1512461253', '33.33', '18');
INSERT INTO `web_bonus` VALUES ('66', '13', '0', '5', '1512461253', '66.67', '19');
INSERT INTO `web_bonus` VALUES ('67', '12', '0', '5', '1512461253', '100.00', '20');
INSERT INTO `web_bonus` VALUES ('68', '11', '0', '5', '1512461253', '150.00', '21');
INSERT INTO `web_bonus` VALUES ('69', '15', '0', '5', '1512461253', '16.67', '22');
INSERT INTO `web_bonus` VALUES ('70', '16', '0', '5', '1512461254', '16.67', '23');

-- ----------------------------
-- Table structure for web_cishu
-- ----------------------------
DROP TABLE IF EXISTS `web_cishu`;
CREATE TABLE `web_cishu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `num` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '次数',
  `mobile` decimal(12,0) NOT NULL COMMENT '手机号码',
  `time` int(11) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0为未注册1为注册',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_cishu
-- ----------------------------
INSERT INTO `web_cishu` VALUES ('85', '1', '15875253685', '1509984000', '0');
INSERT INTO `web_cishu` VALUES ('82', '1', '13059551109', '1509897600', '1');
INSERT INTO `web_cishu` VALUES ('83', '1', '13631906564', '1509897600', '1');
INSERT INTO `web_cishu` VALUES ('84', '1', '18512001472', '1509984000', '1');

-- ----------------------------
-- Table structure for web_class
-- ----------------------------
DROP TABLE IF EXISTS `web_class`;
CREATE TABLE `web_class` (
  `id` mediumint(100) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) DEFAULT NULL,
  `type_message` varchar(100) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '1表示文章分类 2表示留言分类 3表示产品分类',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='文章类型表';

-- ----------------------------
-- Records of web_class
-- ----------------------------
INSERT INTO `web_class` VALUES ('1', '通知公告', '通知公告', '1');
INSERT INTO `web_class` VALUES ('2', '公告', '公告', '1');

-- ----------------------------
-- Table structure for web_code
-- ----------------------------
DROP TABLE IF EXISTS `web_code`;
CREATE TABLE `web_code` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` int(11) DEFAULT NULL COMMENT '手机',
  `code` varchar(6) DEFAULT NULL COMMENT '验证码',
  `status` tinyint(1) DEFAULT '1' COMMENT '1待验证2已验证',
  `create_date` int(11) DEFAULT NULL COMMENT '验证生成时间',
  `uid` int(6) DEFAULT NULL COMMENT '验证码用户id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='验证码';

-- ----------------------------
-- Records of web_code
-- ----------------------------

-- ----------------------------
-- Table structure for web_deposit
-- ----------------------------
DROP TABLE IF EXISTS `web_deposit`;
CREATE TABLE `web_deposit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '提现金额',
  `service_charge` decimal(5,2) DEFAULT '0.00' COMMENT '提现手续费',
  `create_date` char(30) DEFAULT NULL COMMENT '提现时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '1待处理2已提现',
  `uid` int(11) DEFAULT NULL COMMENT '提现会员id',
  `name` varchar(30) DEFAULT NULL COMMENT '提现真实名字',
  `bank` varchar(25) DEFAULT NULL COMMENT '提现银行',
  `province` varchar(30) DEFAULT NULL COMMENT '开户省份',
  `city` varchar(20) DEFAULT NULL COMMENT '开户城市',
  `county_s` varchar(30) DEFAULT NULL COMMENT '开户县城/镇',
  `kaihubank` varchar(50) DEFAULT NULL COMMENT '提现开户行',
  `bank_crad` varchar(30) DEFAULT NULL COMMENT '提现银行卡号',
  `mobile` char(15) DEFAULT NULL COMMENT '手机号码',
  `crad` varchar(30) DEFAULT NULL COMMENT '提现身份证',
  `type` tinyint(1) DEFAULT '0' COMMENT '1现金2奖金',
  `huifu` varchar(255) DEFAULT '个人' COMMENT '收款方类型/个人/企业',
  `shou_type` varchar(255) DEFAULT '个人' COMMENT '收款方类型/个人/企业',
  `xinz` varchar(255) DEFAULT '储蓄卡' COMMENT '账户性质（储蓄卡/信用卡）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='提现表';

-- ----------------------------
-- Records of web_deposit
-- ----------------------------
INSERT INTO `web_deposit` VALUES ('1', '98.00', '2.00', '1512458631', '3', '15', '张三', '工商银行', null, null, null, '麦迪支行', '456151321231564515132', null, '848456163231231654', '1', '卡号错误', '个人', '储蓄卡');
INSERT INTO `web_deposit` VALUES ('2', '98.00', '2.00', '1512459113', '1', '14', '李四', '工商银行', null, null, null, '麦迪南路', '654515151321316541561', null, '465151132313213213', '1', '个人', '个人', '储蓄卡');
INSERT INTO `web_deposit` VALUES ('3', '98.00', '2.00', '1512461170', '1', '16', '秦汉', '建设银行', null, null, null, '麦迪南路', '676949649946676949887', null, '466776619466799434', '1', '个人', '个人', '储蓄卡');

-- ----------------------------
-- Table structure for web_img
-- ----------------------------
DROP TABLE IF EXISTS `web_img`;
CREATE TABLE `web_img` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(6) DEFAULT NULL,
  `path` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `path_img` varchar(255) DEFAULT NULL,
  `path_thumb` varchar(255) DEFAULT NULL,
  `path_xiao` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COMMENT='图片';

-- ----------------------------
-- Records of web_img
-- ----------------------------
INSERT INTO `web_img` VALUES ('88', '1', null, '1', 'upload/images/mb_92151512355999.png', 'upload/images/mb_92151512355999thumb.png', 'upload/images/mb_92151512355999xiao.png');
INSERT INTO `web_img` VALUES ('89', '2', null, '1', 'upload/images/mb_12821512356182.png', 'upload/images/mb_12821512356182thumb.png', 'upload/images/mb_12821512356182xiao.png');
INSERT INTO `web_img` VALUES ('90', '3', null, '1', 'upload/images/mb_59591512356198.png', 'upload/images/mb_59591512356198thumb.png', 'upload/images/mb_59591512356198xiao.png');
INSERT INTO `web_img` VALUES ('91', '4', null, '1', 'upload/images/mb_77811512356220.png', 'upload/images/mb_77811512356220thumb.png', 'upload/images/mb_77811512356220xiao.png');
INSERT INTO `web_img` VALUES ('92', '5', null, '1', 'upload/images/mb_37511512356235.png', 'upload/images/mb_37511512356235thumb.png', 'upload/images/mb_37511512356235xiao.png');
INSERT INTO `web_img` VALUES ('96', '6', null, '1', 'upload/images/mb_26851512356600.png', 'upload/images/mb_26851512356600thumb.png', 'upload/images/mb_26851512356600xiao.png');
INSERT INTO `web_img` VALUES ('95', '7', null, '1', 'upload/images/mb_92811512356568.png', 'upload/images/mb_92811512356568thumb.png', 'upload/images/mb_92811512356568xiao.png');
INSERT INTO `web_img` VALUES ('97', '8', null, '1', 'upload/images/mb_93391512356613.png', 'upload/images/mb_93391512356613thumb.png', 'upload/images/mb_93391512356613xiao.png');
INSERT INTO `web_img` VALUES ('98', '9', null, '1', 'upload/images/mb_88251512356624.png', 'upload/images/mb_88251512356624thumb.png', 'upload/images/mb_88251512356624xiao.png');

-- ----------------------------
-- Table structure for web_infinite_class
-- ----------------------------
DROP TABLE IF EXISTS `web_infinite_class`;
CREATE TABLE `web_infinite_class` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `content` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL COMMENT '路径',
  `pid` int(11) DEFAULT NULL COMMENT '上一级id',
  `status` tinyint(1) DEFAULT '1' COMMENT '1显示2禁用',
  `type` tinyint(1) unsigned zerofill DEFAULT '0' COMMENT '0为众筹1为投资',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='无限分类表';

-- ----------------------------
-- Records of web_infinite_class
-- ----------------------------
INSERT INTO `web_infinite_class` VALUES ('30', '众筹', '                                                                            ', '0', '0', '2', '0');
INSERT INTO `web_infinite_class` VALUES ('36', '分红', '                                                                                                                                        ', '0', '0', '1', '1');

-- ----------------------------
-- Table structure for web_liu
-- ----------------------------
DROP TABLE IF EXISTS `web_liu`;
CREATE TABLE `web_liu` (
  `uid` int(11) DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `reply` text,
  `time` decimal(13,0) DEFAULT NULL COMMENT '留言时间',
  `reply_time` decimal(13,0) DEFAULT NULL COMMENT '回复时间',
  `res_time` decimal(10,0) DEFAULT NULL COMMENT '监控当天时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '0未回复1已回复',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=201 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_liu
-- ----------------------------
INSERT INTO `web_liu` VALUES ('15', '200', '提现问题', '反馈说我卡号错误', '<p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">鑫谷SHIGOO众筹火爆来袭</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第一、免费注册参与，完善资料后即可提现。一个身份证只能注册一个账号，提现24小时到账。</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第二、众筹产品每天20%的分红；购买众筹为100元一个档次。</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\"><br style=\"word-wrap: break-word;\"/></p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">【例如：</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第一天购买100元产品，第二天继续购买100元产品，将获得120元的返利，第三天购买100元，继续提现120元，以此类推</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">每次提现扣取提现金额5%的网络维护费。作为平台资金流动。】</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp;</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投资金额100元月收益600元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投资金额500元月收益3000元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投资金额1000元月收益6000元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投资金额1500元月收益9000元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">以此类推，从小到大定期开放额度</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp;</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp;</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第三、投资返利；</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投500元赠送500元，每天返利17元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投1000元赠送1000元，每天返利33元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投2000元赠送2000元，每天返利67元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投3000元赠送3000元，每天返利100元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投5000元赠送5000元，每天返利167元</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">投资额度5000封</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp;</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp;</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第三、推荐奖利（以实际投资额计算）</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第一代5%</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第二代3%</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">第三代1%</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp;</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\"><br style=\"word-wrap: break-word;\"/></p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">简单粗暴不玩退本金，进出自由，真正的人性化平台！</p><p style=\"word-wrap: break-word; margin-top: 0px; margin-bottom: 10px; padding: 0px; color: rgb(51, 51, 51); font-family: &quot;Microsoft Yahei&quot;, &quot;Hiragino Sans GB&quot;, &quot;Helvetica Neue&quot;, Helvetica, tahoma, arial, &quot;WenQuanYi Micro Hei&quot;, Verdana, sans-serif, 宋体; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);\">为了平台长久发展， 一套资料注册一个账号，禁止投机，封号后果自负。</p><p><br/></p>', '1512458675', '1513326449', '1512403200', '1');

-- ----------------------------
-- Table structure for web_loginlog
-- ----------------------------
DROP TABLE IF EXISTS `web_loginlog`;
CREATE TABLE `web_loginlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `ip` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `country` varchar(50) CHARACTER SET gbk DEFAULT NULL COMMENT '登录地区',
  `create_date` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `beginip` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `endip` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `area` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COMMENT='登录日志';

-- ----------------------------
-- Records of web_loginlog
-- ----------------------------
INSERT INTO `web_loginlog` VALUES ('1', '1', '113.91.39.63', '广东省深圳市', '2017-11-09 17:48:17', '113.90.88.0', '113.92.91.255', '电信', '1');
INSERT INTO `web_loginlog` VALUES ('2', '1', '163.179.236.236', '广东省惠州市', '2017-11-13 10:56:02', '163.179.192.0', '163.179.255.255', '联通', '1');
INSERT INTO `web_loginlog` VALUES ('3', '1', '163.179.236.236', '广东省惠州市', '2017-11-13 14:43:51', '163.179.192.0', '163.179.255.255', '联通', '1');
INSERT INTO `web_loginlog` VALUES ('4', '1', '120.25.115.6', '北京市', '2017-11-13 19:14:14', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('5', '1', '120.25.115.4', '北京市', '2017-11-14 18:11:06', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('6', '1', '120.25.115.4', '北京市', '2017-11-14 19:09:52', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('7', '1', '120.76.16.231', '中国', '2017-11-15 08:44:26', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('8', '1', '120.25.115.7', '北京市', '2017-11-15 09:04:27', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('9', '1', '120.76.16.230', '中国', '2017-11-15 09:08:09', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('10', '1', '120.25.115.6', '北京市', '2017-11-15 09:40:25', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('11', '1', '120.76.16.232', '中国', '2017-11-15 09:44:50', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('12', '1', '120.25.115.7', '北京市', '2017-11-15 10:32:39', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('13', '1', '120.76.16.231', '中国', '2017-11-15 11:39:48', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('14', '1', '120.76.16.231', '中国', '2017-11-15 14:03:04', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('15', '1', '120.25.115.5', '北京市', '2017-11-15 14:09:57', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('16', '1', '120.25.115.7', '北京市', '2017-11-15 16:03:52', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('17', '1', '120.25.115.8', '北京市', '2017-11-15 16:44:33', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('18', '1', '120.25.115.5', '北京市', '2017-11-15 17:31:54', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('19', '1', '120.76.16.228', '中国', '2017-11-15 18:22:38', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('20', '1', '120.76.16.225', '中国', '2017-11-15 19:32:23', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('21', '1', '120.76.16.232', '中国', '2017-11-15 23:52:53', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('22', '1', '120.76.16.225', '中国', '2017-11-16 08:16:15', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('23', '1', '120.25.115.7', '北京市', '2017-11-16 10:13:45', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('24', '1', '120.25.115.6', '北京市', '2017-11-16 11:32:11', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '2');
INSERT INTO `web_loginlog` VALUES ('25', '1', '120.25.115.6', '北京市', '2017-11-16 11:32:22', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('26', '1', '120.76.16.231', '中国', '2017-11-16 12:38:37', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('27', '1', '120.25.115.6', '北京市', '2017-11-16 14:47:58', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('28', '1', '120.76.16.225', '中国', '2017-11-16 17:25:49', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('29', '1', '120.76.16.232', '中国', '2017-11-16 17:52:06', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('30', '1', '120.76.16.232', '中国', '2017-11-17 08:09:09', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('31', '1', '120.25.115.8', '北京市', '2017-11-17 09:13:36', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('32', '1', '120.76.16.230', '中国', '2017-11-17 13:36:34', '120.76.0.0', '120.79.255.255', '长城宽带', '2');
INSERT INTO `web_loginlog` VALUES ('33', '1', '120.76.16.230', '中国', '2017-11-17 17:06:58', '120.76.0.0', '120.79.255.255', '长城宽带', '2');
INSERT INTO `web_loginlog` VALUES ('34', '1', '120.76.16.230', '中国', '2017-11-17 17:07:14', '120.76.0.0', '120.79.255.255', '长城宽带', '2');
INSERT INTO `web_loginlog` VALUES ('35', '1', '120.76.16.225', '中国', '2017-11-17 19:12:57', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('36', '1', '120.25.115.6', '北京市', '2017-11-18 08:30:48', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('37', '1', '120.25.115.7', '北京市', '2017-11-18 10:43:26', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '2');
INSERT INTO `web_loginlog` VALUES ('38', '1', '120.25.115.5', '北京市', '2017-11-18 10:44:32', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '2');
INSERT INTO `web_loginlog` VALUES ('39', '1', '120.76.16.230', '中国', '2017-11-18 10:45:52', '120.76.0.0', '120.79.255.255', '长城宽带', '2');
INSERT INTO `web_loginlog` VALUES ('40', '1', '120.76.16.225', '中国', '2017-11-18 10:51:58', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('41', '1', '120.25.115.7', '北京市', '2017-11-18 14:19:59', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('42', '1', '120.76.16.225', '中国', '2017-11-18 15:06:27', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('43', '1', '120.25.115.7', '北京市', '2017-11-18 15:12:53', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('44', '1', '120.25.115.4', '北京市', '2017-11-18 15:16:42', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('45', '1', '120.25.115.6', '北京市', '2017-11-19 08:52:24', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('46', '1', '120.76.16.228', '中国', '2017-11-19 13:44:44', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('47', '1', '120.25.115.4', '北京市', '2017-11-20 09:30:59', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '2');
INSERT INTO `web_loginlog` VALUES ('48', '1', '120.76.16.232', '中国', '2017-11-20 09:31:30', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('49', '1', '120.25.115.7', '北京市', '2017-11-20 10:34:16', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('50', '1', '120.76.16.230', '中国', '2017-11-20 11:46:44', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('51', '1', '120.76.16.228', '中国', '2017-11-20 13:35:16', '120.76.0.0', '120.79.255.255', '长城宽带', '2');
INSERT INTO `web_loginlog` VALUES ('52', '1', '120.76.16.228', '中国', '2017-11-20 13:36:48', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('53', '1', '120.76.16.228', '中国', '2017-11-20 16:06:10', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('54', '1', '120.76.16.231', '中国', '2017-11-20 20:20:18', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('55', '1', '120.76.16.230', '中国', '2017-11-20 22:33:57', '120.76.0.0', '120.79.255.255', '长城宽带', '2');
INSERT INTO `web_loginlog` VALUES ('56', '1', '120.76.16.230', '中国', '2017-11-20 22:34:49', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('57', '1', '120.76.16.230', '中国', '2017-11-21 01:13:26', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('58', '1', '120.25.115.8', '北京市', '2017-11-21 08:35:47', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('59', '1', '120.25.115.4', '北京市', '2017-11-21 09:59:17', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('60', '1', '120.25.115.5', '北京市', '2017-11-21 13:57:00', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('61', '1', '120.76.16.230', '中国', '2017-11-21 16:14:17', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('62', '1', '120.25.115.7', '北京市', '2017-11-21 16:16:10', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '2');
INSERT INTO `web_loginlog` VALUES ('63', '1', '120.25.115.7', '北京市', '2017-11-21 16:16:35', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('64', '1', '120.25.115.8', '北京市', '2017-11-21 18:17:06', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('65', '1', '120.76.16.231', '中国', '2017-11-21 22:09:40', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('66', '1', '120.76.16.228', '中国', '2017-11-21 22:37:55', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('67', '1', '120.76.16.232', '中国', '2017-11-21 22:42:40', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('68', '1', '120.76.16.231', '中国', '2017-11-22 09:07:12', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('69', '1', '120.76.16.231', '中国', '2017-11-22 10:19:44', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('70', '1', '120.76.16.228', '中国', '2017-11-22 14:36:54', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('71', '1', '120.25.115.4', '北京市', '2017-11-22 15:28:30', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('72', '1', '120.25.115.8', '北京市', '2017-11-22 19:44:57', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('73', '1', '120.76.16.232', '中国', '2017-11-22 20:50:07', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('74', '1', '120.25.115.8', '北京市', '2017-11-23 10:05:06', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('75', '1', '120.76.16.225', '中国', '2017-11-23 16:05:15', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('76', '1', '120.25.115.8', '北京市', '2017-11-23 19:45:35', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('77', '1', '120.25.115.6', '北京市', '2017-11-24 07:25:37', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('78', '1', '120.76.16.232', '中国', '2017-11-24 09:47:34', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('79', '1', '120.76.16.232', '中国', '2017-11-24 09:52:13', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('80', '1', '120.76.16.225', '中国', '2017-11-24 11:55:53', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('81', '1', '120.25.115.8', '北京市', '2017-11-24 12:50:21', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('82', '1', '120.76.16.232', '中国', '2017-11-24 14:22:44', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('83', '1', '120.76.16.232', '中国', '2017-11-24 15:47:57', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('84', '1', '120.25.115.6', '北京市', '2017-11-24 17:34:14', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('85', '1', '120.25.115.4', '北京市', '2017-11-25 08:35:11', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('86', '1', '120.76.16.232', '中国', '2017-11-25 08:46:21', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('87', '1', '120.25.115.4', '北京市', '2017-11-25 11:44:00', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('88', '1', '120.76.16.228', '中国', '2017-11-26 09:00:33', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('89', '1', '120.25.115.7', '北京市', '2017-11-26 09:14:17', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('90', '1', '120.76.16.232', '中国', '2017-11-26 11:38:39', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('91', '1', '120.25.115.7', '北京市', '2017-11-27 11:36:43', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('92', '1', '120.76.16.231', '中国', '2017-11-27 14:23:37', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('93', '1', '120.76.16.232', '中国', '2017-11-27 15:57:22', '120.76.0.0', '120.79.255.255', '长城宽带', '2');
INSERT INTO `web_loginlog` VALUES ('94', '1', '120.76.16.232', '中国', '2017-11-27 15:57:42', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('95', '1', '120.76.16.225', '中国', '2017-11-28 11:58:24', '120.76.0.0', '120.79.255.255', '长城宽带', '1');
INSERT INTO `web_loginlog` VALUES ('96', '1', '120.25.115.6', '北京市', '2017-11-28 14:24:11', '120.24.0.0', '120.27.255.255', '北京新比林通信技术有限公司', '1');
INSERT INTO `web_loginlog` VALUES ('97', '1', '192.168.1.86', '局域网', '2017-12-07 18:01:04', '192.168.0.0', '192.168.255.255', '对方和您在同一内部网', '2');
INSERT INTO `web_loginlog` VALUES ('98', '1', '192.168.1.86', '局域网', '2017-12-07 18:01:16', '192.168.0.0', '192.168.255.255', '对方和您在同一内部网', '2');
INSERT INTO `web_loginlog` VALUES ('99', '2', '192.168.1.86', '局域网', '2017-12-14 10:54:28', '192.168.0.0', '192.168.255.255', '对方和您在同一内部网', '1');
INSERT INTO `web_loginlog` VALUES ('100', '2', '192.168.1.86', '局域网', '2017-12-14 10:56:28', '192.168.0.0', '192.168.255.255', '对方和您在同一内部网', '1');

-- ----------------------------
-- Table structure for web_mbank
-- ----------------------------
DROP TABLE IF EXISTS `web_mbank`;
CREATE TABLE `web_mbank` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(35) DEFAULT NULL COMMENT '开户实名',
  `mobile` char(18) DEFAULT NULL COMMENT '手机号码',
  `bank` char(25) DEFAULT NULL COMMENT '收款银行',
  `sheng` char(20) DEFAULT NULL COMMENT '省',
  `shi` char(20) DEFAULT NULL COMMENT '市',
  `crad` char(25) DEFAULT NULL COMMENT '开户身份证',
  `zhihang` char(100) DEFAULT NULL COMMENT '开户支行',
  `uid` int(9) DEFAULT NULL COMMENT '会员id',
  `create_time` char(20) DEFAULT NULL COMMENT '创建时间',
  `bank_user` char(26) DEFAULT NULL COMMENT '银行卡账号',
  `status` tinyint(1) DEFAULT '1',
  `bank_id` tinyint(4) unsigned DEFAULT NULL COMMENT '银行id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='会员银行';

-- ----------------------------
-- Records of web_mbank
-- ----------------------------
INSERT INTO `web_mbank` VALUES ('1', '张三', null, '工商银行', null, null, '848456163231231654', '麦迪支行', '15', '1512458571', '456151321231564515132', '1', '2');
INSERT INTO `web_mbank` VALUES ('2', '李四', null, '工商银行', null, null, '465151132313213213', '麦迪南路', '14', '1512459091', '654515151321316541561', '1', '2');
INSERT INTO `web_mbank` VALUES ('3', '秦汉', null, '建设银行', null, null, '466776619466799434', '麦迪南路', '16', '1512461133', '676949649946676949887', '1', '5');

-- ----------------------------
-- Table structure for web_member
-- ----------------------------
DROP TABLE IF EXISTS `web_member`;
CREATE TABLE `web_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '会员id',
  `mobile` decimal(12,0) DEFAULT NULL COMMENT '手机号码/账号',
  `password_two` char(50) DEFAULT NULL,
  `password` char(50) DEFAULT NULL COMMENT '密码',
  `reg_ip` char(22) DEFAULT NULL COMMENT '注册ip',
  `reg_time` decimal(13,0) DEFAULT NULL COMMENT '注册时间',
  `log_ip` char(22) DEFAULT NULL COMMENT '登录ip',
  `log_num` int(9) DEFAULT NULL,
  `log_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `update_time` decimal(13,0) DEFAULT NULL COMMENT '更新时间',
  `money` decimal(12,2) unsigned DEFAULT '0.00' COMMENT '金额',
  `invite_code` varchar(12) DEFAULT NULL COMMENT '会员编号/邀请码',
  `recommend` int(11) DEFAULT '0' COMMENT '推荐人id',
  `name` varchar(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1开启2冻结3删除',
  `invite_person` int(9) DEFAULT '0' COMMENT '邀请人数',
  `bonus` decimal(10,2) DEFAULT '0.00' COMMENT '奖金',
  `error_time` char(20) DEFAULT NULL,
  `re_time` int(11) unsigned DEFAULT '0' COMMENT '监控登录时间',
  `error_num` int(5) DEFAULT '0' COMMENT '登陆错误次数',
  `re_num` tinyint(4) unsigned DEFAULT '0' COMMENT '监控次数',
  `buy_result` tinyint(1) DEFAULT '0' COMMENT '1',
  `frozenmoney` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '冻结钱袋',
  `real` decimal(10,2) DEFAULT '0.00' COMMENT '投资产品总实发数据',
  `card` char(25) DEFAULT NULL COMMENT '注册身份证',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `recommend` (`recommend`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of web_member
-- ----------------------------
INSERT INTO `web_member` VALUES ('1', '18655000000', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444204', null, null, '2017-12-22 16:58:59', '1512444581', '10066.00', '455159', '0', null, '1', '8', '0.00', '', '1513872000', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('2', '18600000001', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444220', null, null, '2017-12-25 16:24:30', '1512444220', '9316.67', '519802', '1', null, '1', '0', '0.00', null, '1514131200', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('3', '18600000002', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444231', null, null, '2017-12-05 14:57:48', '1512444231', '9316.67', '251084', '1', null, '1', '0', '0.00', null, '0', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('4', '18600000003', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444242', null, null, '2017-12-05 14:57:48', '1512444242', '9316.67', '571126', '1', null, '1', '0', '0.00', null, '0', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('5', '18600000004', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444253', null, null, '2017-12-22 17:31:54', '1512444308', '9231.17', '978651', '1', null, '1', '0', '0.00', null, '1513872000', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('6', '18610000001', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444271', null, null, '2017-12-05 14:57:46', '1512444271', '9316.67', '814181', '5', null, '1', '0', '0.00', null, '0', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('7', '18610000002', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444286', null, null, '2017-12-05 14:57:45', '1512444286', '9216.67', '872260', '5', null, '1', '0', '0.00', null, '0', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('8', '18610000003', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444308', null, null, '2017-12-05 14:58:22', '1512444343', '9242.17', '453260', '5', null, '1', '0', '0.00', null, '0', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('9', '18618618600', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444344', null, null, '2017-12-05 14:58:28', '1512444384', '7926.67', '159206', '8', null, '1', '0', '0.00', null, '0', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('10', '18620000000', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444365', null, null, '2017-12-05 14:57:43', '1512444365', '9316.67', '751980', '9', null, '1', '0', '0.00', null, '0', '0', '0', '0', '0.00', '0.00', null);
INSERT INTO `web_member` VALUES ('11', '18620000001', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444384', null, null, '2017-12-21 14:46:10', '1512444384', '4666.67', '245198', '9', null, '1', '0', '0.00', null, '1513785600', '0', '0', '0', '8550.00', '450.00', null);
INSERT INTO `web_member` VALUES ('12', '18655000001', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444443', null, null, '2017-12-05 16:07:33', '1512444443', '7100.00', '124104', '1', null, '1', '0', '0.00', null, '0', '0', '0', '0', '5700.00', '300.00', null);
INSERT INTO `web_member` VALUES ('13', '18655000002', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444570', null, null, '2017-12-05 16:07:33', '1512444608', '6670.01', '295301', '1', null, '1', '0', '0.00', null, '0', '0', '0', '0', '3799.99', '200.01', null);
INSERT INTO `web_member` VALUES ('14', '18655000003', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', '192.168.1.24', '1512444581', null, null, '2017-12-05 16:07:33', '1512459113', '7453.33', '232313', '1', null, '1', '1', '6.00', null, '0', '0', '0', '0', '1900.01', '99.99', null);
INSERT INTO `web_member` VALUES ('15', '18675205465', '68cb582802363ea105f89d6d7b0e20ba', '1e2263b227d3580a587068b026d2f465', null, '1512452480', null, null, '2017-12-06 08:58:29', '1512458631', '1763.35', '205465', '1', null, '1', '0', '0.00', null, '1512489600', '0', '0', '0', '3916.65', '83.35', '');
INSERT INTO `web_member` VALUES ('16', '18512001472', '01957ebadbc62ccfbddd9ed87f93d699', '1e2263b227d3580a587068b026d2f465', null, '1512460940', null, null, '2018-01-19 17:24:13', '1516353853', '4333.34', '001472', '14', null, '1', '0', '0.00', null, '1512403200', '0', '0', '0', '966.66', '33.34', '445966588844569874');

-- ----------------------------
-- Table structure for web_orders
-- ----------------------------
DROP TABLE IF EXISTS `web_orders`;
CREATE TABLE `web_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` char(30) DEFAULT NULL COMMENT '订单号',
  `create_date` char(20) DEFAULT NULL COMMENT '下单时间',
  `uid` int(11) DEFAULT NULL COMMENT '下单会员',
  `pid` int(11) DEFAULT NULL COMMENT '产品id',
  `num` int(11) DEFAULT NULL COMMENT '下单数量',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '下单价格',
  `status` tinyint(1) DEFAULT '1' COMMENT '1是2否',
  `type` tinyint(1) DEFAULT '1' COMMENT '1待支付2已支付',
  `interest` double(10,2) DEFAULT '0.00' COMMENT '利息',
  `update_time` char(50) DEFAULT NULL,
  `codeUrl` char(200) DEFAULT NULL,
  `order_id_baibao` char(50) DEFAULT NULL,
  `prepay_id` char(100) DEFAULT NULL,
  `lagou_sign` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `pid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of web_orders
-- ----------------------------
INSERT INTO `web_orders` VALUES ('22', '2017120549100535', '1512346341', '15', '6', '1', '100.00', '2', '2', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('26', '2017120553505054', '1512457812', '15', '7', '1', '500.00', '1', '2', '100.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('27', '2017120510099995', '1512346341', '14', '6', '1', '100.00', '2', '2', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('28', '2017120557504810', '1512459513', '14', '6', '1', '100.00', '1', '2', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('29', '2017120550491009', '1512461041', '16', '6', '1', '100.00', '1', '2', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('30', '2017120510154102', '1512463246', '15', '6', '1', '100.00', '1', '2', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('38', '2017120751100505', '1512631635', '16', '6', '1', '21.00', '1', '1', '4.20', null, 'http://pan.baidu.com/share/qrcode?w=300&h=300&url=weixin://wxpay/bizpayurl?pr=j5UKVif', null, null, null);
INSERT INTO `web_orders` VALUES ('45', '2017121953971015', '1513648101', '15', '7', '1', '500.00', '1', '1', '100.00', null, 'http://pan.baidu.com/share/qrcode?w=300&h=300&url=weixin://wxpay/bizpayurl?pr=uJIylHR', null, null, null);
INSERT INTO `web_orders` VALUES ('47', '2017122198539957', '1513824666', '11', '6', '1', '100.00', '1', '1', '20.00', null, 'http://pan.baidu.com/share/qrcode?w=300&h=300&url=weixin://wxpay/bizpayurl?pr=KBrR1qg', null, null, null);
INSERT INTO `web_orders` VALUES ('49', '2017122156101971', '1513838776', '11', '6', '1', '100.00', '1', '1', '20.00', null, 'http://paysdk.weixin.qq.com/example/qrcode.php?data=weixin://wxpay/bizpayurl?pr=JHGXBht', null, null, null);
INSERT INTO `web_orders` VALUES ('57', '20171221559956485', '1513849831', '1', '6', '1', '100.00', '1', '1', '20.00', null, 'http://paysdk.weixin.qq.com/example/qrcode.php?data=weixin://wxpay/bizpayurl?pr=ZIP4psD', null, 'wx2017122117502991177bdd300097674688', null);
INSERT INTO `web_orders` VALUES ('98', '20171222991005157', '1513937868', '2', '6', '1', '100.00', '1', '1', '20.00', null, 'https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx2017122218174517d789eb180201262513&package=4191832717', null, null, null);
INSERT INTO `web_orders` VALUES ('99', '20171224529798549', '1514103876', '16', '6', '1', '100.00', '1', '1', '20.00', null, 'http://pan.baidu.com/share/qrcode?w=300&h=300&url=weixin://wxpay/bizpayurl?pr=bnu8Lj3', '205001712241624372370', null, null);
INSERT INTO `web_orders` VALUES ('100', '20171225989948100', '1514182542', '15', '7', '1', '500.00', '1', '1', '100.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('101', '20171225995056569', '1514182721', '15', '7', '1', '500.00', '1', '1', '100.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('113', '20171226495556489', '1514290433', '12', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('114', '20171227484910052', '1514363952', '8', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('115', '20171227481005756', '1514366224', '8', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('119', '20171227505352505', '1514371202', '8', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('120', '20171227985798995', '1514371259', '8', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('121', '20171228984851574', '1514427114', '7', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('122', '20171228565157575', '1514427592', '7', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('123', '20171228505299535', '1514427746', '2', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('124', '20171228995155551', '1514429260', '3', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('125', '20171228565010154', '1514432456', '13', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, null);
INSERT INTO `web_orders` VALUES ('126', '20171228519753975', '1514432515', '13', '6', '1', '100.00', '1', '1', '20.00', null, null, null, null, 'B0C8E6718C4FA92540C85504B470D540');

-- ----------------------------
-- Table structure for web_power
-- ----------------------------
DROP TABLE IF EXISTS `web_power`;
CREATE TABLE `web_power` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(9) DEFAULT NULL,
  `name` varchar(30) CHARACTER SET gbk DEFAULT NULL,
  `control_action` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `sort` varchar(30) CHARACTER SET gbk DEFAULT NULL,
  `level` tinyint(10) DEFAULT '0',
  `style` varchar(20) CHARACTER SET gbk DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8 COMMENT='栏目表';

-- ----------------------------
-- Records of web_power
-- ----------------------------
INSERT INTO `web_power` VALUES ('64', '0', '权限管理', '', '111111111', '0', '&#xe60e');
INSERT INTO `web_power` VALUES ('65', '64', '角色列表', 'Rbac/adminrole', '64-65', '1', '');
INSERT INTO `web_power` VALUES ('66', '64', '节点列表', 'Rbac/adminpermission', '64-66', '1', '');
INSERT INTO `web_power` VALUES ('67', '64', '管理员列表', 'Rbac/adminlist', '64-67', '1', '');
INSERT INTO `web_power` VALUES ('68', '0', '系统设置', '', '63', '0', '&#xe62e');
INSERT INTO `web_power` VALUES ('69', '68', '基本设置', 'Webconfig/index', '68-69', '1', '');
INSERT INTO `web_power` VALUES ('70', '65', '角色编辑', 'Rbac/adminroleedit', '64-65-70', '2', '');
INSERT INTO `web_power` VALUES ('71', '65', '添加角色', 'Rbac/adminroleadd', '64-65-71', '2', '');
INSERT INTO `web_power` VALUES ('72', '65', '删除', 'Rbac/adminRoleDel', '64-65-72', '2', '');
INSERT INTO `web_power` VALUES ('73', '65', '批量删除', 'Rbac/datadelRole', '64-65-73', '2', null);
INSERT INTO `web_power` VALUES ('74', '66', '编辑节点', 'Rbac/poweredit', '64-66-74', '2', '');
INSERT INTO `web_power` VALUES ('75', '66', '删除', 'Rbac/del', '64-66-75', '2', null);
INSERT INTO `web_power` VALUES ('76', '66', '批量删除', 'Rbac/datadelPower', '64-66-76', '2', null);
INSERT INTO `web_power` VALUES ('77', '67', '停用', 'Rbac/admin_stop', '64-67-77', '2', null);
INSERT INTO `web_power` VALUES ('78', '67', '启用', 'Rbac/admin_start', '64-67-78', '2', null);
INSERT INTO `web_power` VALUES ('79', '67', '编辑', 'Rbac/adminedit', '64-67-79', '2', null);
INSERT INTO `web_power` VALUES ('80', '67', '修改密码', 'Rbac/adminpasswordedit', '64-67-80', '2', null);
INSERT INTO `web_power` VALUES ('81', '67', '删除', 'Rbac/adminDel', '64-67-81', '2', null);
INSERT INTO `web_power` VALUES ('84', '67', '批量删除', 'Rbac/datadelAdmin', '64-67-84', '2', null);
INSERT INTO `web_power` VALUES ('85', '0', '会员管理', '', '1', '0', '&#xe60d');
INSERT INTO `web_power` VALUES ('86', '0', '文章管理', '', '11', '0', '&#xe616');
INSERT INTO `web_power` VALUES ('87', '0', '图片管理', '', '111', '-1', '&#xe613');
INSERT INTO `web_power` VALUES ('88', '0', '留言管理', '', '1111', '-1', '&#xe63b');
INSERT INTO `web_power` VALUES ('89', '85', '会员列表', 'Member/index', '89-89', '1', '');
INSERT INTO `web_power` VALUES ('90', '86', '文章列表', 'Article/index', '86-90', '1', '');
INSERT INTO `web_power` VALUES ('91', '89', '添加用户', 'Member/useradd', '89-89-91', '2', null);
INSERT INTO `web_power` VALUES ('92', '86', '分类管理', 'Article/articleclass', '86-92', '-1', '');
INSERT INTO `web_power` VALUES ('93', '92', '编辑分类', 'Article/articleclassedit', '86-92-93', '2', null);
INSERT INTO `web_power` VALUES ('94', '88', '留言列表', 'Message/index', '79-94', '1', '');
INSERT INTO `web_power` VALUES ('95', '89', '启用', 'Member/user_start', '89-89-95', '2', null);
INSERT INTO `web_power` VALUES ('96', '89', '停用', 'Member/user_stop', '89-89-96', '2', '');
INSERT INTO `web_power` VALUES ('97', '89', '查看详情', 'Member/usershow', '89-89-97', '2', '');
INSERT INTO `web_power` VALUES ('98', '89', '修改资料', 'Member/useredit', '89-89-98', '2', '');
INSERT INTO `web_power` VALUES ('99', '89', '修改密码', 'Member/userpasswordedit', '89-89-99', '2', null);
INSERT INTO `web_power` VALUES ('101', '89', '删除', 'Member/userDel', '89-89-101', '2', null);
INSERT INTO `web_power` VALUES ('102', '90', '启用', 'Article/article_start', '86-90-102', '2', '');
INSERT INTO `web_power` VALUES ('103', '90', '启用', 'Article/article_stop', '86-90-103', '2', '');
INSERT INTO `web_power` VALUES ('104', '90', '批量删除', 'Article/datadelArticle', '86-90-104', '2', '');
INSERT INTO `web_power` VALUES ('105', '90', '删除', 'Article/articleDel', '86-90-105', '2', '');
INSERT INTO `web_power` VALUES ('106', '90', '查看', 'Article/articlezhang', '86-90-106', '2', '');
INSERT INTO `web_power` VALUES ('107', '90', '编辑', 'Article/articleedit', '86-90-107', '2', '');
INSERT INTO `web_power` VALUES ('108', '90', '添加', 'Article/articleadd', '86-90-108', '2', '');
INSERT INTO `web_power` VALUES ('109', '92', '删除分类', 'Article/articleClassDel', '86-92-109', '2', '');
INSERT INTO `web_power` VALUES ('111', '92', '管理分类', 'Article/articleclass', '86-92-111', '2', '');
INSERT INTO `web_power` VALUES ('112', '94', '删除', 'Message/messageDel', '79-94-112', '2', '');
INSERT INTO `web_power` VALUES ('113', '94', '批量删除', 'Message/datadelMessage', '79-94-113', '2', '');
INSERT INTO `web_power` VALUES ('114', '94', '查看/回复', 'Message/messageedit', '79-94-114', '2', '');
INSERT INTO `web_power` VALUES ('115', '87', '图片列表', 'Picture/index', '80-115', '1', null);
INSERT INTO `web_power` VALUES ('129', '115', '添加图片', 'Picture/pictureadd', '80-115-129', '2', '');
INSERT INTO `web_power` VALUES ('130', '115', '启用', 'Picture/picture_start', '80-115-130', '2', null);
INSERT INTO `web_power` VALUES ('131', '115', '停用', 'Picture/picture_stop', '80-115-131', '2', null);
INSERT INTO `web_power` VALUES ('132', '115', '编辑', 'Picture/pictureedit', '80-115-132', '2', null);
INSERT INTO `web_power` VALUES ('133', '115', '删除', 'Picture/pictureDel', '80-115-133', '2', null);
INSERT INTO `web_power` VALUES ('134', '115', '批量删除', 'Picture/datadePicture', '80-115-134', '2', null);
INSERT INTO `web_power` VALUES ('137', '117', '删除', 'Picture/partnerDel', '80-117-137', '2', null);
INSERT INTO `web_power` VALUES ('141', '117', '启用', 'Picture/partner_start', '80-117-141', '2', null);
INSERT INTO `web_power` VALUES ('142', '117', '停用', 'Picture/partner_stop', '80-117-142', '2', null);
INSERT INTO `web_power` VALUES ('167', '64', '系统日志列表', 'Rbac/loginlog', '64-167', '1', '');
INSERT INTO `web_power` VALUES ('169', '89', 'EXCEL', 'Member/downloadexcel', '89-89-169', '-2', null);
INSERT INTO `web_power` VALUES ('171', '170', '停用', 'Webconfig/bank_stop', '63-170-171', '2', null);
INSERT INTO `web_power` VALUES ('172', '170', '启用', 'Webconfig/bank_start', '63-170-172', '2', null);
INSERT INTO `web_power` VALUES ('174', '170', '添加银行', 'Webconfig/bankadd', '63-170-174', '2', null);
INSERT INTO `web_power` VALUES ('175', '170', '编辑银行', 'Webconfig/bankedit', '63-170-175', '2', null);
INSERT INTO `web_power` VALUES ('176', '170', '删除银行', 'Webconfig/bankdel', '63-170-176', '2', null);
INSERT INTO `web_power` VALUES ('177', '68', '参数设置', 'Webconfig/setbonus', '63-177', '1', null);
INSERT INTO `web_power` VALUES ('188', '89', '修改二级密码', 'Member/usertowpasswordedit', '89-89-188', '-2', null);
INSERT INTO `web_power` VALUES ('193', '85', '关系列表', 'Member/usertree', '89-193', '1', null);
INSERT INTO `web_power` VALUES ('194', '89', '充值', 'Member/recharge', '89-89-194', '2', null);
INSERT INTO `web_power` VALUES ('195', '89', '扣币', 'Member/deduct', '89-89-195', '2', '');
INSERT INTO `web_power` VALUES ('201', '0', '财务管理', '', '201', '0', '&#xe687');
INSERT INTO `web_power` VALUES ('226', '0', '产品管理', '', '226', '0', '&#xe620');
INSERT INTO `web_power` VALUES ('227', '226', '产品列表', 'Product/index', '226-227', '1', null);
INSERT INTO `web_power` VALUES ('230', '227', '添加产品', 'Product/productadd', '226-227-230', '2', null);
INSERT INTO `web_power` VALUES ('231', '227', '批量删除', 'Product/datadel_product', '226-227-231', '2', null);
INSERT INTO `web_power` VALUES ('232', '227', '删除', 'Product/product_del', '226-227-232', '2', null);
INSERT INTO `web_power` VALUES ('233', '227', '发布产品', 'Product/product_start', '226-227-233', '2', null);
INSERT INTO `web_power` VALUES ('234', '227', '下架产品', 'Product/product_stop', '226-227-234', '2', null);
INSERT INTO `web_power` VALUES ('235', '227', '编辑产品', 'Product/productedit', '226-227-235', '2', null);
INSERT INTO `web_power` VALUES ('236', '227', '查看产品图片', 'Product/productimg', '226-227-236', '-2', null);
INSERT INTO `web_power` VALUES ('237', '227', '添加产品图片', 'Product/productaddimg', '226-227-237', '-2', '');
INSERT INTO `web_power` VALUES ('238', '227', '设置为主图', 'Product/product_img_start', '226-227-238', '-2', null);
INSERT INTO `web_power` VALUES ('239', '227', '取消为主图', 'Product/product_img_stop', '226-227-239', '-2', null);
INSERT INTO `web_power` VALUES ('240', '227', '删除产品图片', 'Product/product_img_del', '226-227-240', '-2', null);
INSERT INTO `web_power` VALUES ('241', '228', '添加产品分类', 'Product/product_class_add', '226-228-241', '2', null);
INSERT INTO `web_power` VALUES ('242', '227', '修改产品分类', 'Product/productclassedit', '226-227-242', '2', null);
INSERT INTO `web_power` VALUES ('243', '227', '删除产品分类', 'Product/product_class_del', '226-227-243', '2', null);
INSERT INTO `web_power` VALUES ('244', '227', '编辑产品实时价格', 'Product/productFloatNumeber', '226-227-244', '2', null);
INSERT INTO `web_power` VALUES ('247', '246', '认购记录', 'Order/kucun', '246-247', '1', '');
INSERT INTO `web_power` VALUES ('254', '86', '文章分类', 'Product/productclass', '254-11', '1', null);
INSERT INTO `web_power` VALUES ('256', '226', '产品分类', 'Product/classxian', '256-226', '1', '');
INSERT INTO `web_power` VALUES ('257', '201', '提现列表', 'Report/tixian', '257-201', '1', '');
INSERT INTO `web_power` VALUES ('260', '85', '留言列表', 'member/leaving', '260-1', '1', null);
INSERT INTO `web_power` VALUES ('262', '257', '下载权限', 'report/xiazai', '262-257-201', '2', null);
INSERT INTO `web_power` VALUES ('263', '201', '充值明细', 'Report/recharge', '263-201', '1', null);
INSERT INTO `web_power` VALUES ('264', '201', '购买订单', 'Orderd/index', '264-201', '1', null);

-- ----------------------------
-- Table structure for web_product
-- ----------------------------
DROP TABLE IF EXISTS `web_product`;
CREATE TABLE `web_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品',
  `title` char(40) DEFAULT NULL COMMENT '标题',
  `create_date` decimal(13,0) DEFAULT NULL COMMENT '发布时间',
  `content` varchar(255) DEFAULT NULL COMMENT '发布内容',
  `num` int(11) DEFAULT NULL COMMENT '发布数量',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '发布价格',
  `type_id` int(11) DEFAULT NULL COMMENT '发布类型',
  `buy_num` int(11) DEFAULT '0' COMMENT '购买量',
  `status` tinyint(1) DEFAULT '1' COMMENT '1默认上架',
  `update_time` char(50) DEFAULT NULL,
  `name_bei` decimal(4,2) DEFAULT '0.00' COMMENT '投资产品返还倍率0为没有倍率',
  `name_tian` tinyint(4) DEFAULT '0' COMMENT '投资产品返还天数0为没有返还天数',
  `gou_num` tinyint(4) DEFAULT '0' COMMENT '会员限制购买数量0无限制，大于0为限制次数',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='产品表';

-- ----------------------------
-- Records of web_product
-- ----------------------------
INSERT INTO `web_product` VALUES ('1', '500分红卡', '1512354873', null, null, '500.00', '36', '0', '1', null, '2.00', '60', '0');
INSERT INTO `web_product` VALUES ('2', '1000分红卡', '1512354894', null, null, '1000.00', '36', '0', '1', null, '2.00', '60', '0');
INSERT INTO `web_product` VALUES ('3', '2000分红卡', '1512354911', null, null, '2000.00', '36', '0', '1', null, '2.00', '60', '0');
INSERT INTO `web_product` VALUES ('4', '3000分红卡', '1512354936', null, null, '3000.00', '36', '0', '1', null, '2.00', '60', '0');
INSERT INTO `web_product` VALUES ('5', '5000分红卡', '1512354952', null, null, '5000.00', '36', '0', '1', null, '1.80', '60', '0');
INSERT INTO `web_product` VALUES ('6', '100众筹卡', '1512356298', null, null, '100.00', '30', '101', '1', null, '0.00', '0', '5');
INSERT INTO `web_product` VALUES ('7', '500众筹卡', '1512356324', null, null, '500.00', '30', '4', '1', null, '0.00', '0', '10');
INSERT INTO `web_product` VALUES ('8', '1000众筹卡', '1512356353', null, null, '1000.00', '30', '1', '1', null, '0.00', '0', '10');
INSERT INTO `web_product` VALUES ('9', '1500众筹卡', '1512356375', null, null, '1500.00', '30', '0', '1', null, '0.00', '0', '10');

-- ----------------------------
-- Table structure for web_recharge
-- ----------------------------
DROP TABLE IF EXISTS `web_recharge`;
CREATE TABLE `web_recharge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '充值表自增id',
  `rmb` decimal(10,2) DEFAULT NULL COMMENT '充值人民币',
  `recharge_date` int(11) unsigned DEFAULT NULL COMMENT '充值时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '1充值成功2充值失败',
  `uid` int(6) DEFAULT NULL COMMENT '充值会员id',
  `type` tinyint(1) DEFAULT '1' COMMENT '1充值2扣款',
  `orders` char(50) DEFAULT NULL COMMENT '充值订单号',
  `admin_id` tinyint(3) DEFAULT NULL COMMENT '后台充值的id',
  `money` decimal(10,2) DEFAULT NULL COMMENT '充值金额',
  `payway` int(8) DEFAULT NULL COMMENT '第三方充值方式',
  `message` varchar(100) DEFAULT NULL COMMENT '扣款提示信息',
  `action` tinyint(1) DEFAULT '1' COMMENT '1手动充值2第三方充值',
  `success_status` tinyint(1) DEFAULT '1' COMMENT '1充值成功2充值失败3扣款',
  `bin_type` tinyint(1) DEFAULT '1' COMMENT '币种类型',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='充值表';

-- ----------------------------
-- Records of web_recharge
-- ----------------------------
INSERT INTO `web_recharge` VALUES ('27', null, '1512366235', '1', '4', '1', '2017120498565555', '1', '500.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('28', null, '1512366354', '1', '4', '1', '2017120451485354', '1', '1000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('29', null, '1512367766', '1', '9', '1', '2017120454994999', '1', '1000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('30', null, '1512367803', '1', '6', '1', '2017120498501009', '1', '500.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('31', null, '1512367882', '1', '9', '1', '2017120497985453', '1', '1000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('32', null, '1512369166', '1', '9', '1', '2017120410197561', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('33', null, '1512369187', '1', '8', '1', '2017120451541015', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('34', null, '1512369341', '1', '7', '1', '2017120410054985', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('35', null, '1512371640', '1', '9', '1', '2017120456524951', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('36', null, '1512371855', '1', '6', '1', '2017120410298504', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('37', null, '1512442356', '1', '11', '1', '2017120553495450', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('38', null, '1512442393', '1', '12', '1', '2017120557555010', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('39', null, '1512444600', '1', '14', '1', '2017120556101521', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('40', null, '1512444608', '1', '13', '1', '2017120548975199', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('41', null, '1512452533', '1', '15', '1', '2017120553101485', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('42', null, '1512456993', '1', '15', '1', '2017120549994953', '1', '10000.00', null, '手动充值', '1', '1', '1');
INSERT INTO `web_recharge` VALUES ('43', null, '1512461033', '1', '16', '1', '2017120557555610', '1', '5000.00', null, '手动充值', '1', '1', '1');

-- ----------------------------
-- Table structure for web_role
-- ----------------------------
DROP TABLE IF EXISTS `web_role`;
CREATE TABLE `web_role` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `remarks` varchar(100) CHARACTER SET gbk DEFAULT NULL,
  `power_id` varchar(10000) CHARACTER SET gbk DEFAULT NULL,
  `power_control_action` varchar(10000) CHARACTER SET gbk DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='分组表';

-- ----------------------------
-- Records of web_role
-- ----------------------------
INSERT INTO `web_role` VALUES ('19', '超级管理员', '一般都是傻B', '64,65,70,71,72,73,66,74,75,76,67,77,78,79,80,81,84,167,68,69,170,171,172,174,175,176,177,85,89,91,95,96,97,98,99,101,194,195,193,86,90,102,103,104,105,106,107,108,201,202', ',,Rbac/adminrole,Rbac/adminpermission,Rbac/adminlist,,Webconfig/index,Rbac/adminroleedit,Rbac/adminroleadd,Rbac/adminRoleDel,Rbac/datadelRole,Rbac/poweredit,Rbac/del,Rbac/datadelPower,Rbac/admin_stop,Rbac/admin_start,Rbac/adminedit,Rbac/adminpasswordedit,Rbac/adminDel,Rbac/datadelAdmin,,,Member/index,Article/index,Member/useradd,Member/user_start,Member/user_stop,Member/usershow,Member/useredit,Member/userpasswordedit,Member/userDel,Article/article_start,Article/article_stop,Article/datadelArticle,Article/articleDel,Article/articlezhang,Article/articleedit,Article/articleadd,Rbac/loginlog,Webconfig/banklist,Webconfig/bank_stop,Webconfig/bank_start,Webconfig/bankadd,Webconfig/bankedit,Webconfig/bankdel,Webconfig/setbonus,Member/usertree,Member/recharge,Member/deduct,,Report/recharge');
INSERT INTO `web_role` VALUES ('20', '小明', '今天需要学习', '64,65,70,71,72,73,66,74,75,76,67,77,78,79,80,81,84,167,68,69,177,85,89,91,95,96,97,98,99,101,194,195,193,260,86,90,102,103,104,105,106,107,108,254,201,257,262', ',,Rbac/adminrole,Rbac/adminpermission,Rbac/adminlist,,Webconfig/index,Rbac/adminroleedit,Rbac/adminroleadd,Rbac/adminRoleDel,Rbac/datadelRole,Rbac/poweredit,Rbac/del,Rbac/datadelPower,Rbac/admin_stop,Rbac/admin_start,Rbac/adminedit,Rbac/adminpasswordedit,Rbac/adminDel,Rbac/datadelAdmin,,,Member/index,Article/index,Member/useradd,Member/user_start,Member/user_stop,Member/usershow,Member/useredit,Member/userpasswordedit,Member/userDel,Article/article_start,Article/article_stop,Article/datadelArticle,Article/articleDel,Article/articlezhang,Article/articleedit,Article/articleadd,Rbac/loginlog,Webconfig/setbonus,Member/usertree,Member/recharge,Member/deduct,,Product/productclass,Report/tixian,member/leaving,report/xiazai');

-- ----------------------------
-- Table structure for web_touziconfig
-- ----------------------------
DROP TABLE IF EXISTS `web_touziconfig`;
CREATE TABLE `web_touziconfig` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '投资金额',
  `num_price` decimal(10,2) DEFAULT '0.00' COMMENT '赠送金额',
  `fan_tian` decimal(10,2) DEFAULT '0.00' COMMENT '每天返还',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_touziconfig
-- ----------------------------

-- ----------------------------
-- Table structure for web_webconfig
-- ----------------------------
DROP TABLE IF EXISTS `web_webconfig`;
CREATE TABLE `web_webconfig` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `describe` varchar(50) CHARACTER SET gbk DEFAULT NULL,
  `value` varchar(2000) CHARACTER SET gbk DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='基本设置表';

-- ----------------------------
-- Records of web_webconfig
-- ----------------------------
INSERT INTO `web_webconfig` VALUES ('1', '基本设置', '{\"smsusername\":\"jxcm2017\",\"smsservice\":\"\",\"smspassword\":\"jxcm2017\",\"onoff\":\"1\",\"chaoshi\":\"0\",\"webname\":\"\\u84dd\\u7ea2\\u9ec4\",\"weburl\":\"http:\\/\\/mall700.com\\/\",\"title\":\"\\u84dd\\u7ea2\\u9ec4\",\"keywords\":\"\\u84dd\\u7ea2\\u9ec4\",\"description\":\"\\u84dd\\u7ea2\\u9ec4\",\"copyright\":\"\",\"icp\":\"\",\"cnzz\":\"\",\"ip\":\"\",\"num\":\"4\"}');
INSERT INTO `web_webconfig` VALUES ('2', '参数设置', '{\"tixian_status\":\"1\",\"chongzhi_status\":\"1\",\"deposit_time_start\":\"0\",\"deposit_time_hour\":\"07\",\"deposit_time_minute\":\"00\",\"deposit_time_hour_stop\":\"12\",\"deposit_time_minute_stop\":\"00\",\"deposit_number\":\"1\",\"recommend_one\":\"5\",\"recommend_tow\":\"3\",\"recommend_three\":\"2\",\"iteration_of\":\"10\",\"iteration_percentage\":\"1\",\"team_people_num_zhi\":\"20\",\"team_people_num\":\"150\",\"relationship\":\"10\",\"bonus_money_team\":\"2\",\"team_people_num_zhis\":\"30\",\"team_people_num1\":\"300\",\"relationship1\":\"10\",\"bonus_money_team1\":\"3\",\"deduct_s\":\"20\",\"restitution_of\":\"9\",\"minmoney\":\"100\",\"maxmoney\":\"2500\",\"shouxufei\":\"2\",\"tixianjinemin\":\"100\",\"tixianjinemax\":\"100000\"}');
