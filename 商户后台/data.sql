-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-12-05 14:19:29
-- 服务器版本： 5.7.40-log
-- PHP 版本： 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `epusdt`
--

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
  `trade_id` varchar(32) NOT NULL COMMENT 'epusdt订单号',
  `order_id` varchar(32) NOT NULL COMMENT '客户交易id',
  `product_name` varchar(50) DEFAULT NULL COMMENT '产口名称',
  `user_flag` varchar(20) DEFAULT NULL COMMENT '用户标识',
  `block_transaction_id` varchar(128) DEFAULT NULL COMMENT '区块唯一编号',
  `currency` varchar(10) DEFAULT NULL COMMENT '币种',
  `order_amount` decimal(19,4) NOT NULL DEFAULT '0.0000' COMMENT '订单金额',
  `actual_amount` decimal(19,4) NOT NULL COMMENT '订单实际需要支付的金额，保留4位小数',
  `amount` decimal(19,4) NOT NULL COMMENT '订单金额（CNY），保留4位小数',
  `token` varchar(50) NOT NULL COMMENT '所属钱包地址',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1：等待支付，2：支付成功，3：已过期',
  `notify_url` varchar(128) NOT NULL COMMENT '异步回调地址',
  `notify_url2` varchar(255) DEFAULT NULL,
  `redirect_url` varchar(128) DEFAULT NULL COMMENT '同步回调地址',
  `callback_num` int(11) DEFAULT '0' COMMENT '回调次数',
  `callback_confirm` int(11) DEFAULT '2' COMMENT '回调是否已确认？ 1是 2否',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `rate` float(4,2) NOT NULL DEFAULT '0.00' COMMENT '费率',
  `charged` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `orders`
--

INSERT INTO `orders` (`id`, `merchant_id`, `trade_id`, `order_id`, `product_name`, `user_flag`, `block_transaction_id`, `currency`, `order_amount`, `actual_amount`, `amount`, `token`, `status`, `notify_url`, `notify_url2`, `redirect_url`, `callback_num`, `callback_confirm`, `created_at`, `updated_at`, `deleted_at`, `rate`, `charged`) VALUES
(1, 100001, '202312011701422558746083', 'R202312011722388074', '手续费充值', '100007', '', 'USD', 10.0000, 10.0000, 70.0000, 'TGPzzSDzuWchyxEGRgBGuL2ZUm7yJMNXM8', 3, 'https://pro.tronpay.co/api/transaction/notify', 'https://pro.tronpay.co/merchant/recharge/notify', 'https://pro.tronpay.co/merchant/recharge/success', 0, 0, '2023-12-01 09:22:39', '2023-12-01 09:32:42', NULL, 0.00, 0),
(2, 100001, '202312011701423556341504', 'R202312011739163833', '手续费充值', '100007', '', 'USD', 10.0000, 10.0000, 70.0000, 'TGPzzSDzuWchyxEGRgBGuL2ZUm7yJMNXM8', 3, 'https://pro.tronpay.co/api/transaction/notify', 'https://pro.tronpay.co/merchant/recharge/notify', 'https://pro.tronpay.co/merchant/recharge/success', 0, 0, '2023-12-01 09:39:16', '2023-12-01 09:49:21', NULL, 0.00, 0),
(3, 100001, '202312011701423660521609', 'R202312011741004576', '手续费充值', '100007', '', 'USD', 10.0000, 10.0010, 70.0000, 'TGPzzSDzuWchyxEGRgBGuL2ZUm7yJMNXM8', 3, 'https://pro.tronpay.co/api/transaction/notify', 'https://pro.tronpay.co/merchant/recharge/notify', 'https://pro.tronpay.co/merchant/recharge/success', 0, 0, '2023-12-01 09:41:01', '2023-12-01 09:51:01', NULL, 0.00, 0);

-- --------------------------------------------------------

--
-- 表的结构 `tron_account_detail`
--

