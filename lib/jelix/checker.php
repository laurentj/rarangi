<?php

/**
* check a jelix installation
*
* @package     jelix
* @subpackage  core
* @author      Jouanneau Laurent
* @copyright   2007-2009 Jouanneau laurent
* @link        http://www.jelix.org
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
* @since       1.0b2
*/

/**
 *
 */
include dirname(__FILE__).'/installer/jIInstallReporter.iface.php';
include dirname(__FILE__).'/installer/jInstallerMessageProvider.class.php';
include dirname(__FILE__).'/installer/jInstallChecker.class.php';
/**
 * an HTML reporter for jInstallChecker
 * @package jelix
 */
class jHtmlInstallChecker implements jIInstallReporter {

    function start(){
        echo '<ul class="checkresults">';
    }

    function message($message, $type=''){
        echo '<li class="check'.$type.'">'.htmlspecialchars($message).'</li>';
    }
    
    function end($results){
        echo '</ul>';
        
        $nbError = $results['error'];
        $nbWarning = $results['warning'];
        $nbNotice = $results['notice'];

        echo '<div class="results">';
        if ($nbError) {
            echo ' '.$nbError. $this->messageProvider->get( ($nbError > 1?'number.errors':'number.error'));
        }
        if ($nbWarning) {
            echo ' '.$nbWarning. $this->messageProvider->get(($nbWarning > 1?'number.warnings':'number.warning'));
        }
        if ($nbNotice) {
            echo ' '.$nbNotice. $this->messageProvider->get(($nbNotice > 1?'number.notices':'number.notice'));
        }

        if($nbError){
            echo '<p>'.$this->messageProvider->get(($nbError > 1?'conclusion.errors':'conclusion.error')).'</p>';
        }else if($nbWarning){
            echo '<p>'.$this->messageProvider->get(($nbWarning > 1?'conclusion.warnings':'conclusion.warning')).'</p>';
        }else if($nbNotice){
            echo '<p>'.$this->messageProvider->get(($nbNotice > 1?'conclusion.notices':'conclusion.notice')).'</p>';
        }else{
            echo '<p>'.$this->messageProvider->get('conclusion.ok').'</p>';
        }
        echo "</div>";
    }
}

$reporter = new jHtmlInstallChecker();
$check = new jInstallCheck($reporter);
$reporter->messageProvider = $check->messages;

header("Content-type:text/html;charset=UTF-8");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $check->messages->getLang(); ?>" lang="<?php echo $check->messages->getLang(); ?>">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type"/>
    <title><?php echo htmlspecialchars($check->messages->get('checker.title')); ?></title>

    <style type="text/css">
    body {font-family: verdana, sans-serif;}

    ul.checkresults {
        border:3px solid black;
        margin: 2em;
        padding:1em;
        list-style-type:none;
    }
    ul.checkresults li { margin:0; padding:5px; border-top:1px solid black; }
    li.checkerror   { background-color:#ff6666;}
    li.checkok      { background-color:#a4ffa9;}
    li.checkwarning { background-color:#ffbc8f;}
    </style>

</head><body >
    <h1><?php echo htmlspecialchars($check->messages->get('checker.title')); ?></h1>

<?php $check->run(); ?>
</body>
</html>
