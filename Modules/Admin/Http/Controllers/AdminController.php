<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\ModulePermission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\SettingsRequest;
use Modules\Admin\Repositories\ConfigAppRepository;
use Modules\Admin\Repositories\EnvRepository;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Show the settings page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsEdit()
    {
        $actualLocale = config('app.locale');
        //$locales = locales();

        $actualDriver = env('MAIL_DRIVER');
        $drivers = [
            'smtp' => 'SMTP',
            'mail' => 'PHP',
        ];

        $actualTimezone = config('app.timezone');
        //$timezones = timezones ();

        $actualCacheDriver = env('CACHE_DRIVER');
        $caches = ['apc', 'array', 'database', 'file', 'memcached', 'redis'];

        $actualConnection = env('DB_CONNECTION');
        $connections = ['mysql', 'sqlite', 'pgsql'];

        return view('admin::settings.settings', compact(
           // 'locales',
             'actualLocale',
             'drivers',
             'actualDriver',
           //  'timezones',
             'actualTimezone',
             'caches',
             'actualCacheDriver',
             'connections',
             'actualConnection'
        ));
    }

    /**
     * Update settings
     *
     * @param \App\Http\Requests\SettingsRequest $request
     * @param \App\Repositories\ConfigAppRepository $appRepository
     * @param \App\Repositories\EnvRepository $envRepository
     * @return \Illuminate\Http\RedirectResponse
     * @internal param ConfigAppRepository $repository
     */
    public function settingsUpdate(SettingsRequest $request, ConfigAppRepository $appRepository, EnvRepository $envRepository)
    {
        $inputs = $request->except('_method', '_token', 'page');

        $envRepository->update(array_filter($inputs, function ($key) {
            return strpos($key, '_');
        }, ARRAY_FILTER_USE_KEY));

        $appRepository->update(array_filter($inputs, function ($key) {
            return !strpos($key, '_');
        }, ARRAY_FILTER_USE_KEY));

        $cache = $this->checkCache() ? ' ' . __('Config cache has been updated.') : '';

        $request->session()->flash('ok', __('Settings have been successfully saved. ') . $cache);

        return redirect()->route('settings.edit', ['page' => $request->page]);
    }

    /**
     * Check and refresh cache if exists
     *
     * @return bool
     */
    protected function checkCache()
    {
        if (file_exists(app()->getCachedConfigPath())) {
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            return true;
        }
        return false;
    }

    /**
     * Create Permissions
     *
     * @return object
     */
    public function createPermission()
    {
        return view('admin::create-permission');
    }
    /**
     * Store Permissions
     *
     * @return object
     */
    public function storePermission(Request $request)
    {
        $permission_name = $request->get('permission');
        $module_id = $request->get('module');
        $description = $request->get('description');
        $permission = Permission::create(['name' => $permission_name, 'guard_name' => 'web']);
        $role = Role::findByName('super_admin');
        $role->givePermissionTo($permission);
        $ModulePermission = new ModulePermission;
        $ModulePermission->module_id = $module_id;
        $ModulePermission->permission_description = $description;
        $ModulePermission->permission_id = $permission->id;
        $ModulePermission->save();
        return response()->json(['success' => true]);
    }
}
