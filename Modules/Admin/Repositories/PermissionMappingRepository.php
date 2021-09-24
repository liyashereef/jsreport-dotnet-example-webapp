<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\PermissionMapping;
use Spatie\Permission\Models\Role;

class PermissionMappingRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CandidateBrandAwarenessRepository instance.
     *
     * @param  \App\Models\CandidateBrandAwareness $candidateBrandAwareness
     */
    public function __construct(PermissionMapping $permissionMapping)
    {
        $this->model = $permissionMapping;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        $permissions_mapping=(\DB::table('permission_mappings')->select('roles.id', 'permission_mappings.permission_id', \DB::raw('permissions.name AS permission_id'), \DB::raw('roles.name AS role_id')) ->join('roles', 'permission_mappings.role_id', '=', 'roles.id')->join('permissions', 'permission_mappings.permission_id', '=', 'permissions.id')->where('deleted_at', null)->get());
        $data=$permissions_mapping->groupBy('role_id')->toArray();
        return $this->prepareArray($data);
    }

    public function prepareArray($data)
    {
        $arr=$datatable_rows=array();
        foreach ($data as $key => $each_data) {
             $arr['role_id']=$each_data[0]->id;
             $arr['permission_name']=ucwords(str_replace("_", " ", implode(", ", array_column($each_data, "permission_id"))));
             $arr['role_name']=ucwords(str_replace("_", " ", $key));
             array_push($datatable_rows, $arr);
        }
            return $datatable_rows;
    }

    /**
     * Display details of single Request Type
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Display details of single Request Type
     *
     * @param $id
     * @return object
     */
    public function getPermissionBasedOnRole($role_id)
    {
        return $this->model->where('role_id', $role_id)->get();
    }


    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getLookupList()
    {
        return $this->model->orderBy('role_id', 'asc')->pluck('role_id', 'id')->toArray();
    }

    /**
     * Store a newly created Request Type in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($arr)
    {
        $delete_mapping = $this->model->where('role_id', $arr['role_id'])->delete();
        if (isset($arr['permission_id'])) {
            foreach ($arr['permission_id'] as $key => $permission_id) {
                $data['role_id']= $arr['role_id'];
                $data['permission_id']=  $permission_id;
                $this->model->create($data);
            }
        }
       
        return $delete_mapping;
    }

    /**
     * Remove the specified Request Type from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
