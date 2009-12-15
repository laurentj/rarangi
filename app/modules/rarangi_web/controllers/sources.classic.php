<?php
/**
* @package   rarangi
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class sourcesCtrl extends jController {
    
    /**
    * Display a file of sources
    */
    function index() {
        $resp = $this->getResponse('html');
        
        $project = $GLOBALS['currentproject'];
        $path = $this->param('path');
        if ($path) {
            $path = str_replace('..', '', $path);
        }

        $resp->title = $path;
        
        $resp->body->assignZone('BREADCRUMB', 'location_breadcrumb', array(
                    'mode' => 'projectbrowse',
                    'projectname' => $project->name));
        $resp->body->assignZone('MENUBAR', 'project_menubar');

        $tpl = new jTpl();
        $tpl->assign('filename', $path);
        $tpl->assign('project', $project->name);

        $filedao = jDao::get('rarangi~files');
        $file = $filedao->getByFullPath($path, $project->id);
        if ($file) {
            $tpl->assign('file', $file);
            
            if ($file->isdir) {
                $tpl->assign('directory', $filedao->getDirectoryContent($path, $project->id));
                $tpl->assign('filecontent','');
            } else {
                $tpl->assign('directory','');
                $tpl->assign('filecontent', jDao::get('rarangi~files_content')->findByFile($file->id));
            }
            $resp->body->assign('MAIN', $tpl->fetch('file_content'));
        }
        else {
            $resp->setHttpStatus('404', 'Not Found');
            $resp->body->assign('MAIN', "<p>unknow file $path</p>");
        }
        
        return $resp;
    }

}
