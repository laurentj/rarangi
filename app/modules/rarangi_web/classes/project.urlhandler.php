<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2008 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi/
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/
class projectUrlsHandler implements jIUrlSignificantHandler {
 
    function parse($url){
 
        if(preg_match('!^/([^/]+)(/(.*))?$!',$url->pathInfo,$match)){
            $urlact = new jUrlAction($url->params);

            $project = jDao::get('rarangi~projects')->getByName($match[1]);
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

            if(preg_match('!^packages/([^/]+)/(classes|interfaces|functions)/?$!', $match[3], $m)) {
                $urlact->setParam('action', 'packages:'.$m[2]);
                $urlact->setParam('package', $m[1]);
                return $urlact;
            }
            if(preg_match('!^packages/([^/]+)/(classes|interfaces|functions)/(.+)$!', $match[3], $m)) {
                if ($m[2] == 'classes') {
                    $urlact->setParam('action', 'components:classdetails');
                    $urlact->setParam('classname', $m[3]);
                }
                elseif ($m[2] == 'interfaces') {
                    $urlact->setParam('action', 'components:interfacedetails');
                    $urlact->setParam('interfacename', $m[3]);
                }
                else {
                    $urlact->setParam('action', 'components:functiondetails');
                    $urlact->setParam('functionname', $m[3]);
                }
                $urlact->setParam('package', $m[1]);
                return $urlact;
            }
        }
        return false;
    }
 
    function create($urlact, $url){
        $action = $urlact->getParam('action');
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
            case 'packages:classes':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/classes/';
                $url->delParam('package');
                break;
            case 'packages:interfaces':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/interfaces/';
                $url->delParam('package');
                break;
            case 'packages:functions':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/functions/';
                $url->delParam('package');
                break;
            case 'components:classdetails':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/classes/'. $url->getParam('classname');
                $url->delParam('package');
                $url->delParam('classname');
                break;
            case 'components:interfacedetails':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/interfaces/'. $url->getParam('interfacename');
                $url->delParam('package');
                $url->delParam('interfacename');
                break;
            case 'components:functiondetails':
                $url->pathInfo .= 'packages/' . $url->getParam('package').'/functions/'. $url->getParam('functionname');
                $url->delParam('package');
                $url->delParam('functionname');
                break;
        }
    }
}