<?php
/**
* @package     jelix-scripts
* @author      Laurent Jouanneau
* @contributor Nicolas Jeudy (patch ticket #99)
* @contributor Gwendal Jouannic (patch ticket #615)
* @contributor Loic Mathaud
* @copyright   2005-2007 Laurent Jouanneau
* @copyright   2007 Nicolas Jeudy, 2008 Gwendal Jouannic, 2008 Loic Mathaud
* @link        http://www.jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class createdaoCommand extends JelixScriptCommand {

    public  $name = 'createdao';
    public  $allowed_options=array('-profile'=>true, '-empty'=>false);
    public  $allowed_parameters=array('module'=>true,'name'=>true, 'table'=>false);

    public  $syntaxhelp = "[-profile name] [-empty] MODULE DAO [TABLE]";
    public  $help=array(
        'fr'=>"
    Crée un nouveau fichier de dao

    -profile (facultatif) : indique le profil à utiliser pour se connecter à
                           la base et récupérer les informations de la table
    -empty (facultatif) : ne se connecte pas à la base et génère un fichier
                          dao vide

    MODULE: nom du module concerné.
    DAO   : nom du dao à créer.
    TABLE : nom de la table principale sur laquelle s'appuie le dao
            (cette commande ne permet pas de générer un dao s'appuyant sur
             de multiples tables)
    Si la table n'est pas indiquée, le nom de la DAO devra être le nom de la table.",
        'en'=>"
    Create a new dao file.

    -profile (optional) : indicate the name of the profile to use for the
                        database connection.
    -empty (optional) : just create an empty dao file (it doesn't connect to
                        the database)

    MODULE : module name where to create the dao
    DAO    : dao name
    TABLE  : name of the main table on which the dao is mapped. You cannot indicate
             multiple tables
    If the TABLE is not provided, the DAO name will be used as table name.",
    );


    public function run(){

       jxs_init_jelix_env();

       $path= $this->getModulePath($this->_parameters['module']);

       $filename= $path.'daos/';
       $this->createDir($filename);

       $filename.=strtolower($this->_parameters['name']).'.dao.xml';

       $profile = $this->getOption('-profile');

       $param = array('name'=>($this->_parameters['name']),
              'table'=>$this->getParam('table'));
        if($param['table'] == null)
            $param['table'] = $param['name'];

       if($this->getOption('-empty')){
          $this->createFile($filename,'module/dao_empty.xml.tpl',$param);
       }else{

         $tools = jDb::getConnection($profile)->tools();
         $fields = $tools->getFieldList($param['table']);

         $properties='';
         $primarykeys='';
         foreach($fields as $fieldname=>$prop){

            $name = str_replace('-', '_', $fieldname);
            $properties.="\n        <property name=\"$name\" fieldname=\"$fieldname\"";
            $properties.=' datatype="'.$prop->type.'"';
            if($prop->primary) {
               if($primarykeys != '')
                  $primarykeys.=','.$fieldname;
               else
                  $primarykeys.=$fieldname;
            }
            if($prop->notNull && !$prop->autoIncrement)
               $properties.=' required="true"';
            
            if($prop->autoIncrement)
                $properties.=' autoincrement="true"';

            if($prop->hasDefault) {
                $properties.=' default="'.htmlspecialchars($prop->default).'"';
            }
            if ($prop->length) {
                 $properties.=' maxlength="'.$prop->length.'"';
            }
            if ($prop->sequence) {
                 $properties.=' sequence="'.$prop->sequence.'"';
            }
            $properties.='/>';

         }

         if($primarykeys == '') {
            throw new Exception("The table has no primary keys. A dao needs a primary key on the table to be defined.");
         }

         $param['properties']=$properties;
         $param['primarykeys']=$primarykeys;
         $this->createFile($filename,'module/dao.xml.tpl',$param);
       }
    }
}

