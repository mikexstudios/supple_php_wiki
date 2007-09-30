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
(6, 'version', '0.4.0'),
(7, 'default_read_permission', 'Anonymous'),
(8, 'default_write_permission', 'Registered');

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
(4, 'FormattingRules', UNIX_TIMESTAMP(), '== suppleText Formatting Guide ==\n\nNote: Anything between {{{ {{{ }}} and {{{  ~}}} }}} is not formatted.\n \n\nOnce you have read through this, test your formatting skills in the SandBox.\n== 1. Text Formatting ==\n\n{{{**I''m bold**}}}\n**I''m bold**\n\n{{{//I''m italic text!//}}}\n//I''m italic text!//\n\n{{{And I''m __underlined__!}}}\nAnd I''m __underlined__!\n\n{{{ {{{monospace text ~}}} }}} (//the text inside will not be parsed//)\n{{{monospace text}}}\n\n{{{!!highlight text!!}}}\n!!highlight text!!\n\n{{{This is ^^superscripted^^.}}}\nThis is ^^superscripted^^.\n\n{{{This is ,,subscripted,,.}}}\nThis is ,,subscripted,,.\n\n== 2. Headers ==\n\nUse between five = (for the biggest header) and two = (for the smallest header) on both sides of a text to render it as a header.\n\n{{{= Really big header =}}}\n= Really big header =\n\n{{{== Rather big header ==}}}\n== Rather big header ==\n\n{{{=== Medium header ===}}}\n=== Medium header ===\n\n{{{==== Not-so-big header ====}}}\n==== Not-so-big header ====\n\n{{{===== Smallish header =====}}}\n===== Smallish header =====\n\n{{{====== Smallest header ======}}}\n====== Smallest header ======\n\n== 3. Horizontal separator ==\n{{{----}}}\n----\n\n== 4. Forced line break ==\nThis is the first line,{{{\\\\}}}and this is the second.\n\nThis is the first line,\\\\and this is the second.\n\n== 5. Lists and indents ==\n\nYou can indent text using 4 spaces (which will auto-convert into a tab).\n\n{{{\n    This text is indented.\n}}}\n    This text is indented\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ~):\n\n=== Bulleted lists ===\n{{{\n* Line one\n* Line two\n}}}\n\n* Line one\n* Line two\n\n=== Numbered lists ===\n{{{\n# Line one\n# Line two\n}}}\n\n# Line one\n# Line two\n\n== 7. Images ==\n\n{{{ {{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} }}}\n\n{{http://www.suppletext.org/logo-medium.gif|suppleText Logo}} \n\n== 8. Links ==\n\nTo create a **link to a wiki page** you can use any of the following options:\n\n   1. type a WikiName:\n\n	  {{{FormattingRules}}}\n	  FormattingRules\n\n   2. add a forced link surrounding the page name by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[SandBox|Test your formatting skills]]}}}\n	  [[SandBox|Test your formatting skills]]\n\n	  {{{[[SandBox|沙箱]]}}}\n	  [[SandBox|沙箱]]\n\n   3. add an image with a link (see instructions above).\n\n\nTo **link to external pages**, you can do any of the following:\n\n   1. type a URL inside the page:\n\n	  {{{http://www.example.com}}}\n	  http://www.example.com\n\n   2. add a forced link surrounding the URL by {{{[[}}} and {{{]]}}} (everything after the | will be shown as description):\n\n	  {{{[[http://example.com/jenna/|Jenna''s Home Page]]}}}\n	  [[http://example.com/jenna/|Jenna''s Home Page]]\n\n	  {{{[[mailto:mail@example.com|Write me!]]}}}\n	  [[mailto:mail@example.com|Write me!]]\n\n   3. add an image with a link:\n	  {{{[[http://www.suppletext.org|{{http://www.suppletext.org/logo-medium.gif|suppleText Logo}}]]}}}\n	  [[http://www.suppletext.org|{{http://www.suppletext.org/logo-medium.gif|suppleText Logo}}]]\n\n== 9. No Formatting ==\n\nWhen you want to disable all wiki formatting, surround the text in the {{{ {{{ }}} and {{{  ~}}} }}} characters.\n\nExample:\n{{{\n{{{\n//This// does **not** get [[formatted]] \n~}}}\n}}}\n\n{{{\n//This// does **not** get [[formatted]] \n}}}\n\nThe escape character disables the automatic conversion of the URLs to links, and any similar mechanisms like camelcase wikiwords, copyright sign, etc. //Tip//: The escape character is also commonly used to resolve conflicts in wiki syntax. For instance, if you wanted to display {{{ ~~} ~}} }}}, you would write: {{{ {{{  ~~} ~}}  ~}}} }}}.\n\nExample:\n{{{\n~~#1\nhttp://www.foo.com/~bar/\n~~http://www.foo.com/\nCamelCaseLink\n~~CamelCaseLink\n}}}\n\n~#1\nhttp://www.foo.com/~bar/\n~http://www.foo.com/\nCamelCaseLink\n~CamelCaseLink\n\n== 10. Tables ==\n\nAll cells are separated by single pipes. Leading spaces are permitted before the first cell of a row and trailing spaces are permitted at the end of a line. The ending pipe is optional. You can embed links, bold, italics, line breaks, and nowiki in table cells. Equal sign directly following pipe defines a header. Headers can be arranged horizontally or vertically.\n\nExample:\n{{{\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |\n}}}\n\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |\n\nNote: !!Complex tables can also be created by embedding HTML code in a wiki page!! (see instructions below).\n\n== 11. Floats ==\n\nTo create a !!left floated box!!, wrap the text in a <div> with class ''floatl''.\n\nExample:\n{{{\n<div class="floatl">\nSome text in a left-floated box hanging around\n</div>\nSome more text as a filler. Some more text as a filler. Some more text \nas a filler. Some more text as a filler. Some more text as a filler. \nSome more text as a filler. Some more text as a filler. Some more text \nas a filler.\n}}}\n\n<div class="floatl">\nSome text in a left-floated box hanging around\n</div>\nSome more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler.\n\nTo create a !!right floated box!!, wrap the text in a <div> with class ''floatr''.\n\nExample:\n{{{\n<div class="floatr">\nSome text in a right-floated box hanging around\n</div>\nSome more text as a filler. Some more text as a filler. Some more text \nas a filler. Some more text as a filler. Some more text as a filler. \nSome more text as a filler. Some more text as a filler. Some more text \nas a filler.\n}}}\n\n<div class="floatr">\nSome text in a right-floated box hanging around\n</div>\nSome more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler.\n\n\n== 12. Embedded HTML ==\n\nSometimes, wiki syntax is just not sufficient for some of the complex things you want to do. Therefore, you have the option to add your own HTML code. Just wrap the HTML in the {{{<ht ~ml>}}} and {{{</ht ~ml>}}} tags.\n\nExamples:\n\n{{{\n<ht ~ml>\n<acronym title="Cascade Style Sheet">CSS</acronym>\n</html>\n}}}\n\n<html>\n<acronym title="Cascade Style Sheet">CSS</acronym>\n</html>\n\n(//HTML is secured by the [[http://www.htmlpurifier.com|HTMLPurifier]] library//.)\n', 'suppleText', 'Initial Setup'),
(5, 'AbridgedFormattingRules', UNIX_TIMESTAMP(), '== Quick Formatting Rules ==\n\nHere are some of the more commonly used markup rules. For the full version, please visit the FormattingRules page (//make sure you open it in a new window so that you don''t lose any changes you have made//). [[http://www.wikicreole.org/wiki/Creole1.0|Creole 1.0]] syntax is fully supported.\n\n=== Basics ===\n<html>\n<table width="100%" border="0" cellspacing="0" cellpadding="5">\n	<tr class="alternate">\n		<td width="120"><strong>Bold</strong></td>\n		<td>**Your Text**</td>\n		<td><strong>Your Text</strong></td>\n	</tr>\n	<tr>\n		<td><strong>Italic</strong></td>\n		<td>//Your Text//</td>\n		<td><em>Your Text</em></td>\n	</tr>\n	<tr class="alternate">\n		<td><strong>Links</strong></td>\n		<td>[[SandBox]]</td>\n		<td><a href="SandBox">SandBox</a></td>\n	</tr>\n	<tr>\n		<td></td>\n		<td>[[SandBox|Your Text]]</td>\n		<td><a href="SandBox">Your Text</a></td>\n	</tr>\n	<tr class="alternate">\n		<td></td>\n		<td>[[http://www.suppletext.com/|Free wiki!]]</td>\n		<td><a href="http://www.suppletext.com/">Free wiki!</a></td>\n	</tr>\n</table>\n</html>\n\n=== Intermediate ===\n<html>\n<table width="100%" border="0" cellspacing="0" cellpadding="5">\n	<tr class="alternate">\n		<td width="120"><strong>Headers</strong></td>\n		<td>== Large heading ==</td>\n		<td><h2>Large heading</h2></td>\n	</tr>\n	<tr>\n		<td></td>\n		<td>=== Medium heading ===</td>\n		<td><h3>Medium heading</h3></td>\n	</tr>\n	<tr class="alternate">\n		<td></td>\n		<td>==== Small heading ====</td>\n		<td><h4>Small heading</h4></td>\n	</tr>\n	<tr>\n		<td><strong>Lists</strong></td>\n		<td>\n			* Bullet List<br />\n			* Second Item<br />\n			** Sub Item			\n		</td>\n		<td>\n			<ul>\n				<li>Bullet List</li>\n				<li>\n					Second Item\n					<ul>\n						<li>Sub Item</li>\n					</ul>\n				</li>\n			</ul>\n		</td>\n	</tr>\n	<tr class="alternate">\n		<td></td>\n		<td>\n			# Numbered List<br />\n			# Second Item<br />\n			## Sub Item			\n		</td>\n		<td>\n			<ol>\n				<li>Bullet List</li>\n				<li>\n					Second Item\n					<ol>\n						<li>Sub Item</li>\n					</ol>\n				</li>\n			</ol>\n		</td>\n	</tr>\n	<tr>\n		<td width="120"><strong>Images</strong></td>\n		<td>{{http://www.website.com/image.gif|My Image}}</td>\n		<td>(Image with alt. text)</td>\n	</tr>\n</table>\n</html>\n\n=== Advanced ===\n<html>\n<table width="100%" border="0" cellspacing="0" cellpadding="5">\n	<tr class="alternate">\n		<td width="120"><strong>Page Title</strong></td>\n		<td>@@page_title = My Title@@</td>\n		<td>(Changes the displayed page title)</td>\n	</tr>\n	<tr>\n		<td><strong>Horizontal Line</strong></td>\n		<td>----</td>\n		<td><hr /></td>\n	</tr>\n	<tr class="alternate">\n		<td><strong>Table</strong></td>\n		<td>\n			<pre>\n|=Heading Col 1 |=Heading Col 2         |\n|Cell 1.1       |Two lines\\\\in Cell 1.2 |\n|Cell 2.1       |Cell 2.2               |\n			</pre>\n		</td>\n		<td>\n			<table class="wiki_syntax_table">\n				<tr>\n					<th>Heading Col 1</th>\n					<th>Heading Col 2</th>\n				</tr>\n				<tr>\n					<td>Cell 1.1</td>\n					<td>Two lines<br />in Cell 1.2</td>\n				</tr>\n				<tr>\n					<td>Cell 2.1</td>\n					<td>Cell 2.2</td>\n				</tr>\n			</table>\n		</td>\n	</tr>\n	<tr>\n		<td><strong>No Formatting</strong></td>\n		<td>\n			{{{<br />\n			== [[Nowiki]]:<br />\n			//**don''t** format//<br />\n			}}}\n		</td>\n		<td>\n			<pre>\n== [[Nowiki]]:\n//**don''t** format//\n			</pre>\n		</td>\n	</tr>\n</table>\n</html>', 'suppleText', 'Initial Setup');

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
  `session_start` int(10) unsigned NOT NULL default '0',
  `session_last_activity` int(10) unsigned NOT NULL default '0',
  `session_ip_address` varchar(16) character set latin1 NOT NULL default '0',
  `session_user_agent` varchar(50) character set latin1 NOT NULL,
  `session_data` text character set latin1 NOT NULL,
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