CREATE TABLE `tron_account_detail` (
  `id` int(11) NOT NULL COMMENT '主键ID',
  `merchant_id` int(11) NOT NULL DEFAULT '0' COMMENT '商户ID',
  `type` varchar(20) DEFAULT NULL COMMENT '类型',
  `relate_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `change` float(10,4) NOT NULL DEFAULT '0.0000' COMMENT '金额变动',
  `balance` float(10,4) NOT NULL DEFAULT '0.0000' COMMENT '变动后余额',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户账户明细表';

--
-- 转存表中的数据 `tron_account_detail`
--

INSERT INTO `tron_account_detail` (`id`, `merchant_id`, `type`, `relate_id`, `change`, `balance`, `add_time`) VALUES
(1, 100001, 'system', 0, 5.0000, 5.0000, 1691660353),
(4, 100007, 'system', 0, 5.0000, 5.0000, 1696918773);

-- --------------------------------------------------------

--
-- 表的结构 `tron_merchant`
--

CREATE TABLE `tron_merchant` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `username` varchar(20) DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) DEFAULT '' COMMENT '昵称',
  `password` varchar(32) DEFAULT '' COMMENT '密码',
  `salt` varchar(30) DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `email` varchar(100) DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机号码',
  `login_failure` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `login_time` bigint(16) DEFAULT NULL COMMENT '登录时间',
  `login_ip` varchar(50) DEFAULT NULL COMMENT '登录IP',
  `create_time` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) DEFAULT '' COMMENT 'Session标识',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0未审核 1已审核 -1已禁用',
  `balance` float(10,4) NOT NULL DEFAULT '0.0000' COMMENT '账户余额',
  `rate` float(4,2) NOT NULL DEFAULT '0.00' COMMENT '当前费率'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户表';

--
-- 转存表中的数据 `tron_merchant`
--

INSERT INTO `tron_merchant` (`id`, `username`, `nickname`, `password`, `salt`, `avatar`, `email`, `mobile`, `login_failure`, `login_time`, `login_ip`, `create_time`, `update_time`, `token`, `status`, `balance`, `rate`) VALUES
(100001, 'altheran', 'altheran', '932c2c60796253e3d2cd48f3e88b50f5', 'abcd', '/assets/img/avatar.png', 'admin@admin.com', '15821794791', 0, 1701421841, '124.77.13.165', 1491635035, 1701421841, '6569a311584d9', 1, 5.0000, 2.50),
(100007, 'altheran2', 'altheran2', '370e0e712f867a6b57f1db7cd05caba6', '1204', '/assets/img/avatar.png', 'altheran2@gmail.com', '', 0, 1701422454, '43.155.32.92', 1696918773, 1701422454, '6569a576bdb5d', 1, 5.0000, 2.50);

-- --------------------------------------------------------

--
-- 表的结构 `tron_merchant_param`
--

CREATE TABLE `tron_merchant_param` (
  `id` int(11) NOT NULL,
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `notify_email` varchar(50) DEFAULT NULL,
  `order_complete` tinyint(1) NOT NULL DEFAULT '0',
  `balance_not_enough` tinyint(1) NOT NULL DEFAULT '0',
  `login_success` tinyint(1) NOT NULL DEFAULT '0',
  `google_secret` varchar(50) DEFAULT NULL,
  `notify_url` int(11) DEFAULT NULL,
  `white_list` text,
  `secret` varchar(50) DEFAULT NULL,
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `tron_merchant_param`
--

INSERT INTO `tron_merchant_param` (`id`, `merchant_id`, `notify_email`, `order_complete`, `balance_not_enough`, `login_success`, `google_secret`, `notify_url`, `white_list`, `secret`, `add_time`, `update_time`) VALUES
(1, 100001, 'admin@admin.com', 1, 1, 1, '6OD32ENFT4L2TEDE', 0, '', 'A4mauT8hR6VdGr0qTH', 0, 1692950092),
(3, 100007, 'altheran2@gmail.com', 0, 0, 0, NULL, NULL, NULL, 'cvtw0hQmZoFIV5dfA8', 1696918773, 1696918773);

-- --------------------------------------------------------

--
-- 表的结构 `tron_wallet_address`
--

CREATE TABLE `tron_wallet_address` (
  `id` int(11) NOT NULL COMMENT '主键ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
  `wallet_address` varchar(50) NOT NULL COMMENT '钱包地址',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1删除 0已禁用 1已启用',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `tron_wallet_address`
--

INSERT INTO `tron_wallet_address` (`id`, `merchant_id`, `wallet_address`, `status`, `add_time`, `update_time`) VALUES
(1, 100001, 'TGPzzSDzuWchyxEGRgBGuL2ZUm7yJMNXM8', 1, 1687748103, 1687748103);

-- --------------------------------------------------------

--
-- 表的结构 `wallet_address`
--

CREATE TABLE `wallet_address` (
  `id` int(11) NOT NULL,
  `token` varchar(50) NOT NULL COMMENT '钱包token',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1:启用 2:禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='钱包表';

--
-- 转存表中的数据 `wallet_address`
--

INSERT INTO `wallet_address` (`id`, `token`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TGPzzSDzuWchyxEGRgBGuL2ZUm7yJMNXM8', 1, '2023-05-26 08:16:53', '2023-05-26 08:16:53', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_id_uindex` (`order_id`),
  ADD UNIQUE KEY `orders_trade_id_uindex` (`trade_id`),
  ADD KEY `orders_block_transaction_id_index` (`block_transaction_id`);

--
-- 表的索引 `tron_account_detail`
--
ALTER TABLE `tron_account_detail`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `tron_merchant`
--
ALTER TABLE `tron_merchant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE;

--
-- 表的索引 `tron_merchant_param`
--
ALTER TABLE `tron_merchant_param`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `tron_wallet_address`
--
ALTER TABLE `tron_wallet_address`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `wallet_address`
--
ALTER TABLE `wallet_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_address_token_index` (`token`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `tron_account_detail`
--
ALTER TABLE `tron_account_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `tron_merchant`
--
ALTER TABLE `tron_merchant`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=100008;

--
-- 使用表AUTO_INCREMENT `tron_merchant_param`
--
ALTER TABLE `tron_merchant_param`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `tron_wallet_address`
--
ALTER TABLE `tron_wallet_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `wallet_address`
--
ALTER TABLE `wallet_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
