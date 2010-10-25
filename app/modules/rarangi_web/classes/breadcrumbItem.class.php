<?php
/**
* @package     rarangi
* @author      Laurent Jouanneau
* @contributor
* @copyright   2010 Laurent Jouanneau
* @link        http://forge.jelix.org/projects/rarangi/
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/


class breadcrumbItem {
    public $name;
    public $url;
    public $children = array();
    function __construct($name, $url) {
        $this->name = $name;
        $this->url = $url;
    }
}