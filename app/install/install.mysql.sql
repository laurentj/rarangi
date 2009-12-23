-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lun 19 Janvier 2009 à 00:13
-- Version du serveur: 5.0.45
-- Version de PHP: 5.2.3-1ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Base de données: `rarangi_tests`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `authors`
-- 

DROP TABLE IF EXISTS `authors`;
CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `classes`
-- 

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  `file_id` int(11) default NULL,
  `package_id` int(11) default NULL,
  `line_start` int(11) NOT NULL,
  `line_end` int(11) NOT NULL default '0',
  `mother_class` int(11) default NULL,
  `is_abstract` tinyint(1) NOT NULL,
  `is_interface` tinyint(1) NOT NULL,
  `is_experimental` tinyint(1) NOT NULL default '0',
  `is_deprecated` tinyint(1) NOT NULL default '0',
  `deprecated` varchar(100) default NULL,
  `short_description` tinytext,
  `description` text,
  `copyright` tinytext,
  `internal` text,
  `links` tinytext,
  `see` tinytext,
  `uses` tinytext,
  `since` varchar(100) default NULL,
  `changelog` text,
  `todo` text,
  `license_link` varchar(100) default NULL,
  `license_label` varchar(150) default NULL,
  `license_text` text,
  `user_tags` TEXT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`,`project_id`,`file_id`),
  KEY `package_id` (`package_id`),
  KEY `mother_class` (`mother_class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `classes_authors`
-- 

