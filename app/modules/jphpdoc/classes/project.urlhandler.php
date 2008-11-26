<?php
/**
* @package     jphpdoc
* @author      Laurent Jouanneau
* @contributor
* @copyright   2008 Laurent Jouanneau
* @link        http://forge.jelix.org/
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
class projectUrlsHandler implements jIUrlSignificantHandler {
 
    function parse($url){
 
        if(preg_match('!^/([^/]+)(/(.*))?$!',$url->pathInfo,$match)){
            $urlact = new jUrlAction($url->params);

            $project = jDao::get('jphpdoc~projects')->getByName($match[1]);
            if(!$project)
                return false;
            $urlact->setParam('project',$match[1]);
            $GLOBALS['currentproject'] = $project;

            if($match[3] == '') {
                $urlact->setParam('action', 'default:project');
                return $urlact;
            }

            if(preg_match('!^sources(/(.*))?$!', $match[3], $m)) {
                $urlact->setParam('action', 'sources:index');
                $urlact->setParam('path', $m[2]);
                return $urlact;
            }

            if(preg_match('!^packages/?$!', $match[3], $m)) {
                $urlact->setParam('action', 'packages:index');
                return $urlact;
            }

            if(preg_match('!^packages/([^/]+)/?$!', $match[3], $m)) {
                $urlact->setParam('action', 'packages:details');
                $urlact->setParam('package', $m[1]);
                return $urlact;
            }

            if(preg_match('!^packages/([^/]+)/([^/]+)/?$!', $match[3], $m)) {
                $urlact->setParam('action', 'packages:subpackdetails');
                $urlact->setParam('package', $m[1]);
                $urlact->setParam('subpackage', $m[2]);
                return $urlact;
            }

            if(preg_match('!^packages/([^/]+)/([^/]+)/(classes|functions)/?$!', $match[3], $m)) {
                $urlact->setParam('action', 'packages:'.$m[3].'list');
                $urlact->setParam('package', $m[1]);
                $urlact->setParam('subpackage', $m[2]);
                return $urlact;
            }
        }
        return false;
    }
 
    function create($urlact, $url){
        $action = $url->getParam('action');
        $project = $url->getParam('project');
        $url->pathInfo = "/$project/";

        $url->delParam('action');
        $url->delParam('module');
        $url->delParam('project');

        switch($action) {
            case 'sources:index':
                $url->pathInfo .= 'sources/';
                $path = $url->getParam('path');
                if($path) {
                    $url->pathInfo .= $path;
                }
                $url->delParam('path');
                break;
            case 'packages:index':
                $url->pathInfo .= 'packages/';
                break;
            case 'packages:details':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/';
                $url->delParam('package');
                break;
            case 'packages:subpackdetails':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/'. $url->getParam('subpackage').'/';
                $url->delParam('package');
                $url->delParam('subpackage');
                break;
            case 'packages:classeslist':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/'. $url->getParam('subpackage').'/classes/';
                $url->delParam('package');
                $url->delParam('subpackage');
                break;
            case 'packages:functionslist':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/'. $url->getParam('subpackage').'/functions/';
                $url->delParam('package');
                $url->delParam('subpackage');
                break;
        }
    }
}