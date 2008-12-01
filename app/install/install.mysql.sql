-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 02, 2008 at 12:57 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `phpdoctests`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  `file_id` int(11) default NULL,
  `package_id` int(11) default NULL,
  `subpackage_id` int(11) default NULL,
  `linenumber` int(11) NOT NULL,
  `mother_class` int(11) default NULL,
  `is_abstract` tinyint(1) NOT NULL,
  `is_interface` tinyint(1) NOT NULL,
  `short_description` tinytext,
  `description` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`,`project_id`),
  KEY `file_id` (`file_id`),
  KEY `package_id` (`package_id`),
  KEY `mother_class` (`mother_class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `class_properties`
--

CREATE TABLE IF NOT EXISTS `class_properties` (
  `name` varchar(150) NOT NULL,
  `class_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `line_number` int(11) NOT NULL,
  `datatype` varchar(150) NOT NULL,
  `default_value` tinytext,
  `is_static` tinyint(1) NOT NULL,
  `accessibility` char(3) NOT NULL,
  `short_description` tinytext,
  `description` text,
  PRIMARY KEY  (`name`,`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL auto_increment,
  `package_id` int(11) default NULL,
  `subpackage_id` int(11) default NULL,
  `project_id` int(11) NOT NULL,
  `fullpath` varchar(255) NOT NULL,
  `isdir` tinyint(4) NOT NULL default '0',
  `dirname` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `dirname` (`dirname`),
  KEY `fullpath` (`fullpath`),
  KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `files_content`
--

CREATE TABLE IF NOT EXISTS `files_content` (
  `file_id` int(11) NOT NULL,
  `linenumber` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `content` tinytext NOT NULL,
  PRIMARY KEY  (`file_id`,`linenumber`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `interface_class`
--

CREATE TABLE IF NOT EXISTS `interface_class` (
  `class_id` int(11) NOT NULL,
  `interface_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY  (`class_id`,`interface_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_sub` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
