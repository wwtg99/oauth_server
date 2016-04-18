<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/16
 * Time: 15:05
 */

namespace App\Model;


class Admin
{

    const SCHEMA = '';

    /**
     * @param $department_id
     * @param $name
     * @return array
     * @throws \Exception
     */
    public static function getDepartment($department_id = null, $name = null)
    {
        $db = getDB()->getConnection('main');
        if (!is_null($department_id)) {
            $re = $db->get(Admin::SCHEMA . 'departments',
                ['department_id', 'name', 'descr', 'created_at', 'updated_at'],
                ['AND' => ['department_id' => $department_id]]);
        } elseif (!is_null($name)) {
            $re = $db->get(Admin::SCHEMA . 'departments',
                ['department_id', 'name', 'descr', 'created_at', 'updated_at'],
                ['AND' => ['name' => $name]]);
        } else {
            $re = $db->select(Admin::SCHEMA . 'departments', ['department_id', 'name', 'descr', 'created_at', 'updated_at']);
        }
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param $department_id
     * @param $name
     * @param $descr
     * @return bool|string
     * @throws \Exception
     */
    public static function createDepartment($department_id, $name, $descr)
    {
        $db = getDB()->getConnection('main');
        $dep = self::getDepartment($department_id);
        if ($dep) {
            return false;
        }
        $db->insert('departments', ['department_id'=>$department_id, 'name'=>$name, 'descr'=>$descr]);
        $dep = self::getDepartment($department_id);
        if ($dep) {
            return $dep['department_id'];
        }
        return false;
    }

    /**
     * @param $department_id
     * @param $name
     * @param $descr
     * @return bool
     * @throws \Exception
     */
    public static function updateDepartment($department_id, $name, $descr)
    {
        $db = getDB()->getConnection('main');
        $re = $db->update('departments', ['name'=>$name, 'descr'=>$descr], ['department_id'=>$department_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $department_id
     * @return bool
     * @throws \Exception
     */
    public static function deleteDepartment($department_id)
    {
        $db = getDB()->getConnection('main');
        $re = $db->delete('departments', ['department_id'=>$department_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $role_id
     * @param $role_name
     * @return array
     * @throws \Exception
     */
    public static function getRole($role_id = null, $role_name = null)
    {
        $db = getDB()->getConnection('main');
        if (!is_null($role_id)) {
            $re = $db->get(Admin::SCHEMA . 'roles', ['role_id', 'name', 'descr', 'created_at', 'updated_at'],
                ['AND' => ['role_id' => $role_id]]);
        } elseif (!is_null($role_name)) {
            $re = $db->get(Admin::SCHEMA . 'roles', ['role_id', 'name', 'descr', 'created_at', 'updated_at'],
                ['AND' => ['name' => $role_name]]);
        } else {
            $re = $db->select(Admin::SCHEMA . 'roles', ['role_id', 'name', 'descr', 'created_at', 'updated_at']);
        }
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param $name
     * @param $descr
     * @return bool|string
     */
    public static function createRole($name, $descr)
    {
        $db = getDB()->getConnection('main');
        $re = Admin::getRole(null, $name);
        if ($re) {
            return false;
        }
        $db->insert(Admin::SCHEMA . 'roles', ['name'=>$name, 'descr'=>$descr]);
        $re = Admin::getRole(null, $name);
        if ($re) {
            return $re['role_id'];
        }
        return false;
    }

    /**
     * @param $role_id
     * @return bool
     */
    public static function deleteRole($role_id)
    {
        //system roles
        if ($role_id == 1 || $role_id == 2) {
            return false;
        }
        $db = getDB()->getConnection('main');
        $re = $db->delete(Admin::SCHEMA . 'roles', ['role_id'=>$role_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $role_id
     * @param $name
     * @param $descr
     * @return bool
     * @throws \Exception
     */
    public static function updateRole($role_id, $name, $descr = null)
    {
        //system roles
        if ($role_id == 1 || $role_id == 2) {
            return false;
        }
        $db = getDB()->getConnection('main');
        $re = $db->update(Admin::SCHEMA . 'roles', ['name'=>$name, 'descr'=>$descr], ['role_id'=>$role_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param $user_name
     * @return array
     */
    public static function getUser($user_id = null, $user_name = null)
    {
        $db = getDB()->getConnection('main');
        if (!is_null($user_id)) {
            $re = $db->get(Admin::SCHEMA . 'view_users',
                ['user_id', 'name', 'label', 'email', 'descr', 'department_id', 'department', 'department_descr', 'superuser', 'roles', 'created_at', 'updated_at', 'deleted_at'],
                ['AND' => ['user_id' => $user_id]]);
        } elseif (!is_null($user_name)) {
            $re = $db->get(Admin::SCHEMA . 'view_users',
                ['user_id', 'name', 'label', 'email', 'descr', 'department_id', 'department', 'department_descr', 'superuser', 'roles', 'created_at', 'updated_at', 'deleted_at'],
                ['AND' => ['name' => $user_name]]);
        } else {
            $re = $db->select(Admin::SCHEMA . 'view_users',
                ['user_id', 'name', 'label', 'email', 'descr', 'department_id', 'department', 'department_descr', 'superuser', 'roles', 'created_at', 'updated_at', 'deleted_at']);
        }
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param array $user
     * @param $roles
     * @return bool
     * @throws \Exception
     */
    public static function createUser(array $user, $roles)
    {
        $db = getDB()->getConnection('main');
        $name = $user['name'];
        $db->insert(Admin::SCHEMA . 'users', $user);
        $uid = $db->query("select " . Admin::SCHEMA . "get_user_id('$name')")->fetchColumn();
        $re = Admin::changeRoles($uid, $roles);
        if ($re) {
            return $uid;
        }
        return false;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public static function deleteUser($user_id)
    {
        $db = getDB()->getConnection('main');
        $db->delete(Admin::SCHEMA . 'users', ['user_id'=>$user_id]);
        return true;
    }

    /**
     * @param $user_id
     * @param array $user
     * @param $roles
     * @return bool
     */
    public static function updateUser($user_id, array $user, $roles)
    {
        $db = getDB()->getConnection('main');
        $db->exec('BEGIN;');
        $re = $db->update(Admin::SCHEMA . 'users', $user, ['user_id'=>$user_id]);
        if ($re) {
            $re = Admin::changeRoles($user_id, $roles);
            if ($re) {
                $db->exec('COMMIT;');
                return true;
            }
        }
        $db->exec('ROLLBACK;');
        return false;
    }

    /**
     * @param string $user_id
     * @param string|array $roles
     * @return bool
     */
    public static function changeRoles($user_id, $roles)
    {
        $db = getDB()->reconnect('main');
        if (!is_array($roles)) {
            $roles = explode(',', $roles);
        }
        $r = [];
        foreach ($roles as $role) {
            array_push($r, ['role_name'=>$role]);
        }
        $roles = json_encode($r);
        $re = $db->queryOne("select " . Admin::SCHEMA . "change_roles(:uid, cast(:roles as json))", ['uid'=>$user_id, 'roles'=>$roles]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public static function resetPassword($user_id)
    {
        $db = getDB()->getConnection('main');
        $re = $db->update(Admin::SCHEMA . 'users', ['password'=>null], ['user_id'=>$user_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param bool $active
     * @return bool
     * @throws \Exception
     */
    public static function activeUser($user_id, $active = true)
    {
        $db = getDB()->getConnection('main');
        if ($active) {
            $re = $db->query('select ' . Admin::SCHEMA . "active_user('$user_id')")->fetchColumn();
            if ($re) {
                return true;
            }
        } else {
            $db->delete(Admin::SCHEMA . 'users', ['user_id'=>$user_id]);
            return true;
        }
        return false;
    }
} 