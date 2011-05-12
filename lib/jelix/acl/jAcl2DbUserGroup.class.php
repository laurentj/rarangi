<?php
/**
* @package     jelix
* @subpackage  acl
* @author      Laurent Jouanneau
* @contributor Julien Issler, Vincent Viaud
* @copyright   2006-2010 Laurent Jouanneau
* @copyright   2009 Julien Issler
* @copyright   2011 Vincent Viaud
* @link        http://www.jelix.org
* @licence     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @since 1.1
*/

/**
 * Use this class to register or unregister users in the acl system, and to manage user groups.
 *  Works only with db driver of jAcl2.
 * @package     jelix
 * @subpackage  acl
 * @static
 */
class jAcl2DbUserGroup {

    /**
     * @internal The constructor is private, because all methods are static
     */
    private function __construct (){ }

    /**
     * Says if the current user is a member of the given user group
     * @param string $groupid The id of a group
     * @return boolean true if it's ok
     */
    public static function isMemberOfGroup ($groupid){
        return in_array($groupid, self::getGroups());
    }

    protected static $groups = null;

    /**
     * retrieve the list of group the current user is member of
     * @return array list of group id
     */
    public static function getGroups(){
        if(!jAuth::isConnected())
            return array();

        // chargement des groupes
        if(self::$groups === null){
            $gp = jDao::get('jacl2db~jacl2usergroup', 'jacl2_profile')
                    ->getGroupsUser(jAuth::getUserSession()->login);
            self::$groups = array();
            foreach($gp as $g){
                self::$groups[] = $g->id_aclgrp;
            }

        }
        return self::$groups;
    }

    /**
     * get the private group for the current user or for the given login
     * @param string $login The user's login
     * @return string the id of the private group
     * @since 1.2
     */
    public static function getPrivateGroup($login=null){
        if(!$login){
            if(!jAuth::isConnected())
                return null;
            $login = jAuth::getUserSession()->login;
        }
        return jDao::get('jacl2db~jacl2group', 'jacl2_profile')->getPrivateGroup($login)->id_aclgrp;
    }

    /**
     * get a group
     * @param string $code The code
     * @return jacl2group|false the dao object r false if it doesn't exist
     * @since 1.2
     */
    public static function getGroup($code){
        return jDao::get('jacl2db~jacl2group', 'jacl2_profile')->get($code);
    }

    /**
     * get the list of the users of a group
     * @param string $groupid  id of the user group
     * @return array a list of users object (dao records)
     */
    public static function getUsersList($groupid){
        return jDao::get('jacl2db~jacl2usergroup', 'jacl2_profile')->getUsersGroup($groupid);
    }

    /**
     * register a user in the acl system
     *
     * For example, this method is called by the acl module when responding
     * to the event generated by the auth module when a user is created.
     * When a user is registered, a private group is created.
     * @param string $login the user login
     * @param boolean $defaultGroup if true, the user become the member of default groups
     */
    public static function createUser($login, $defaultGroup=true){
        $daousergroup = jDao::get('jacl2db~jacl2usergroup','jacl2_profile');
        $daogroup = jDao::get('jacl2db~jacl2group','jacl2_profile');
        $usergrp = jDao::createRecord('jacl2db~jacl2usergroup','jacl2_profile');
        $usergrp->login = $login;

        // si $defaultGroup -> assign le user aux groupes par defaut
        if($defaultGroup){
            $defgrp = $daogroup->getDefaultGroups();
            foreach($defgrp as $group){
                $usergrp->id_aclgrp = $group->id_aclgrp;
                $daousergroup->insert($usergrp);
            }
        }

        // create a private group
        $persgrp = jDao::createRecord('jacl2db~jacl2group','jacl2_profile');
        $persgrp->id_aclgrp = '__priv_'.$login;
        $persgrp->name = $login;
        $persgrp->grouptype = 2;
        $persgrp->ownerlogin = $login;

        $daogroup->insert($persgrp);
        $usergrp->id_aclgrp = $persgrp->id_aclgrp;
        $daousergroup->insert($usergrp);
    }

