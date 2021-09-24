<?php

namespace Modules\Admin\Repositories;

use App\Models\Module;
use Spatie\Permission\Models\Role;
use Modules\Admin\Models\User;

class RolesAndPermissionRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $module, $role;

    /**
     * Create a new Repository instance.
     *
     * @param  \App\Models\Module $module
     * @param  Spatie\Permission\Models\Role $role
     */
    public function __construct()
    {
        $this->module = new Module();
        $this->role =  new Role();
    }

    /**
     * Get Roles list
     *
     * @param empty
     * @return array
     */
    public function getRoleList()
    {
        $roleList = $this->role->select(['id', 'name'])->where('name', '!=', 'super_admin')->orderBy('name')->get();
        return $roleList;
    }

    /**
     * Return all Parameters required to render in the Add and Edit view
     *
     * @param $id
     * @return array
     */
    public function addEdit($id = null)
    {
        $permission_array = array();
        $role_name = null;
        $permission_name = array();
        $modules = Module::with('permission_model', 'permission_model.permission')->get();
        if ($id) {
            $checked_permissions = Role::findById($id)->permissions;
            $role_name = Role::findById($id)->name;
            foreach ($checked_permissions as $permission) {
                $permission_array[] = $permission->id;
                $permission_name[] = $permission->name;
            }
        }
        return (['modules' => $modules, 'permission_array' => $permission_array, 'role_name' => $role_name, 'id' => $id, 'permission_name' => $permission_name]);
    }

    /**
     * Store a newly created Role and Permissions in storage.
     *
     * @param  $request
     * @return boolean
     */
    public function save($request)
    {
        $role = $this->role->updateOrCreate(['id' => $request->get('id')], ['name' => $request->get('role')]);
        $permission_array = ($request->get('module_permissions')) ?? array();
        $role->syncPermissions($permission_array);
        return true;
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  $id
     * @return boolean
     */
    public function destroyRole($id)
    {
        $roledetail = $this->role->find($id);
        $rolename = $roledetail->name;
       
        $usercount = User::whereHas("roles", function ($q) use ($rolename) {
            $q->where("name", $rolename);
        })->count();
        if ($usercount>0) {
            return false;
        } else {
            $permissions = $this->role->findById($id)->permissions;
            $role = $this->role->findOrFail($id);
            foreach ($permissions as $permission) {
                $role->revokePermissionTo($permission);
            }
            $role->delete();
            return true;
        }
    }

    /**
     * Return Role List as array
     * @param  null
     * @return array
     */
    public function defaultRolesArray()
    {
        $existing_roles = array(
            'super_admin',
            'admin',
            'ceo',
            'coo',
            'area_manager',
            'supervisor',
            'guard',
            'duty_officer',
            'hr_representative',
            'cfo',
            'vice_president',
            'regional_manager',
            'client',
            'payroll_clerk',
            'hr_manager',
            'training_manager',
            'quarter_master'
        );
        return $existing_roles;
    }

    /**
     * Get all base permissions in key value pair (both slug and text) except array
     * @param Array $except
     * @return Array
     */
    public function getBasePermissionAsRoleArray($except = [])
    {
        $data =  [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'supervisor' => 'Supervisor',
            'area_manager' => 'Area Manager',
            'guard' => 'Guard',
            'client' => 'Client',
            'manager' => 'Manager',
            'employee' => 'Employee',
            'cfo' => 'CFO',
            'hr_manager' => 'HR Manager',
            'hr_representative' => 'HR Representative',
            'duty_officer' => 'Duty Officer'
        ];
        return array_diff_key($data, array_flip($except));
    }
    /**
     * Get all permissions slugs except arguments.
     * @param Array $except
     * @return Array
     */
    public function getBasePermissionAsRoleArraySlugs($except = [])
    {
        return array_keys($this->getBasePermissionAsRoleArray($except));
    }

    /**
     * Get Roles list except the array in parameter
     *
     * @param array
     * @return array
     */
    public function getDefaultRoleListForRoleHierarchy($role_except)
    {
        $roleList = $this->role->whereNotIn('name', $role_except)->pluck('name');
        return $roleList;
    }
}
