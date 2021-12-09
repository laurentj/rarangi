
CREATE TABLE %%PREFIX%%authors (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  project_id INTEGER NOT NULL,
  name varchar(150) NOT NULL,
  email varchar(150) NOT NULL
) ;
CREATE INDEX IF NOT EXISTS %%PREFIX%%authors_project_id_idx ON %%PREFIX%%authors( project_id );
-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%classes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(255) NOT NULL,
  project_id INTEGER NOT NULL,
  file_id INTEGER default NULL,
  package_id INTEGER default NULL,
  line_start INTEGER NOT NULL,
  line_end INTEGER NOT NULL default '0',
  mother_class INTEGER default NULL,
  is_abstract BOOLEAN NOT NULL,
  is_interface BOOLEAN NOT NULL,
  is_experimental BOOLEAN NOT NULL default '0',
  is_deprecated BOOLEAN NOT NULL default '0',
  deprecated varchar(100) default NULL,
  short_description TEXT,
  description text,
  copyright TEXT,
  internal text,
  links TEXT,
  see TEXT,
  uses TEXT,
  since varchar(100) default NULL,
  changelog text,
  todo text,
  license_link varchar(100) default NULL,
  license_label varchar(150) default NULL,
  license_text text,
  user_tags TEXT NULL,
  UNIQUE (name,project_id,file_id)
) ;
CREATE INDEX IF NOT EXISTS %%PREFIX%%classes_package_id_idx ON %%PREFIX%%classes( package_id );
CREATE INDEX IF NOT EXISTS %%PREFIX%%classes_mother_class_idx ON %%PREFIX%%classes( mother_class );

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%classes_authors (
  class_id INTEGER NOT NULL,
  author_id INTEGER NOT NULL,
  as_contributor BOOLEAN NOT NULL default '0',
  PRIMARY KEY  (class_id,author_id)
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%class_methods (
  name varchar(150) NOT NULL,
  class_id INTEGER NOT NULL,
  project_id INTEGER NOT NULL,
  line_start INTEGER NOT NULL,
  line_end INTEGER NOT NULL default '0',
  is_static BOOLEAN NOT NULL,
  is_final BOOLEAN NOT NULL default '0',
  is_abstract BOOLEAN NOT NULL default '0',
  is_experimental BOOLEAN NOT NULL default '0',
  is_deprecated BOOLEAN NOT NULL default '0',
  deprecated varchar(100) default NULL,
  accessibility char(3) NOT NULL,
  short_description TEXT,
  description text,
  return_datatype varchar(150) NOT NULL,
  return_description mediumtext NOT NULL,
  copyright TEXT,
  internal text,
  links TEXT,
  see TEXT,
  uses TEXT,
  since varchar(100) default NULL,
  changelog text,
  todo text,
  license_link varchar(100) default NULL,
  license_label varchar(150) default NULL,
  license_text text,
  user_tags TEXT NULL,
  PRIMARY KEY  (name,class_id)
) ;


CREATE TABLE %%PREFIX%%method_parameters (
class_id INT NOT NULL ,
method_name VARCHAR( 150 ) NOT NULL ,
arg_number MEDIUMINT NOT NULL ,
type VARCHAR( 255 ) NULL ,
name VARCHAR( 150 ) NOT NULL ,
defaultvalue VARCHAR( 255 ) NULL ,
documentation TEXT  NULL,
PRIMARY KEY ( class_id , method_name , arg_number )
);



-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%class_properties (
  name varchar(150) NOT NULL,
  class_id INTEGER NOT NULL,
  project_id INTEGER NOT NULL,
  line_start INTEGER NOT NULL,
  datatype varchar(150) NOT NULL,
  default_value TEXT,
  type BOOLEAN NOT NULL,
  accessibility char(3) NOT NULL,
  is_experimental BOOLEAN NOT NULL default '0',
  is_deprecated BOOLEAN NOT NULL default '0',
  deprecated varchar(100) default NULL,
  short_description TEXT,
  description text,
  copyright TEXT,
  internal text,
  links TEXT,
  see TEXT,
  uses TEXT,
  since varchar(100) default NULL,
  changelog text,
  todo text,
  user_tags TEXT NULL,
  license_link varchar(100) default NULL,
  license_label varchar(150) default NULL,
  license_text text,
  PRIMARY KEY  (name,class_id)
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%files (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  package_id INTEGER default NULL,
  project_id INTEGER NOT NULL,
  fullpath varchar(255) NOT NULL,
  isdir tinyint(4) NOT NULL default '0',
  dirname varchar(255) NOT NULL,
  filename varchar(255) NOT NULL,
  copyright TEXT,
  short_description TEXT,
  description text,
  internal text,
  is_experimental BOOLEAN NOT NULL default '0',
  is_deprecated BOOLEAN NOT NULL default '0',
  deprecated varchar(100) default NULL,
  links TEXT,
  see TEXT,
  uses TEXT,
  since varchar(100) default NULL,
  changelog text,
  todo text,
  license_link varchar(100) default NULL,
  license_label varchar(150) default NULL,
  license_text text,
  user_tags TEXT NULL
) ;

CREATE INDEX IF NOT EXISTS %%PREFIX%%files_project_id_idx ON %%PREFIX%%files( project_id );
CREATE INDEX IF NOT EXISTS %%PREFIX%%files_dirname_idx ON %%PREFIX%%files( dirname );
CREATE INDEX IF NOT EXISTS %%PREFIX%%files_fullpath_idx ON %%PREFIX%%files( fullpath );
CREATE INDEX IF NOT EXISTS %%PREFIX%%files_filename_idx ON %%PREFIX%%files( filename );

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%files_authors (
  file_id INTEGER NOT NULL,
  author_id INTEGER NOT NULL,
  as_contributor BOOLEAN NOT NULL default '0',
  PRIMARY KEY  (file_id,author_id)
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%files_content (
  file_id INTEGER NOT NULL,
  linenumber INTEGER NOT NULL,
  project_id INTEGER NOT NULL,
  content TEXT NOT NULL,
  PRIMARY KEY  (file_id,linenumber)
) ;
CREATE INDEX IF NOT EXISTS %%PREFIX%%files_content_project_id_idx ON %%PREFIX%%files_content( project_id );

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%functions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(150) NOT NULL,
  project_id INTEGER NOT NULL,
  package_id INTEGER default NULL,
  file_id INT NOT NULL,
  line_start INTEGER NOT NULL,
  line_end INTEGER NOT NULL default '0',
  short_description TEXT,
  description text,
  return_datatype varchar(150) NOT NULL,
  return_description mediumtext NOT NULL,
  is_experimental BOOLEAN NOT NULL default '0',
  is_deprecated BOOLEAN NOT NULL default '0',
  deprecated varchar(100) default NULL,
  copyright TEXT,
  internal text,
  links TEXT,
  see TEXT,
  uses TEXT,
  since varchar(100) default NULL,
  changelog text,
  todo text,
  license_link varchar(100) default NULL,
  license_label varchar(150) default NULL,
  license_text text,
  user_tags TEXT NULL
) ;
CREATE INDEX IF NOT EXISTS %%PREFIX%%functions_package_id_idx ON %%PREFIX%%functions( package_id );

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%functions_authors (
  function_id INTEGER NOT NULL,
  author_id INTEGER NOT NULL,
  as_contributor BOOLEAN NOT NULL default '0',
  PRIMARY KEY  (function_id,author_id)
) ;


CREATE TABLE %%PREFIX%%function_parameters (
function_id INT NOT NULL ,
arg_number MEDIUMINT NOT NULL ,
type VARCHAR( 255 ) NULL ,
name VARCHAR( 150 ) NOT NULL ,
defaultvalue VARCHAR( 255 ) NULL ,
documentation TEXT  NULL,
PRIMARY KEY ( function_id, arg_number )
);

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%interface_class (
  class_id INTEGER NOT NULL,
  interface_id INTEGER NOT NULL,
  project_id INTEGER NOT NULL,
  PRIMARY KEY  (class_id,interface_id)
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%methods_authors (
  name varchar(150) NOT NULL,
  class_id INTEGER NOT NULL,
  author_id INTEGER NOT NULL,
  as_contributor BOOLEAN NOT NULL default '0',
  PRIMARY KEY  (name,class_id,author_id)
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%globals (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(150) NOT NULL,
  project_id INTEGER NOT NULL,
  package_id INTEGER default NULL,
  file_id INT NOT NULL,
  line_start INTEGER NOT NULL,
  datatype varchar(150) NOT NULL,
  default_value TEXT,
  type BOOLEAN NOT NULL,
  is_experimental BOOLEAN NOT NULL default '0',
  is_deprecated BOOLEAN NOT NULL default '0',
  deprecated varchar(100) default NULL,
  short_description TEXT,
  description text,
  copyright TEXT,
  internal text,
  links TEXT,
  see TEXT,
  uses TEXT,
  since varchar(100) default NULL,
  changelog text,
  todo text,
  user_tags TEXT NULL,
  license_link varchar(100) default NULL,
  license_label varchar(150) default NULL,
  license_text text
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%globals_authors (
  global_id INTEGER NOT NULL,
  author_id INTEGER NOT NULL,
  as_contributor BOOLEAN NOT NULL default '0',
  PRIMARY KEY  (global_id,author_id)
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%packages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  project_id INTEGER NOT NULL,
  name varchar(50) NOT NULL,
  UNIQUE (project_id)
) ;

-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%projects (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(50) NOT NULL
) ;


-- --------------------------------------------------------

CREATE TABLE %%PREFIX%%errors (
id INTEGER PRIMARY KEY AUTOINCREMENT ,
type VARCHAR( 20 )  NOT NULL ,
message TEXT NOT NULL ,
file VARCHAR( 255 ) NULL ,
line INT NULL ,
project_id INT NOT NULL
);

CREATE INDEX IF NOT EXISTS %%PREFIX%%errors_project_id_idx ON %%PREFIX%%errors( project_id );

