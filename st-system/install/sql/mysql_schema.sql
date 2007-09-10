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
  `id` int(5) NOT NULL auto_increment,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10;

INSERT INTO `st_config` (`id`, `key`, `value`) VALUES 
(1, 'is_rewrite', 'true'),
(2, 'root_page', 'HomePage'),
(4, 'use_cache', 'false'),
(5, 'use_theme', 'supple'),
(6, 'version', '0.3.0'),
(7, 'default_read_permission', 'Anonymous,Registered,Editor'),
(8, 'default_write_permission', 'Registered,Editor');

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

INSERT INTO `st_pages` (`id`, `tag`, `time`, `body`, `user`, `note`) VALUES 
(1, 'HomePage', UNIX_TIMESTAMP(), '== Welcome to your new suppleText wiki! ==\n\nYou will want to replace this text with whatever you want to put on your new home page. This is done by clicking the Edit link in the bottom right-hand corner. Any time you want to edit this or any content page, just click on the link!\n\nSome pages you might want to check out:\n* FormattingRules - read through the simple syntax used to create and edit pages.\n* SandBox - play around with the syntax here.\n\n(Note: If you would like to edit the navigation bar in the header of this theme, edit [[Special:Navigation]].)', 'suppleText', 'Initial Setup'),
(2, 'SandBox', UNIX_TIMESTAMP(), '=== Welcome to the SandBox! ===\n\nThis page is for playing around with wiki syntax. Feel free to mess around!', 'suppleText', 'Initial Setup'),
(3, 'Special:Navigation', UNIX_TIMESTAMP(), '[[HomePage|Home]] | [[http://www.suppletext.org/|suppleText]] | [[SandBox]] | //Put navigation links here by editing [[Special:Navigation]]//', 'suppleText', 'Initial Setup'),
(4, 'FormattingRules', UNIX_TIMESTAMP(), '== suppleText Formatting Guide ==\n\nNote: Anything between {{{ {{{ }}} and {{{  ~}}} }}} is not formatted.\n \n\nOnce you have read through this, test your formatting skills in the SandBox.\n== 1. Text Formatting ==\n\n{{{**I''m bold**}}}\n**I''m bold**\n\n{{{//I''m italic text!//}}}\n//I''m italic text!//\n\n{{{ {{{monospace text ~}}} }}}\n{{{monospace text}}}\n\n== 2. Headers ==\n\nUse between five = (for the biggest header) and two = (for the smallest header) on both sides of a text to render it as a header.\n\n{{{= Really big header =}}}\n= Really big header =\n\n{{{== Rather big header ==}}}\n== Rather big header ==\n\n{{{=== Medium header ===}}}\n=== Medium header ===\n\n{{{==== Not-so-big header ====}}}\n==== Not-so-big header ====\n\n{{{===== Smallish header =====}}}\n===== Smallish header =====\n\n{{{====== Smallest header ======}}}\n====== Smallest header ======\n\n== 3. Horizontal separator ==\n{{{----}}}\n----\n\n== 4. Forced line break ==\nThis is the first line,{{{\\\\}}}and this is the second.\n\nThis is the first line,\\\\and this is the second.\n\n== 5. Lists and indents ==\n\nCurrently, no indent functionality.\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ~):\n\n=== Bulleted lists ===\n{{{\n* Line one\n* Line two\n}}}\n\n* Line one\n* Line two\n\n=== Numbered lists ===\n{{{\n# Line one\n# Line two\n}}}\n\n# Line one\n# Line two\n\n== 7. Images ==\n\n{{{ {{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} }}}\n\n{{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} \n\n== 8. Links ==\n\nTo create a link to a wiki page you can use any of the following options:\n\n   1. type a WikiName:\n\n	  {{{FormattingRules}}}\n	  FormattingRules\n\n   2. add a forced link surrounding the page name by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[SandBox|Test your formatting skills]]}}}\n	  [[SandBox|Test your formatting skills]]\n\n	  {{{[[SandBox|沙箱]]}}}\n	  [[SandBox|沙箱]]\n\n   3. add an image with a link (see instructions above).\n\n\nTo link to external pages, you can do any of the following:\n\n   1. type a URL inside the page:\n\n	  {{{http://www.example.com}}}\n	  http://www.example.com\n\n   2. add a forced link surrounding the URL by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[http://example.com/jenna/|Jenna''s Home Page]]}}}\n	  [[http://example.com/jenna/|Jenna''s Home Page]]\n\n	  {{{[[mailto:mail@example.com|Write me!]]}}}\n	  [[mailto:mail@example.com|Write me!]]\n\n   3. add an image with a link (see instructions above);\n\n== 9. Tables ==\n\nAll cells are separated by single pipes. Leading spaces are permitted before the first cell of a row and trailing spaces are permitted at the end of a line. The ending pipe is optional. You can embed links, bold, italics, line breaks, and nowiki in table cells. Equal sign directly following pipe defines a header. Headers can be arranged horizontally or vertically.\n\nExample:\n{{{\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |\n}}}\n\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |', 'suppleText', 'Initial Setup');

-- --------------------------------------------------------

-- 
-- Table structure for table `st_page_metadata`
-- 

CREATE TABLE IF NOT EXISTS `st_page_metadata` (
  `id` int(100) NOT NULL auto_increment,
  `pagename` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `attribute` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
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
  `username` varchar(100) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `attribute` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
