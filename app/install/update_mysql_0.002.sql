ALTER TABLE `classes` ADD `is_experimental` TINYINT( 0 ) NOT NULL ,
ADD `is_deprecated` TINYINT( 0 ) NOT NULL ,
ADD `user_tags` TEXT NULL, ADD `deprecated` VARCHAR( 100 ) NULL ;
ALTER TABLE `files` ADD `is_experimental` TINYINT( 0 ) NOT NULL ,
ADD `is_deprecated` TINYINT( 0 ) NOT NULL ,
ADD `user_tags` TEXT NULL, ADD `deprecated` VARCHAR( 100 ) NULL ;
ALTER TABLE `class_methods` ADD `is_experimental` TINYINT( 0 ) NOT NULL ,
ADD `is_deprecated` TINYINT( 0 ) NOT NULL ,
ADD `user_tags` TEXT NULL, ADD `deprecated` VARCHAR( 100 ) NULL ;
ALTER TABLE `class_properties` ADD `is_experimental` TINYINT( 0 ) NOT NULL ,
ADD `is_deprecated` TINYINT( 0 ) NOT NULL ,
ADD `user_tags` TEXT NULL, ADD `deprecated` VARCHAR( 100 ) NULL ;
ALTER TABLE `functions` ADD `is_experimental` TINYINT( 0 ) NOT NULL ,
ADD `is_deprecated` TINYINT( 0 ) NOT NULL ,
ADD `user_tags` TEXT NULL, ADD `deprecated` VARCHAR( 100 ) NULL ;