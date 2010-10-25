<?php
/**
* @package   app
* @subpackage rarangi
* @author    Loic Mathaud <loic@mathaud.net>
* @contributor Laurent Jouanneau
* @copyright 2008 Loic Mathaud, 2010 Laurent Jouanneau
* @link      http://forge.jelix.org/projects/rarangi/
* @licence    GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/



/**
 * parameters :
 *      project : the project
 *      part : sources|errors|packages
 *      part = sources
 *         path :  the path of the directory/file to show
 *      part = errors
 *      part = packages
 *         package : the package name
 *         component_type : the component type
 *         component : the component name
 */
 class location_breadcrumbZone extends jZone {
    protected $_tplname='location_breadcrumb';

    protected function _prepareTpl() {
        $this->_tpl->assign('projectslist', jDao::get('rarangi~projects')->findAll());
        $this->_tpl->assignIfNone('project', '');
        $this->_tpl->assignIfNone('part', '');
        $this->_tpl->assignIfNone('items', array());
        $project = $this->param('project');
        if (!$project) {
            return;
        }

        $this->_tpl->assign('prj', $GLOBALS['currentproject']);
    }
}
