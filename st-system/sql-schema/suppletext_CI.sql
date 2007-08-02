-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 01, 2007 at 09:13 PM
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

-- 
-- Dumping data for table `st_config`
-- 

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=192 ;

-- 
-- Dumping data for table `st_pages`
-- 

INSERT INTO `st_pages` (`id`, `tag`, `time`, `body`, `user`, `note`) VALUES 
(182, 'Special:Navigation', 1185714400, 'HomePage | [[http://www.suppletext.org/|suppleText]] | [[SandBox]] | //Put navigation links here//', '192.168.85.1', 'Navigation Links'),
(179, 'HomePage', 1185711506, '== Welcome to your new suppleText wiki! ==\n\nYou will want to replace this text with whatever you want to put on your new home page. This is done by clicking the Edit link in the bottom right-hand corner. Any time you want to edit this or any content page, just click on the link!', '192.168.85.1', 'Smaller header'),
(183, 'NavigationLinks', 1185714441, '', '192.168.85.1', 'Removed'),
(190, 'SandBox', 1185948743, '=== Welcome to the SandBox! ===\n\nThis page is for playing around with wiki syntax. Feel free to mess around!\n\nWe are going to edit again...\n\n\nasdfasdfwdf asdfasdf "><a href="asdf">asdf</a>\n\nTesting prep_for_form', '192.168.85.1', 'changed note'),
(191, 'SandBox', 1186016428, '=== Welcome to the SandBox! ===\n\nThis page is for playing around with wiki syntax. Feel free to mess around!\n\nWe are going to edit again...\n\n\nasdfasdfwdf asdfasdf "><a href="asdf">asdf</a>\n\nTesting prep_for_form\n\nAdding something else.', '192.168.85.1', 'new revision?'),
(84, 'Login', 2007, '<<< login >>>', '', ''),
(90, 'Logout', 2007, '<<< logout >>>', 'test', ''),
(138, 'FormattingRules', 2007, '== suppleText Formatting Guide ==\n\nNote: Anything between {{{ {{{ }}} and {{{  ~}}} }}} is not formatted.\n \n\nOnce you have read through this, test your formatting skills in the SandBox.\n== 1. Text Formatting ==\n\n{{{**I''m bold**}}}\n**I''m bold**\n\n{{{//I''m italic text!//}}}\n//I''m italic text!//\n\n{{{ {{{monospace text ~}}} }}}\n{{{monospace text}}}\n\n== 2. Headers ==\n\nUse between five = (for the biggest header) and two = (for the smallest header) on both sides of a text to render it as a header.\n\n{{{= Really big header =}}}\n= Really big header =\n\n{{{== Rather big header ==}}}\n== Rather big header ==\n\n{{{=== Medium header ===}}}\n=== Medium header ===\n\n{{{==== Not-so-big header ====}}}\n==== Not-so-big header ====\n\n{{{===== Smallish header =====}}}\n===== Smallish header =====\n\n{{{====== Smallest header ======}}}\n====== Smallest header ======\n\n== 3. Horizontal separator ==\n{{{----}}}\n----\n\n== 4. Forced line break ==\nThis is the first line,{{{\\\\}}}and this is the second.\n\nThis is the first line,\\\\and this is the second.\n\n== 5. Lists and indents ==\n\nCurrently, no indent functionality.\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ~):\n\n=== Bulleted lists ===\n{{{\n* Line one\n* Line two\n}}}\n\n* Line one\n* Line two\n\n=== Numbered lists ===\n{{{\n# Line one\n# Line two\n}}}\n\n# Line one\n# Line two\n\n== 7. Images ==\n\n{{{ {{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} }}}\n\n{{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} \n\n== 8. Links ==\n\nTo create a link to a wiki page you can use any of the following options:\n\n   1. type a WikiName:\n\n	  {{{FormattingRules}}}\n	  FormattingRules\n\n   2. add a forced link surrounding the page name by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[SandBox|Test your formatting skills]]}}}\n	  [[SandBox|Test your formatting skills]]\n\n	  {{{[[SandBox|沙箱]]}}}\n	  [[SandBox|沙箱]]\n\n   3. add an image with a link (see instructions above).\n\n\nTo link to external pages, you can do any of the following:\n\n   1. type a URL inside the page:\n\n	  {{{http://www.example.com}}}\n	  http://www.example.com\n\n   2. add a forced link surrounding the URL by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[http://example.com/jenna/|Jenna''s Home Page]]}}}\n	  [[http://example.com/jenna/|Jenna''s Home Page]]\n\n	  {{{[[mailto:mail@example.com|Write me!]]}}}\n	  [[mailto:mail@example.com|Write me!]]\n\n   3. add an image with a link (see instructions above);\n\n== 9. Tables ==\n\nAll cells are separated by single pipes. Leading spaces are permitted before the first cell of a row and trailing spaces are permitted at the end of a line. The ending pipe is optional. You can embed links, bold, italics, line breaks, and nowiki in table cells. Equal sign directly following pipe defines a header. Headers can be arranged horizontally or vertically.\n\nExample:\n{{{\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |\n}}}\n\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |', 'Anonymous', ''),
(133, 'FormattingRules2', 2007, '== suppleText Formatting Guide ==\n\nNote: Anything between {{{ {{{ }}} and {{{  ~}}} }}} is not formatted.\n \n\nOnce you have read through this, test your formatting skills in the SandBox.\n== 1. Text Formatting ==\n\n{{{**I''m bold**}}}\n**I''m bold**\n\n{{{//I''m italic text!//}}}\n//I''m italic text!//\n\n{{{ {{{monospace text ~}}} }}}\n{{{monospace text}}}\n\n== 2. Headers ==\n\nUse between five = (for the biggest header) and two = (for the smallest header) on both sides of a text to render it as a header.\n\n{{{= Really big header =}}}\n= Really big header =\n\n{{{== Rather big header ==}}}\n== Rather big header ==\n\n{{{=== Medium header ===}}}\n=== Medium header ===\n\n{{{==== Not-so-big header ====}}}\n==== Not-so-big header ====\n\n{{{===== Smallish header =====}}}\n===== Smallish header =====\n\n{{{====== Smallest header ======}}}\n====== Smallest header ======\n\n== 3. Horizontal separator ==\n{{{----}}}\n----\n\n== 4. Forced line break ==\nThis is the first line,{{{\\\\}}}and this is the second.\n\nThis is the first line,\\\\and this is the second.\n\n== 5. Lists and indents ==\n\nCurrently, no indent functionality.\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ~):\n\n=== Bulleted lists ===\n{{{\n* Line one\n* Line two\n}}}\n\n* Line one\n* Line two\n\n=== Numbered lists ===\n{{{\n# Line one\n# Line two\n}}}\n\n# Line one\n# Line two\n\n== 7. Images ==\n\n{{{ {{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} }}}\n\n{{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} \n\n== 8. Links ==\n\nTo create a link to a wiki page you can use any of the following options:\n\n   1. type a WikiName:\n\n	  {{{FormattingRules}}}\n	  FormattingRules\n\n   2. add a forced link surrounding the page name by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[SandBox|Test your formatting skills]]}}}\n	  [[SandBox|Test your formatting skills]]\n\n	  {{{[[SandBox|&#27801;&#31665;]]}}}\n	  [[SandBox|&#27801;&#31665;]]\n\n   3. add an image with a link (see instructions above).\n\n\nTo link to external pages, you can do any of the following:\n\n   1. type a URL inside the page:\n\n	  {{{http://www.example.com}}}\n	  http://www.example.com\n\n   2. add a forced link surrounding the URL by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[http://example.com/jenna/|Jenna''s Home Page]]}}}\n	  [[http://example.com/jenna/|Jenna''s Home Page]]\n\n	  {{{[[mailto:mail@example.com|Write me!]]}}}\n	  [[mailto:mail@example.com|Write me!]]\n\n   3. add an image with a link (see instructions above);\n\n== 9. Tables ==\n\nAll cells are separated by single pipes. Leading spaces are permitted before the first cell of a row and trailing spaces are permitted at the end of a line. The ending pipe is optional. You can embed links, bold, italics, line breaks, and nowiki in table cells. Equal sign directly following pipe defines a header. Headers can be arranged horizontally or vertically.\n\nExample:\n{{{\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |\n}}}\n\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |', 'Anonymous', ''),
(135, 'Special:Test', 2007, 'Will this text be included?', 'Anonymous', ''),
(160, 'SandBox2', 2007, '= SandBox 2 =\r\n\r\nRequiem, testing. Sibilance.\r\n\r\n'''';!--"<XSS>=&{()}\r\n\r\n{{'''';!--"<XSS>=&{()}}}\r\n[['''';!--"<XSS>=&{()}]]\r\n\r\n{{http://www.google.com/\\0 javascript:alert(''XSS'');}}\r\n\r\n<script>\r\nalert(document.cookie);\r\n</script>\r\n\r\nHTML Injection:\r\n<a href="http://www.mikexstudios.com">Visit my blog!</a>\r\n\r\nInserting something using the new database connection.\r\n\r\nThis is the first version! Still work? Again? Even Again? There''s a dog. And there''s a cat.\r\n\r\nThis is the second version! Is everything still working?\r\n\r\nThird version.\r\n\r\n<<<include Special:Test>>>\r\n\r\nFourth version.\r\n\r\nPlead the Fifth.\r\n\r\nThe sixth.\r\n\r\nSeventh.\r\n\r\n{{{ {{{monospace ~}}} }}}\r\nhello\r\n{{{I am the pillows}}}\r\n\r\n{{{\r\npre thing\r\n}}}\r\nsomething afterwards\r\n\r\n[[mailto:me@asdf.com|me@adsf.com]]\r\n\r\n[[mailto:me@asddf.com]]\r\n\r\nTesting image with link:\r\n\r\n[[http://www.suppletext.org|{{http://www.suppletext.org/logo-medium.gif|suppleText Logo}}]]\r\n\r\nThree closing braces: {{{ ~}}} }}}\r\n\r\nThree opening braces: {{{ {{{ }}}\r\n\r\nThis is a {{{ * }}} star. This is a dollar sign: {{{ $ }}}.\r\n\r\n= SandBox2 : [[HomePage|My suppleText wiki]] =\r\n\r\n\r\n<<<include NavigationLinks>>>\r\n\r\nTesting again.', 'Anonymous', 'added some text');

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

-- 
-- Dumping data for table `st_sessions`
-- 

INSERT INTO `st_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`) VALUES 
('afa83c882da686c32dca4a4c31cc356a', '192.168.85.1', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv', 1185906364),
('0049bac2533165f623a3d8c8c58aef08', '192.168.85.1', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv', 1185695680),
('9cd09974887fdb94a436aa63ee36ae96', '192.168.85.1', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv', 1185907214),
('2d9f0a9a3f7f0d95bf6a5bd1c6415048', '192.168.85.1', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv', 1185914440),
('d4dcb038d68df2ebb36b6bdec2f532af', '192.168.85.1', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv', 1185966547),
('da56c7ef1fc7f2726bce4c9a1e4b1101', '192.168.85.1', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv', 1186017121);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `st_users`
-- 

INSERT INTO `st_users` (`id`, `username`, `key`, `value`, `attribute`) VALUES 
(1, 'test', 'uid', '1', ''),
(9, 'test', 'password', '9b11ed77f0c03a1598bde1feff751e75f1326675', '');
