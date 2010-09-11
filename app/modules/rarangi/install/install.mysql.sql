
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `%%PREFIX%%authors` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%classes` (
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

CREATE TABLE `%%PREFIX%%classes_authors` (
  `class_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`class_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%class_methods` (
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


CREATE TABLE `%%PREFIX%%method_parameters` (
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

CREATE TABLE `%%PREFIX%%class_properties` (
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

CREATE TABLE `%%PREFIX%%files` (
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

CREATE TABLE `%%PREFIX%%files_authors` (
  `file_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`file_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%files_content` (
  `file_id` int(11) NOT NULL,
  `linenumber` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `content` tinytext NOT NULL,
  PRIMARY KEY  (`file_id`,`linenumber`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%functions` (
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

CREATE TABLE `%%PREFIX%%functions_authors` (
  `function_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`function_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `%%PREFIX%%function_parameters` (
`function_id` INT NOT NULL ,
`arg_number` MEDIUMINT NOT NULL ,
`type` VARCHAR( 255 ) NULL ,
`name` VARCHAR( 150 ) NOT NULL ,
`defaultvalue` VARCHAR( 255 ) NULL ,
`documentation` TEXT  NULL,
PRIMARY KEY ( `function_id`, `arg_number` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%interface_class` (
  `class_id` int(11) NOT NULL,
  `interface_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY  (`class_id`,`interface_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%methods_authors` (
  `name` varchar(150) NOT NULL,
  `class_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`name`,`class_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%globals` (
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

CREATE TABLE `%%PREFIX%%globals_authors` (
  `global_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`global_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%packages` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

CREATE TABLE `%%PREFIX%%errors` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`type` VARCHAR( 20 )  NOT NULL ,
`message` TEXT NOT NULL ,
`file` VARCHAR( 255 ) NULL ,
`line` INT NULL ,
`project_id` INT NOT NULL ,
INDEX ( `project_id` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;

