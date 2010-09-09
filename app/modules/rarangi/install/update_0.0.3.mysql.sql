
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



CREATE TABLE `%%PREFIX%%globals_authors` (
  `global_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `as_contributor` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`global_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
