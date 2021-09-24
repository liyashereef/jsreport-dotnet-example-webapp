<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ShiftModuleDropdownRequest;
use Modules\Admin\Models\ShiftModuleDropdown;
use Modules\Admin\Models\ShiftModuleDropdownOption;
use Modules\Admin\Models\ShiftModuleField;
use Modules\Admin\Repositories\ShiftModuleDropdownRepository;

class ShiftModuleDropdownController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\ShiftModuleDropdownRepository $shiftModuleDropdownRepository
     * @return void
     */
    public function __construct(ShiftModuleDropdownRepository $shiftModuleDropdownRepository, HelperService $helperService)
    {
        $this->repository = $shiftModuleDropdownRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::shift-module.shift-module-dropdown');
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        $dropdownlist = $this->repository->getAll();
        return datatables()->of($this->prepareDataforDropdownList($dropdownlist))->addIndexColumn()->toJson();
    }

    /**
     * Prepare datatable elements as array.
     * @param  $dropdownlist
     * @return array
     */
    public function prepareDataforDropdownList($dropdownlist)
    {
        $datatable_rows = array();
        foreach ($dropdownlist as $key => $each_list) {
            $each_row["id"] = $each_list->id;
            $each_row["dropdown_name"] = $each_list->dropdown_name;
            $each_row["post_order"] = ($each_list->post_order == 1) ? 'Yes' : 'No';
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }
    
    /**
     * Controller for GET method of add and edit of template
     *
     * @param int $dropdown_id
     */
    public function addDropdown($dropdown_id = null)
    {
        $dropdown_exists = 0;
        if (isset($dropdown_id)) {
            $option_list = ShiftModuleDropdownOption::where('shift_module_dropdown_id', $dropdown_id)->get();
            $dropdown_arr = ShiftModuleDropdown::with('shiftModuleDropdownOption')->where('id', $dropdown_id)->first()->toArray();
            $shiftmodule_dropdown_id = ShiftModuleField::pluck('dropdown_id')->toArray();
            if (in_array($dropdown_id, $shiftmodule_dropdown_id)) {
                $dropdown_exists = 1;
            } else {
                $dropdown_exists = 0;
            }
        }

        return view('admin::shift-module.add', compact('option_list', 'dropdown_arr', 'dropdown_exists'));
    }

    /**
     * Function to save Dropdown
     * @param Request $request
     * @return json
     */
    public function storeDropdown(ShiftModuleDropdownRequest $request)
    {
        try {
            \DB::beginTransaction();
            $dropdown_data['dropdown_name'] = $request->get('dropdown_name');
            $info_id = $request->get('info_id');
            $dropdown_data['post_order'] = $request->get('post_order');
            if (isset($info_id) && ($info_id) != null) {
                $dropdown_data['info'] = $info_id;
            }
           // $dropdown_data['detail'] = $request->get('detail');
            $obj_dropdown = ShiftModuleDropdown::updateOrCreate(array('id' => $request->get('id')), $dropdown_data);
            $option_arr=ShiftModuleDropdownOption::where('shift_module_dropdown_id', $request->get('id'))->pluck('id')->toArray();
            $diff_arr=array_diff($option_arr, $request->option_id);
            foreach ($diff_arr as $key => $optionId) {
                ShiftModuleDropdownOption::where('id', $optionId)->delete();
                unset($request->request->option_name[$key]);
                unset($request->request->option_info[$key]);
                unset($request->request->option_id[$key]);
                unset($request->request->order_sequence[$key]);
            }
            $template_id = $obj_dropdown->id;
            for ($i = 0; $i < count($request->get('option_name')); $i++) {
                $shiftmodule_dropdown_data = [
                        'shift_module_dropdown_id' => $template_id,
                        'option_name' => $request->option_name[$i],
                         'option_info' => $request->option_info[$i],
                        'order_sequence' => $request->order_sequence[$i],
                    ];
                    ShiftModuleDropdownOption::updateOrCreate(array('id' => $request->option_id[$i]), $shiftmodule_dropdown_data);
            }

            \DB::commit();
            return response()->json(array('success' => 'true'));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $shiftmodule_dropdown_id = ShiftModuleField::pluck('dropdown_id')->toArray();
            if (in_array($id, $shiftmodule_dropdown_id)) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                $lookup_delete = $this->repository->delete($id);
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
