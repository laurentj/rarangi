
-- Liste des groupes
CREATE TABLE  IF NOT EXISTS `jacl2_group` (
  `id_aclgrp` int(11) NOT NULL auto_increment,
  `name` varchar(150) NOT NULL default '',
  `code` varchar(30) default NULL,
  `grouptype` tinyint(4) NOT NULL default '0',
  `ownerlogin` varchar(50) default NULL,
  PRIMARY KEY  (`id_aclgrp`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- liste des groupes associés à chaque utilisateur
CREATE TABLE IF NOT EXISTS `jacl2_user_group` (
  `login` varchar(50) NOT NULL default '',
  `id_aclgrp` int(11) NOT NULL default '0',
  KEY `login` (`login`,`id_aclgrp`)
) TYPE=MyISAM;


-- liste des sujets, avec leur appartenance à un groupe de valeurs de droits
CREATE TABLE IF NOT EXISTS `jacl2_subject` (
  `id_aclsbj` varchar(100) NOT NULL default '',
  `label_key` varchar(100) default NULL,
  PRIMARY KEY  (`id_aclsbj`)
) TYPE=MyISAM;

-- table centrale
-- valeurs du droit pour chaque couple sujet/groupe ou triplet sujet/groupe/ressource
CREATE TABLE IF NOT EXISTS `jacl2_rights` (
  `id_aclsbj` varchar(100) NOT NULL default '',
  `id_aclgrp` int(11) NOT NULL default '0',
  `id_aclres` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id_aclsbj`,`id_aclgrp`,`id_aclres`)
) TYPE=MyISAM;