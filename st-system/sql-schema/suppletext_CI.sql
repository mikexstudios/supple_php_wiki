-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 04, 2007 at 02:17 PM
-- Server version: 5.0.38
-- PHP Version: 5.2.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `suppletext_CI`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `st_config`
-- 

DROP TABLE IF EXISTS `st_config`;
CREATE TABLE IF NOT EXISTS `st_config` (
  `order` smallint(5) unsigned NOT NULL default '0',
  `option` varchar(50) NOT NULL default '',
  `value` text NOT NULL,
  `vartype` enum('string','number','boolean') NOT NULL default 'string',
  `displaycode` varchar(20) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`option`),
  UNIQUE KEY `order` (`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `st_config` (`order`, `option`, `value`, `vartype`, `displaycode`, `name`, `description`) VALUES 
(4, 'is_rewrite', 'true', 'boolean', 'yesno', 'Enable Pretty URLs', 'Set to yes if proper .htaccess file is created with the necessary mod_rewrite code'),
(1, 'root_page', 'HomePage', 'string', 'string', 'Default Page', 'The page that is displayed when no page is specified.'),
(0, 'site_name', 'My suppleText wiki', 'string', 'text', 'Wiki Site Name', 'A title for your wiki'),
(5, 'site_url', '/suppleText', 'string', 'text', 'Site URL', 'Absolute or relative path to script location. No trailing slash.'),
(6, 'use_cache', 'false', 'boolean', 'yesno', 'Use Page Caching', 'If set to true, stores syntax parsed pages in database and loads the cached version to users.'),
(2, 'use_theme', 'supple', 'string', 'text', 'Use Theme', 'The theme used to display pages');

-- --------------------------------------------------------

-- 
-- Table structure for table `st_pages`
-- 

DROP TABLE IF EXISTS `st_pages`;
CREATE TABLE IF NOT EXISTS `st_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag` varchar(75) NOT NULL default '',
  `time` int(10) NOT NULL,
  `body` mediumtext NOT NULL,
  `user` varchar(75) NOT NULL default '',
  `note` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idx_tag` (`tag`),
  KEY `idx_time` (`time`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `st_sessions`
-- 

DROP TABLE IF EXISTS `st_sessions`;
CREATE TABLE IF NOT EXISTS `st_sessions` (
  `session_id` varchar(40) character set latin1 NOT NULL default '0',
  `ip_address` varchar(16) character set latin1 NOT NULL default '0',
  `user_agent` varchar(50) character set latin1 NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `st_users`
-- 

DROP TABLE IF EXISTS `st_users`;
CREATE TABLE IF NOT EXISTS `st_users` (
  `id` int(100) NOT NULL auto_increment,
  `username` varchar(100) character set latin1 NOT NULL,
  `key` varchar(100) character set latin1 NOT NULL,
  `value` varchar(100) character set latin1 NOT NULL,
  `attribute` varchar(50) character set latin1 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
