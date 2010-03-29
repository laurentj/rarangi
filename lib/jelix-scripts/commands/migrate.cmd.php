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
        require_once (JELIXS_LIB_PATH.'jelix/installer/jInstaller.class.php');
        $reporter = new textInstallReporter();
        $install = new jInstaller($reporter);
        $install->installApplication(true);
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
        
        $doc = $this->projectXml;
        
        $this->updateInfo($doc, '', '');

        $this->updateJelixDependency($doc);
        
        $dep = $this->nextElementSibling($this->firstElementChild($doc->documentElement));
        $dir = $this->nextElementSibling($dep, 'directories');
        
        $this->checkPath($dir, 'config', JELIX_APP_CONFIG_PATH);
        $this->checkPath($dir, 'log', JELIX_APP_LOG_PATH);
        $this->checkPath($dir, 'var', JELIX_APP_VAR_PATH);
        $this->checkPath($dir, 'www', JELIX_APP_WWW_PATH);
        $this->checkPath($dir, 'temp', JELIX_APP_REAL_TEMP_PATH);

        $this->projectXml->save(JELIX_APP_PATH.'project.xml');
    }
    
    protected function checkPath($dir, $localName, $path) {
        $config = $dir->getElementsByTagName($localName);
        if (!$config || $config->length == 0) {
            $config = $dir->ownerDocument->createElement($localName);
            $dir->appendChild($config);
        }
        else {
            $config = $config->item(0);
        }
        if (trim($config->textContent) == '') {
            $config->textContent = jxs_getRelativePath(JELIX_APP_PATH, $path, true);
        }
    }

    protected function updateInfo($doc, $id, $name) {
        $info = $this->firstElementChild($doc->documentElement, 'info');

        if ($info->getAttribute('id') == '')
            $info->setAttribute('id',$id);
        if ($info->getAttribute('name') == '')
            $info->setAttribute('name', $name);

        $version = $this->firstElementChild($info, 'version');

        if (!$version->hasAttribute('stability')) {
            $version->setAttribute('stability', 'stable');
        }
        if ($version->textContent == '')
            $version->textContent = '1.0';
        
        return $info;
    }
    
    
    protected function updateJelixDependency($doc) {
        
        $info = $this->firstElementChild($doc->documentElement);
        $dep = $this->nextElementSibling($info, 'dependencies');
        $jelix = $this->firstElementChild($dep, 'jelix');

        if (!$jelix->hasAttribute('minversion')) {
            $jelix->setAttribute('minversion', JELIX_VERSION);
        }

        if (!$jelix->hasAttribute('maxversion') || jVersionComparator::compareVersion($jelix->getAttribute('maxversion'), JELIX_VERSION) == -1) {
            $jelix->setAttribute('maxversion', jVersionComparator::getBranchVersion(JELIX_VERSION).'.*');
        }
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

        $this->updateInfo($doc, $module->name.JELIXS_INFO_DEFAULT_IDSUFFIX, $module->name);
        $this->updateJelixDependency($doc);
        $doc->save($modulexml);
    }
    
    
    protected function firstElementChild($elt, $name = '') {
        $child = $elt->firstChild;
        while ($child && $child->nodeType != 1)
            $child = $child->nextSibling;

        if ($name != '' && (!$child || $child->localName != $name)) {
            $doc = $elt->ownerDocument;
            $new = $doc->createElement($name);
            if ($child)
                $child = $doc->documentElement->insertBefore($new, $child);
            else
                $child = $doc->appendChild($new);
        }
        return $child;
    }
    
    protected function nextElementSibling($elt, $name = '') {
        $child = $elt->nextSibling;
        while ($child && $child->nodeType != 1)
            $child = $child->nextSibling;

        if ($name != '' && (!$child || $child->localName != $name)) {
            $doc = $elt->ownerDocument;
            $new = $doc->createElement($name);
            if ($child)
                $child = $doc->documentElement->insertBefore($new, $child);
            else
                $child = $doc->appendChild($new);
        }

        return $child;
    }
}