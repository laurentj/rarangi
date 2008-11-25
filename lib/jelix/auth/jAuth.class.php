<?php
/**
* @package    jelix
* @subpackage auth
* @author     Laurent Jouanneau
* @contributor Frédéric Guillot, Antoine Detante, Julien Issler
* @copyright  2001-2005 CopixTeam, 2005-2008 Laurent Jouanneau, 2007 Frédéric Guillot, 2007 Antoine Detante
* @copyright  2007 Julien Issler
*
* This classes were get originally from an experimental branch of the Copix project (Copix 2.3dev, http://www.copix.org)
* Few lines of code are still copyrighted 2001-2005 CopixTeam (LGPL licence).
* Initial author of this Copix classes is Laurent Jouanneau, and this classes were adapted for Jelix by him
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require(JELIX_LIB_PATH.'auth/jIAuthDriver.iface.php');

/**
 * This is the main class for authentification process
 * @package    jelix
 * @subpackage auth
 */
class jAuth {

    /**
     * Load the configuration of authentification, stored in the auth plugin config
     * @return array
     */
    protected static function  _getConfig(){
        static $config = null;
        if($config == null){
            global $gJCoord;
            $plugin = $gJCoord->getPlugin('auth');
            if($plugin === null){
                throw new jException('jelix~auth.error.plugin.missing');
            }
            $config = & $plugin->config;

            if (!isset($config['session_name'])
                || $config['session_name'] == ''){

                $config['session_name'] = 'JELIX_USER';
            }
            if (!isset( $config['persistant_cookie_path']) 
                ||  $config['persistant_cookie_path'] == '') {
                $config['persistant_cookie_path'] = $GLOBALS['gJConfig']->urlengine['basePath'];
            }
        }
        return $config;
    }

    /**
     * load the auth driver
     * @return jIAuthDriver
     */
    protected static function _getDriver(){
        static $driver = null;
        if($driver == null){
            $config = self::_getConfig();
            global $gJConfig;
            $db = strtolower($config['driver']);
            if(!isset($gJConfig->_pluginsPathList_auth) 
                || !isset($gJConfig->_pluginsPathList_auth[$db])
                || !file_exists($gJConfig->_pluginsPathList_auth[$db]) ){
                throw new jException('jelix~auth.error.driver.notfound',$db);
            }
            require_once($gJConfig->_pluginsPathList_auth[$db].$db.'.auth.php');
            $dname = $config['driver'].'AuthDriver';
            $driver = new $dname($config[$config['driver']]);
        }
        return $driver;
    }

    /**
     * load user data
     *
     * This method returns an object, generated by the driver, and which contains
     * data corresponding to the given login. This method should be called if you want
     * to update data of a user. see updateUser method.
     *
     * @param string $login
     * @return object the user
     */
    public static function getUser($login){
        $dr = self::_getDriver();
        return $dr->getUser($login);
    }

    /**
     * Create a new user object
     * 
     * You should call this method if you want to create a new user. It returns an object,
     * representing a user. Then you should fill its properties and give it to the saveNewUser
     * method.
     * 
     * @param string $login the user login
     * @param string $password the user password (not encrypted)
     * @return object the returned object depends on the driver
     * @since 1.0b2
     */
    public static function createUserObject($login,$password){
        $dr = self::_getDriver();
        return $dr->createUserObject($login,$password);
    }

    /**
     * Save a new user 
     * 
     * if the saving has succeed, a AuthNewUser event is sent
     * The given object should have been created by calling createUserObject method :
     *
     * example :
     *  <pre>
     *   $user = jAuth::createUserObject('login','password');
     *   $user->email ='bla@foo.com';
     *   jAuth::saveNewUser($user);
     *  </pre>
     *  the type of $user depends of the driver, so it can have other properties.
     *
     * @param  object $user the user data
     * @return object the user (eventually, with additional data)
     */
    public static function saveNewUser($user){
        $dr = self::_getDriver();
        if($dr->saveNewUser($user)){
            jEvent::notify ('AuthNewUser', array('user'=>$user));
        }
        return $user;
    }

    /**
     * update user data
     * 
     * It send a AuthUpdateUser event if the saving has succeed. If you want
     * to change the user password, you must use jAuth::changePassword method
     * instead of jAuth::updateUser method.
     *
     * The given object should have been created by calling getUser method.
     * Example :
     *  <pre>
     *   $user = jAuth::getUser('login');
     *   $user->email ='bla@foo.com';
     *   jAuth::updateUser($user);
     *  </pre>
     *  the type of $user depends of the driver, so it can have other properties.
     * 
     * @param object $user  user data
     */
    public static function updateUser($user){
        $dr = self::_getDriver();
        if($user = $dr->updateUser($user)){
            jEvent::notify ('AuthUpdateUser', array('user'=>$user));
        }
    }