DROP TABLE IF EXISTS `classes_authors`;
CREATE TABLE IF NOT EXISTS `classes_authors` (
  `class_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`class_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `class_methods`
-- 

DROP TABLE IF EXISTS `class_methods`;
CREATE TABLE IF NOT EXISTS `class_methods` (
  `name` varchar(150) NOT NULL,
  `class_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `line_start` int(11) NOT NULL,
  `line_end` int(11) NOT NULL default '0',
  `is_static` tinyint(1) NOT NULL,
  `is_final` tinyint(1) NOT NULL default '0',
  `is_abstract` tinyint(1) NOT NULL default '0',
  `is_experimental` tinyint(1) NOT NULL default '0',
  `is_deprecated` tinyint(1) NOT NULL default '0',
  `deprecated` varchar(100) default NULL,
  `accessibility` char(3) NOT NULL,
  `short_description` tinytext,
  `description` text,
  `return_datatype` varchar(150) NOT NULL,
  `return_description` mediumtext NOT NULL,
  `copyright` tinytext,
  `internal` text,
  `links` tinytext,
  `see` tinytext,
  `uses` tinytext,
  `since` varchar(100) default NULL,
  `changelog` text,
  `todo` text,
  `license_link` varchar(100) default NULL,
  `license_label` varchar(150) default NULL,
  `license_text` text,
  `user_tags` TEXT NULL,
  PRIMARY KEY  (`name`,`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `method_parameters`;
CREATE TABLE `method_parameters` (
`class_id` INT NOT NULL ,
`method_name` VARCHAR( 150 ) NOT NULL ,
`arg_number` MEDIUMINT NOT NULL ,
`type` VARCHAR( 255 ) NULL ,
`name` VARCHAR( 150 ) NOT NULL ,
`defaultvalue` VARCHAR( 255 ) NULL ,
`documentation` TEXT  NULL,
PRIMARY KEY ( `class_id` , `method_name` , `arg_number` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

-- 
-- Structure de la table `class_properties`
-- 

DROP TABLE IF EXISTS `class_properties`;
CREATE TABLE IF NOT EXISTS `class_properties` (
  `name` varchar(150) NOT NULL,
  `class_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `line_start` int(11) NOT NULL,
  `datatype` varchar(150) NOT NULL,
  `default_value` tinytext,
  `type` tinyint(1) NOT NULL,
  `accessibility` char(3) NOT NULL,
  `is_experimental` tinyint(1) NOT NULL default '0',
  `is_deprecated` tinyint(1) NOT NULL default '0',
  `deprecated` varchar(100) default NULL,
  `short_description` tinytext,
  `description` text,
  `copyright` tinytext,
  `internal` text,
  `links` tinytext,
  `see` tinytext,
  `uses` tinytext,
  `since` varchar(100) default NULL,
  `changelog` text,
  `todo` text,
  `user_tags` TEXT NULL,
  `license_link` varchar(100) default NULL,
  `license_label` varchar(150) default NULL,
  `license_text` text,
  PRIMARY KEY  (`name`,`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `files`
-- 

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL auto_increment,
  `package_id` int(11) default NULL,
  `project_id` int(11) NOT NULL,
  `fullpath` varchar(255) NOT NULL,
  `isdir` tinyint(4) NOT NULL default '0',
  `dirname` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `copyright` tinytext,
  `short_description` tinytext,
  `description` text,
  `internal` text,
  `is_experimental` tinyint(1) NOT NULL default '0',
  `is_deprecated` tinyint(1) NOT NULL default '0',
  `deprecated` varchar(100) default NULL,
  `links` tinytext,
  `see` tinytext,
  `uses` tinytext,
  `since` varchar(100) default NULL,
  `changelog` text,
  `todo` text,
  `license_link` varchar(100) default NULL,
  `license_label` varchar(150) default NULL,
  `license_text` text,
  `user_tags` TEXT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `dirname` (`dirname`),
  KEY `fullpath` (`fullpath`),
  KEY `filename` (`filename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `files_authors`
-- 

DROP TABLE IF EXISTS `files_authors`;
CREATE TABLE IF NOT EXISTS `files_authors` (
  `file_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`file_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `files_content`
-- 

DROP TABLE IF EXISTS `files_content`;
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
-- Structure de la table `functions`
-- 

DROP TABLE IF EXISTS `functions`;
CREATE TABLE IF NOT EXISTS `functions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(150) NOT NULL,
  `project_id` int(11) NOT NULL,
  `package_id` int(11) default NULL,
  `file_id` INT NOT NULL,
  `line_start` int(11) NOT NULL,
  `line_end` int(11) NOT NULL default '0',
  `short_description` tinytext,
  `description` text,
  `return_datatype` varchar(150) NOT NULL,
  `return_description` mediumtext NOT NULL,
  `is_experimental` tinyint(1) NOT NULL default '0',
  `is_deprecated` tinyint(1) NOT NULL default '0',
  `deprecated` varchar(100) default NULL,
  `copyright` tinytext,
  `internal` text,
  `links` tinytext,
  `see` tinytext,
  `uses` tinytext,
  `since` varchar(100) default NULL,
  `changelog` text,
  `todo` text,
  `license_link` varchar(100) default NULL,
  `license_label` varchar(150) default NULL,
  `license_text` text,
  `user_tags` TEXT NULL,
  PRIMARY KEY  (`id`),
  KEY `package_id` (`package_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `functions_authors`
-- 

DROP TABLE IF EXISTS `functions_authors`;
CREATE TABLE IF NOT EXISTS `functions_authors` (
  `function_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`function_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `function_parameters`;
CREATE TABLE `function_parameters` (
`function_id` INT NOT NULL ,
`arg_number` MEDIUMINT NOT NULL ,
`type` VARCHAR( 255 ) NULL ,
`name` VARCHAR( 150 ) NOT NULL ,
`defaultvalue` VARCHAR( 255 ) NULL ,
`documentation` TEXT  NULL,
PRIMARY KEY ( `function_id`, `arg_number` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `interface_class`
-- 

DROP TABLE IF EXISTS `interface_class`;
CREATE TABLE IF NOT EXISTS `interface_class` (
  `class_id` int(11) NOT NULL,
  `interface_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY  (`class_id`,`interface_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `methods_authors`
-- 

DROP TABLE IF EXISTS `methods_authors`;
CREATE TABLE IF NOT EXISTS `methods_authors` (
  `name` varchar(150) NOT NULL,
  `class_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`name`,`class_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `globals`
-- 

DROP TABLE IF EXISTS `globals`;
CREATE TABLE IF NOT EXISTS `globals` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(150) NOT NULL,
  `project_id` int(11) NOT NULL,
  `package_id` int(11) default NULL,
  `file_id` INT NOT NULL,
  `line_start` int(11) NOT NULL,
  `datatype` varchar(150) NOT NULL,
  `default_value` tinytext,
  `type` tinyint(1) NOT NULL,
  `is_experimental` tinyint(1) NOT NULL default '0',
  `is_deprecated` tinyint(1) NOT NULL default '0',
  `deprecated` varchar(100) default NULL,
  `short_description` tinytext,
  `description` text,
  `copyright` tinytext,
  `internal` text,
  `links` tinytext,
  `see` tinytext,
  `uses` tinytext,
  `since` varchar(100) default NULL,
  `changelog` text,
  `todo` text,
  `user_tags` TEXT NULL,
  `license_link` varchar(100) default NULL,
  `license_label` varchar(150) default NULL,
  `license_text` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `globals_authors`
-- 

DROP TABLE IF EXISTS `globals_authors`;
CREATE TABLE IF NOT EXISTS `globals_authors` (
  `global_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`global_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `packages`
-- 

DROP TABLE IF EXISTS `packages`;
CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `projects`
-- 

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
