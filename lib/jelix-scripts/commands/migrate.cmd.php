<?php

/**
* @package     jelix-scripts
* @author      Laurent Jouanneau
* @contributor 
* @copyright   2010 Laurent Jouanneau
* @link        http://jelix.org
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class migrateModule {
    public $path;
    public $name;
    public $access;
}


class migrateCommand extends JelixScriptCommand {

    public  $name = 'migrate';
    public  $allowed_options = array('-v'=>false);
    public  $allowed_parameters = array();

    public  $applicationMustExist = true;

    public  $syntaxhelp = "[-v]";
    public  $help = '';

    function __construct(){
        $this->help= array(
            'fr'=>"
    migre une application jelix 1.1  vers jelix 1.2

    Option -v : mode verbeux.
    ",
            'en'=>"
    Migrate a jelix 1.1 application to jelix 1.2

    Option -v: verbose mode.
    ",
    );
    }

    public function run(){
        $this->loadProjectXml();

        // verify version
        $this->checkVersion();

        // update configuration file of entry points
        $this->updateConfig();

        // update project.xml
        $this->updateProjectXml();

        // lancement de jInstaller
        // TODO

    }

    
    protected function checkVersion() {
        list($minversion, $maxversion) = $this->getSupportedJelixVersion();
        
        if($minversion == '' || $maxversion == '')
            throw new Exception('Minimum and max jelix version of your project is not indicated in project.xml');
        
        if (jVersionComparator::compareVersion($maxversion, "1.2") > -1)
            throw new Exception("Because of maxversion in project.xml, it seems that your application is already compatible with jelix 1.2");
        
        if (file_exists(JELIX_APP_CONFIG_PATH.'installer.ini.php'))
            throw new Exception("installer.ini.php already exists !");
    }
    
    
    protected function updateConfig() {
        $configList = array();

        // retrieve the default config
        $defaultconfig = new jIniFileModifier(JELIX_APP_CONFIG_PATH.'defaultconfig.ini.php');

        $this->defaultModulesPath = $defaultconfig->getValue('modulesPath');
        if (!$this->defaultModulesPath) {
            $this->defaultModulesPath = 'lib:jelix-modules/,app:modules/';
        }

        $this->defaultCheckTrustedModules = $defaultconfig->getValue('checkTrustedModules');
        if ($this->defaultCheckTrustedModules === null)
            $this->defaultCheckTrustedModules = false;

        $this->defaultTrustedModules = $defaultconfig->getValue('trustedModules');
        if ($this->defaultTrustedModules === null)
            $this->defaultTrustedModules = '';

        $allModulePath = $this->getModulesPath($this->defaultModulesPath, ($this->defaultCheckTrustedModules?1:2));

        if ($this->defaultCheckTrustedModules) {
            $list = preg_split('/ *, */', $this->defaultTrustedModules);
            foreach ($list as $module) {
                if (isset($allModulePath[$module]))
                    $allModulePath[$module]->access = 2;
            }
        }

        $this->defaultUnusedModules = $defaultconfig->getValue('unusedModules');
        if ($this->defaultUnusedModules) {
            $list = preg_split('/ *, */', $this->defaultUnusedModules);
            foreach ($list as $module) {
                if (isset($allModulePath[$module]))
                    $allModulePath[$module]->access = 0;
            }
        }

        foreach ($allModulePath as $name=>$module) {
            $defaultconfig->setValue($name.'.access', $module->access, 'modules');
            $this->updateModuleXml($module);
        }

        $defaultconfig->removeValue('checkTrustedModules');
        $defaultconfig->removeValue('trustedModules');
        $defaultconfig->removeValue('unusedModules');
        $defaultconfig->save();

        $configList['defaultconfig.ini.php'] = $defaultconfig;

        // read each entry point configuration
        $eplist = $this->getEntryPointsList();

        foreach($eplist as $ep) {
            if (isset($configList[$ep['config']]))
                continue;

            $config = new jIniFileModifier(JELIX_APP_CONFIG_PATH.$ep['config']);
    
            $modulesPath = $config->getValue('modulesPath');
            if (!$modulesPath) {
                $modulesPath = $this->defaultModulesPath;
            }

            $checkTrustedModules = $config->getValue('checkTrustedModules');
            if ($checkTrustedModules === null)
                $checkTrustedModules = $this->defaultCheckTrustedModules;

            $trustedModules = $config->getValue('trustedModules');
            if (!$trustedModules)
                $trustedModules = $this->defaultTrustedModules;

            $unusedModules = $config->getValue('unusedModules');
            if (!$unusedModules)
                $unusedModules = $this->defaultUnusedModules;
    
            $epModulePath = $this->getModulesPath($modulesPath, ($checkTrustedModules?1:2));

            if ($checkTrustedModules) {
                $list = preg_split('/ *, */', $trustedModules);
                foreach ($list as $module) {
                    if (isset($allModulePath[$module]))
                        $epModulePath[$module]->access = 2;
                }
            }

            if ($unusedModules) {
                $list = preg_split('/ *, */', $unusedModules);
                foreach ($list as $module) {
                    if (isset($allModulePath[$module]))
                        $epModulePath[$module]->access = 0;
                }
            }
            
            foreach ($epModulePath as $name=>$module) {
                if (!isset($allModulePath[$name]) || $allModulePath[$name]->access != $module->access) {
                    $config->setValue($name.'.access', $module->access, 'modules');
                }
                if (!isset($allModulePath[$name]))
                    $this->updateModuleXml($module);
            }

            $config->removeValue('checkTrustedModules');
            $config->removeValue('trustedModules');
            $config->removeValue('unusedModules');
            $config->save();

            $configList[$ep['config']] = $config;
        }
    }
    
    protected $moduleRepositories = array();
    
    protected function getModulesPath($modulesPath, $defaultAccess) {

        $list = preg_split('/ *, */', $modulesPath);
        array_unshift($list, JELIX_LIB_PATH.'core-modules/');
        $modulesPathList = array();
        
        foreach ($list as $k=>$path) {
            if (trim($path) == '') continue;
            $p = str_replace(array('lib:','app:'), array(LIB_PATH, JELIX_APP_PATH), $path);
            if (!file_exists($p)) {
                throw new Exception('The path, '.$path.' given in the jelix config, doesn\'t exists !',E_USER_ERROR);
            }

            if (substr($p,-1) !='/')
                $p.='/';

            $this->moduleRepositories[$p] = array();

            if ($handle = opendir($p)) {
                while (false !== ($f = readdir($handle))) {
                    if ($f[0] != '.' && is_dir($p.$f)) {
                        $m = new migrateModule();
                        $m->path = $p.$f.'/';
                        $m->name = $f;
                        $m->access = $defaultAccess;
                        $m->repository = $p;
                        $modulesPathList[$f] = $m;
                        $this->moduleRepositories[$p][$f] = $m;
                    }
                }
                closedir($handle);
            }
        }
        return $modulesPathList;
    }
 
    protected function updateProjectXml() {
        
        $this->updateJelixDependency($this->projectXml);
        $this->projectXml->save();
    }
    
    
    protected function updateJelixDependency($doc) {
        $deps = $doc->getElementsByTagName('dependencies');
        if (!$deps || $deps->length ==0) {
            $dep = $doc->createElement('dependencies');
            $doc->documentElement->appendChild($dep);
        }
        else
            $dep = $deps->item(0);

        $jelix = $dep->getElementsByTagName('jelix');
        
        if (!$jelix || $jelix->length == 0) {
            $jelix = $doc->createElement('jelix');
            $dep->appendChild($jelix);
        }
        else
            $jelix = $jelix->item(0);

        $jelix->setAttribute('minversion', JELIX_VERSION);
        $jelix->setAttribute('maxversion', jVersionComparator::getBranchVersion(JELIX_VERSION).'.*');
    }
    
    protected function updateModuleXml(migrateModule $module) {
        
        $modulexml = $module->path.'module.xml';
        if (!file_exists($modulexml)) {
            $param = array();
            $param['module'] = $module->name;
            $param['default_id'] = $module->name.JELIXS_INFO_DEFAULT_IDSUFFIX;
            $param['version'] = '1.0';
            $this->createFile($modulexml, 'module/module.xml.tpl', $param);
            return;
        }
        
        $doc = new DOMDocument();

        if (!$doc->load($modulexml)){
           throw new Exception("cannot load $modulexml");
        }

        if ($doc->documentElement->namespaceURI != JELIX_NAMESPACE_BASE.'project/1.0'){
            throw new Exception("bad namespace in project.xml");
        }
        
        $infos = $doc->getElementsByTagName('info');
        if (!$infos || $infos->length ==0) {
            $info = $doc->createElement('info');
            $doc->documentElement->appendChild($info);
        }
        else {
            $info = $infos->item(0);
            if ($info->getAttribute('id') == '')
                $info->setAttribute('id',$module->name.JELIXS_INFO_DEFAULT_IDSUFFIX);
            if ($info->getAttribute('module') == '')
                $info->setAttribute('module', $module->name);
        }
        
        $versions = $info->getElementsByTagName('version');
        if (!$versions || $versions->length == 0) {
            $version = $doc->createElement('version');
            $version->setAttribute('stability', 'stable');
            $version->textContent = '1.0';
        }
        else {
            $version = $versions->item(0);
            if (!$version->hasAttribute('stability')) {
                $version->setAttribute('stability', 'stable');
            }
            if ($version->textContent == '')
                $version->textContent = '1.0';
        }

        $this->updateJelixDependency($doc);
        $doc->save();
    }
}