    /**
     * remove a user
     * send first AuthCanRemoveUser event, then if ok, send AuthRemoveUser
     * and then remove the user.
     * @param string $login the user login
     * @return boolean true if ok
     */
    public static function removeUser($login){
        $dr = self::_getDriver();
        $eventresp = jEvent::notify ('AuthCanRemoveUser', array('login'=>$login));
        foreach($eventresp->getResponse() as $rep){
            if(!isset($rep['canremove']) || $rep['canremove'] === false){
                return false;
            }
        }
        jEvent::notify ('AuthRemoveUser', array('login'=>$login));
        return $dr->removeUser($login);
    }

    /**
     * construct the user list
     * @param string $pattern '' for all users
     * @return array array of object
     */
    public static function getUserList($pattern = '%'){
        $dr = self::_getDriver();
        return $dr->getUserlist($pattern);
    }

    /**
     * change a user password
     *
     * @param string $login the login of the user
     * @param string $newpassword the new password (not encrypted)
     * @return boolean true if the change succeed
     */
    public static function changePassword($login, $newpassword){
        $dr = self::_getDriver();
        return $dr->changePassword($login, $newpassword);
    }

    /**
     * verify that the password correspond to the login
     * @param string $login the login of the user
     * @param string $password the password to test (not encrypted)
     * @return object|false  if ok, returns the user as object
     */
    public static function verifyPassword($login, $password){
        $dr = self::_getDriver();
        return $dr->verifyPassword($login, $password);
    }

    /**
     * authentificate a user, and create a user in the php session
     * @param string $login the login of the user
     * @param string $password the password to test (not encrypted)
     * @param boolean $persistant (optional) the session must be persistant
     * @return boolean true if authentification is ok
     */
    public static function login($login, $password, $persistant=false){

        $dr = self::_getDriver();
        $config = self::_getConfig();

        if($user = $dr->verifyPassword($login, $password)){

            $eventresp = jEvent::notify ('AuthCanLogin', array('login'=>$login, 'user'=>$user));
            foreach($eventresp->getResponse() as $rep){
                if(!isset($rep['canlogin']) || $rep['canlogin'] === false){
                    return false;
                }
            }

            $_SESSION[$config['session_name']] = $user;
            $persistence = 0;

            // Add a cookie for session persistance, if enabled
            if($persistant && isset($config['persistant_enable']) && $config['persistant_enable']) {
                if(!isset($config['persistant_crypt_key']) || !isset($config['persistant_cookie_name'])){
                    throw new jException('jelix~auth.error.persistant.incorrectconfig','persistant_cookie_name, persistant_crypt_key');
                }

                if(isset($config['persistant_duration']))
                    $persistence=$config['persistant_duration']*86400;
                else
                    $persistence=86400; // 24h
                $persistence += time();
                $encryptedPassword=jCrypt::encrypt($password,$config['persistant_crypt_key']);
                setcookie($config['persistant_cookie_name'].'[login]', $login, $persistence, $config['persistant_cookie_path']);
                setcookie($config['persistant_cookie_name'].'[passwd]', $encryptedPassword, $persistence, $config['persistant_cookie_path']);
            }

            jEvent::notify ('AuthLogin', array('login'=>$login, 'persistence'=>$persistence));
            return true;
        }else
            return false;
    }

    /**
     * Check if persistant session is enabled in config
     * @return boolean true if persistant session in enabled
     */
    public static function isPersistant(){
        $config = self::_getConfig();
        if(!isset($config['persistant_enable']))
            return false;
        else
            return $config['persistant_enable'];
    }

    /**
     * logout a user and delete the user in the php session
     */
    public static function logout(){

        $config = self::_getConfig();
        jEvent::notify ('AuthLogout', array('login'=>$_SESSION[$config['session_name']]->login));
        $_SESSION[$config['session_name']] = new jAuthDummyUser();
        jAcl::clearCache();
        if(isset($config['persistant_enable']) && $config['persistant_enable']){
            if(!isset($config['persistant_cookie_name']))
                throw new jException('jelix~auth.error.persistant.incorrectconfig','persistant_cookie_name, persistant_crypt_key');
            setcookie($config['persistant_cookie_name'].'[login]', '', time() - 3600, $config['persistant_cookie_path']);
            setcookie($config['persistant_cookie_name'].'[passwd]', '', time() - 3600, $config['persistant_cookie_path']);
        }
    }

    /**
     * Says if the user is connected
     * @return boolean
     */
    public static function isConnected(){
        $config = self::_getConfig();
        return (isset($_SESSION[$config['session_name']]) && $_SESSION[$config['session_name']]->login != '');
    }

   /**
    * return the user stored in the php session
    * @return object the user data
    */
    public static function getUserSession (){
        $config = self::_getConfig();
        if (! isset ($_SESSION[$config['session_name']])){
            $_SESSION[$config['session_name']] = new jAuthDummyUser();
        }
        return $_SESSION[$config['session_name']];
    }

    /**
     * generate a password with random letter or number
     * @param int $length the length of the generated password
     * @return string the generated password
     */
    public static function getRandomPassword($length = 10){
        $letter = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $pass = '';
        for($i=0;$i<$length;$i++){
            $pass .= $letter{rand(0,61)};
        }
        return $pass;
    }
}
