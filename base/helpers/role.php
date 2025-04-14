<?php

// Get current user roles
function current_user_roles($user_id = null)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }

    if (is_numeric($user_id) && $user_id > 0) {
        return \Yii::$app->authManager->getRolesByUser($user_id);
    }
}

// Get current user roles array
function current_user_roles_array($user_id = null)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }

    $mk = [];
    if (is_numeric($user_id) && $user_id > 0) {
        foreach (\Yii::$app->authManager->getRolesByUser($user_id) as $role => $params) {
            $mk[] = $role;
        }
        return $mk;
    }
}
function user_roles_array($user_id)
{
    $mk = [];
    if (is_numeric($user_id) && $user_id > 0) {
        foreach (\Yii::$app->authManager->getRolesByUser($user_id) as $role => $params) {
            $mk[] = $role;
        }
        return $mk;
    }
}

// current user roles  is $role
function current_user_is_this_role($user_id = null, $roleName)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }

    $roles = (object)\Yii::$app->authManager->getRoles();

    if (property_exists($roles, $roleName)) {
        return true;
    } else {
        return false;
    }
}


function _checkRole($roleName)
{
    $user_id = current_user_id();
    $roles = (object)\Yii::$app->authManager->getRoles();

    if (property_exists($roles, $roleName)) {
        return true;
    } else {
        return false;
    }
}

function _eduRoles()
{
    $rolesPermissions = include '../../api/config/roles-permissions.php';
    $data = [];
    foreach ($rolesPermissions as $role => $permissions) {
        $roleExplode = explode('_', $role);
        if ($roleExplode[0] == 'edu')
            $data[] = $role;
    }
    return $data;
}

function isRole($roleName, $user_id = null)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }

    $roles = (object)\Yii::$app->authManager->getRolesByUser($user_id);

    if (property_exists($roles, $roleName)) {
        return true;
    } else {
        return false;
    }
}
