<?php
/**
* @package   jphpdoc
* @subpackage 
* @author    Laurent Jouanneau
* @copyright 2008 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/jphpdoc/
* @licence   GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

class sourcesCtrl extends jController {
    
    /**
    * display a file of sources
    */
    function index() {
        $rep = $this->getResponse('html');
        $tpl = new jTpl();
        
        $project = $GLOBALS['currentproject'];
        $path = $this->param('path');
        if($path) {
            $path = str_replace('..','',$path);
        }

        $rep->title = $path;

        
        $tpl->assign('filename', $path);
        $tpl->assign('project', $project->name);

        $filedao = jDao::get('files');
        $file = $filedao->getByFullPath($path, $project->id);
        if($file) {
            $tpl->assign('file',$file);
            
            if($file->isdir) {
                $tpl->assign('directory', $filedao->getDirectoryContent($path, $project->id));
                $tpl->assign('filecontent','');
            }
            else {
                $tpl->assign('directory','');
                $tpl->assign('filecontent', jDao::get('files_content')->findByFile($file->id));
            }
            $rep->body->assign('MAIN', $tpl->fetch('file_content'));
        }
        else {
            $rep->body->assign('MAIN', "<p>unknow file $path</p>");
        }


        //$rep->body->assignZone('SIDEBAR', 'sources_sidebar', array('path'=>'',) );
        $rep->body->assignZone('SUBMENUBAR', 'project_menubar');
        return $rep;
    }

}
