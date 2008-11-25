-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 25, 2008 at 11:22 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `phpdoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `name` varchar(100) NOT NULL,
  `project_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `linenumber` int(11) NOT NULL,
  `mother_class` varchar(100) default NULL,
  PRIMARY KEY  (`name`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `files_contents`
--

CREATE TABLE IF NOT EXISTS `files_content` (
  `file_id` int(11) NOT NULL,
  `linenumber` int(11) NOT NULL,
  `content` tinytext NOT NULL,
  PRIMARY KEY  (`file_id`,`linenumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
