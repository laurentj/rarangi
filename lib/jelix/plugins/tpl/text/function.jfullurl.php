<?php
/**
* @package    jelix
* @subpackage jtpl_plugin
* @author     Mickael Fradin aka kewix
* @contributor Laurent Jouanneau
* @copyright  2009 Mickael Fradin
* @link       http://www.jelix.org
* @licence    GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
 * function plugin :  write the full url (with domain name) corresponding to the given jelix action
 *
 * @param jTpl $tpl template engine
 * @param string $selector selector action
 * @param array $params parameters for the url
 * @param string domain name, false if you want to use the config domain name or the server name
 */
function jtpl_function_text_jfullurl($tpl, $selector, $params=array(), $domain=false) {
    global $gJConfig;

    if (!$domain) {
        $domain = $gJConfig->domainName;
    }

    // Add the http or https if not given
    if (!preg_match('/^http/', $domain)) {
        $domain = $GLOBALS['gJCoord']->request->getProtocol().$domain;
    }

    // echo the full Url
    echo $domain.jUrl::get($selector, $params, 0);
}