    /**
     * add a user into a group
     *
     * (a user can be a member of several groups)
     * @param string $login the user login
     * @param string $groupid the group id
     */
    public static function addUserToGroup($login, $groupid){
        if( $groupid == '__anonymous')
            throw new Exception ('jAcl2DbUserGroup::addUserToGroup : invalid group id');
        $usergrp = jDao::createRecord('jacl2db~jacl2usergroup','jacl2_profile');
        $usergrp->login = $login;
        $usergrp->id_aclgrp = $groupid;
        jDao::get('jacl2db~jacl2usergroup','jacl2_profile')->insert($usergrp);
    }

    /**
     * remove a user from a group
     * @param string $login the user login
     * @param string $groupid the group id
     */
    public static function removeUserFromGroup($login,$groupid){
        jDao::get('jacl2db~jacl2usergroup', 'jacl2_profile')->delete($login,$groupid);
    }

    /**
     * unregister a user in the acl system
     * @param string $login the user login
     */
    public static function removeUser($login){
        $daogroup = jDao::get('jacl2db~jacl2group','jacl2_profile');

        // recupere le groupe privé
        $privategrp = $daogroup->getPrivateGroup($login);
        if(!$privategrp) return;

        // supprime les droits sur le groupe privé (jacl_rights)
        jDao::get('jacl2db~jacl2rights','jacl2_profile')->deleteByGroup($privategrp->id_aclgrp);

        // l'enleve de tous les groupes (jacl_users_group)
        jDao::get('jacl2db~jacl2usergroup','jacl2_profile')->deleteByUser($login);

        // supprime le groupe personnel du user (jacl_group)
        $daogroup->delete($privategrp->id_aclgrp);
    }

    /**
     * create a new group
     * @param string $name its name
     * @param string $id_aclgrp its id
     * @return string the id of the new group
     */
    public static function createGroup($name, $id_aclgrp = null){
        if ($id_aclgrp === null)
            $id_aclgrp = strtolower(str_replace(' ', '_',$name));
        $group = jDao::createRecord('jacl2db~jacl2group','jacl2_profile');
        $group->id_aclgrp = $id_aclgrp;
        $group->name = $name;
        $group->grouptype = 0;
        jDao::get('jacl2db~jacl2group','jacl2_profile')->insert($group);
        return $group->id_aclgrp;
    }

    /**
     * set a group to be default (or not)
     *
     * there can have several default group. A default group is a group
     * where a user is assigned to during its registration
     * @param string $groupid the group id
     * @param boolean $default true if the group is to be default, else false
     */
    public static function setDefaultGroup($groupid, $default=true){
        if( $groupid == '__anonymous')
            throw new Exception ('jAcl2DbUserGroup::setDefaultGroup : invalid group id');

        $daogroup = jDao::get('jacl2db~jacl2group', 'jacl2_profile');
        if($default)
            $daogroup->setToDefault($groupid);
        else
            $daogroup->setToNormal($groupid);
    }

    /**
     * change the name of a group
     * @param string $groupid the group id
     * @param string $name the new name
     */
    public static function updateGroup($groupid, $name){
        if( $groupid == '__anonymous')
            throw new Exception ('jAcl2DbUserGroup::updateGroup : invalid group id');
        jDao::get('jacl2db~jacl2group','jacl2_profile')->changeName($groupid,$name);
    }

    /**
     * delete a group from the acl system
     * @param string $groupid the group id
     */
    public static function removeGroup($groupid){
        if( $groupid == '__anonymous')
            throw new Exception ('jAcl2DbUserGroup::removeGroup : invalid group id');
        // enlever tous les droits attachés au groupe
        jDao::get('jacl2db~jacl2rights','jacl2_profile')->deleteByGroup($groupid);
        // enlever les utilisateurs du groupe
        jDao::get('jacl2db~jacl2usergroup','jacl2_profile')->deleteByGroup($groupid);
        // suppression du groupe
        jDao::get('jacl2db~jacl2group','jacl2_profile')->delete($groupid);
    }

    /**
     * return a list of group.
     *
     * if a login is given, it returns only the groups of the user.
     * Else it returns all groups (except private groups)
     * @param string $login an optional login
     * @return array a list of groups object (dao records)
     */
    public static function getGroupList($login=''){
        if ($login === '') {
            return jDao::get('jacl2db~jacl2group', 'jacl2_profile')->findAllPublicGroup();
        }else{
            return jDao::get('jacl2db~jacl2groupsofuser','jacl2_profile')->getGroupsUser($login);
        }
    }

    /**
     * clear cache of variables of this class
     * @since 1.3
     */
    public static function clearCache(){
        self::$groups = null;
    }

}
