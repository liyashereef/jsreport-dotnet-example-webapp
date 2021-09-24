<?php

namespace Modules\Admin\Repositories;

use App\Repositories\AttachmentRepository;
use Carbon\Carbon;
use Config;
use DB;
use Log;
use Modules\Admin\Models\AdminColorsetting;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\ShiftModule;
use Modules\Admin\Models\ShiftModuleDropdownOption;
use Modules\Admin\Models\ShiftModuleEntry;
use Modules\Admin\Models\ShiftModuleEntryAttachment;
use Modules\Admin\Models\ShiftModuleField;
use Modules\FeverScan\Models\FeverReading;
use Modules\FeverScan\Repositories\FeverReadingRepository;
use Modules\Supervisorpanel\Models\ShiftModulePostOrder;
use Modules\Timetracker\Repositories\ImageRepository;

class ShiftModuleRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new SiteNoteStatusLookup instance.
     *
     * @param  \App\Models\SiteNoteStatusLookup $siteNoteStatusLookup
     */
    public function __construct(
        ShiftModule $shiftModule,
        ShiftModuleEntry $shiftModuleEntry,
        ImageRepository $imageRepository,
        AttachmentRepository $attachment_repository,
        ShiftModuleEntryAttachment $shiftModuleEntryAttachment,
        FeverReadingRepository $feverReadingRepository
    ) {
        $this->model = $shiftModule;
        $this->shiftModuleEntrymodel = $shiftModuleEntry;
        $this->imageRepository = $imageRepository;
        $this->attachment_repository = $attachment_repository;
        $this->shiftModuleEntryAttachment = $shiftModuleEntryAttachment;
        $this->feverReadingRepository = $feverReadingRepository;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll($customer_id = null, $activeOnly = false)
    {
        $module_list = $this->model->select(['id', 'customer_id', 'module_name', 'enable_timeshift', 'dashboard_view', 'is_active', 'post_order', 'created_at', 'updated_at'])->with('customer');
        if ($customer_id != null) {
            $module_list = $module_list->where('customer_id', $customer_id);
        }
        if ($activeOnly) {
            $module_list->where('is_active', true);
        }
        return $module_list = $module_list->get();
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
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getLookupList()
    {
        return $this->model->orderBy('order_sequence')->pluck('status', 'id')->toArray();
    }

    /**
     * Store a newly created Request Type in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $lookup = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
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

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllModuleDetails($customer_id)
    {
        $query = $this->model->with('shiftmodulefield.fieldtype', 'shiftmodulefield.dropdown', 'customer', 'customer.geoFenceDetails')->where('customer_id', $customer_id)->where('is_active', 1);

        $query->with(array(
            'shiftmodulefield' => function ($query) {
                $query->where('field_status', 1)->orderBy('order_id');
            },
            'shiftmodulefield.dropdown.shiftModuleDropdownOption' => function ($query) {
                $query->orderBy('order_sequence');
            },

        ));
        return $query->get()->toArray();
    }

    public function addCustomerModuleDetails($request, $userid)
    {
        if ($request->fieldData) {
            $count = 0;
            $entryids = array();
            $created_at = null;
            foreach ($request->fieldData as $eachreq) {

                if (sizeof($eachreq) > 0) {
                    foreach ($eachreq as $eachdata) {
                        $fields = ShiftModuleField::where('id', '=', $eachdata['fieldId'])->first();
                        $shiftModuleEntry = new ShiftModuleEntry;
                        $shiftModuleEntry->customer_id = $request->customerId;
                        $shiftModuleEntry->module_id = $request->moduleId;

                        if ($fields->field_type == 1) {
                            $shiftModuleEntry->field_id = $eachdata['fieldId'];
                            $shiftModuleEntry->field_value = '';
                        } else if ($fields->field_type == 2) {
                            $shiftModuleEntry->field_id = $eachdata['fieldId'];
                            $shiftModuleEntry->field_value = $eachdata['value']['latitude'] . '#' . $eachdata['value']['longitude'];
                        } else if ($fields->field_type == 3 || $fields->field_type == 6 || $fields->field_type == 8) {
                            $shiftModuleEntry->field_id = $eachdata['fieldId'];
                            $option = ShiftModuleDropdownOption::where('id', '=', $eachdata['value'])->first();
                            $shiftModuleEntry->field_value = isset($option->option_name) ? $option->option_name : '';
                        } else if ($fields->field_type == 5) {
                            $shiftModuleEntry->field_id = $eachdata['fieldId'];
                            $shiftModuleEntry->field_value = '';
                        } else {
                            $shiftModuleEntry->field_id = $eachdata['fieldId'];
                            $shiftModuleEntry->field_value = $eachdata['value'];
                        }

                        $shiftModuleEntry->shift_id = isset($request->shiftId) ? $request->shiftId : 0;
                        $shiftModuleEntry->attachment_id = 0;
                        $shiftModuleEntry->shift_start_date = $request->shiftStartTime;
                        $shiftModuleEntry->created_by = $userid;
                        $shiftModuleEntry->updated_by = $userid;
                        $shiftModuleEntry->save();

                        if ($count == 0) {
                            $created_at = $shiftModuleEntry->created_at;
                            $count++;
                        }
                        array_push($entryids, $shiftModuleEntry->id);

                        if (($fields->field_type == 1) && ($shiftModuleEntry->id != 0)) {
                            if (isset($eachdata['value']) && !empty($eachdata['value'])) {
                                foreach ($eachdata['value'] as $imgkey => $eachimage) {
                                    $imagefile = $this->imageRepository->imageFromBase64($eachimage);
                                    $request['user_id'] = $userid;
                                    $request['customer_id'] = $request->customerId;
                                    $attachment_id = $this->attachment_repository->saveBase64ImageFile('shift-module', $request, $imagefile);

                                    $this->shiftModuleEntryAttachment->create(
                                        [
                                            'shift_module_entry_id' => $shiftModuleEntry->id,
                                            'attachment_id' => $attachment_id,
                                            'created_by' => $userid,
                                        ]
                                    );
                                }
                            }
                        }

                        if (($fields->field_type == 5) && ($shiftModuleEntry->id != 0) && isset($eachdata['value'])) {
                            $this->shiftModuleEntryAttachment->create(
                                [
                                    'shift_module_entry_id' => $shiftModuleEntry->id,
                                    'attachment_id' => $eachdata['value'],
                                    'created_by' => $userid,
                                ]
                            );
                        }
                    }
                }
            }
            $this->shiftModuleEntrymodel->whereIn('id', $entryids)->update(['created_at' => $created_at]);

            //BEGIN:- Fever Scan module changes
            $fever_scan_module = $this->model->where('id', $request->moduleId)->where('module_name', '=', Config::get('globals.fever_scan_module'))->count();
            if ($fever_scan_module) {
                $customerdetail = Customer::find($request->customerId);
                try {
                    \DB::beginTransaction();
                    $feverReading = new FeverReading;
                    foreach ($request->fieldData as $eachreq) {
                        $feverReading->customer_id = $request->customerId;
                        $feverReading->module_id = $request->moduleId;
                        $feverReading->shift_id = isset($request->shiftId) ? $request->shiftId : 0;
                        $feverReading->created_by = $userid;
                        if (sizeof($eachreq) > 0) {
                            foreach ($eachreq as $eachdata) {
                                $fields = ShiftModuleField::where('id', '=', $eachdata['fieldId'])->first();

                                if ($fields->system_name == 'email') {
                                    $feverReading->email = $eachdata['value'];
                                } else if ($fields->system_name == 'name') {
                                    $feverReading->name = $eachdata['value'];
                                } else if ($fields->system_name == 'phone') {
                                    $feverReading->phone = $eachdata['value'];
                                } else if ($fields->system_name == 'gender') {
                                    $option = ShiftModuleDropdownOption::where('id', '=', $eachdata['value'])->first();
                                    $feverReading->gender = isset($option->option_name) ? $option->option_name : '--';
                                } else if ($fields->system_name == 'age_group') {
                                    $option = ShiftModuleDropdownOption::where('id', '=', $eachdata['value'])->first();
                                    $feverReading->age_group = isset($option->option_name) ? $option->option_name : '--';
                                } else if ($fields->system_name == 'temperature') {
                                    $option = ShiftModuleDropdownOption::where('id', '=', $eachdata['value'])->first();
                                    $feverReading->temperature = isset($option->option_name) ? $option->option_name : '--';
                                    $temperature_id = AdminColorsetting::select('id')->where('fieldidentifier', 1)->where('rangebegin', '<=', $option->option_name)
                                        ->where('rangeend', '>=', $option->option_name)->first();
                                    $feverReading->temperature_id = isset($temperature_id->id) ? $temperature_id->id : 0;
                                } else if ($fields->system_name == 'notes') {
                                    $feverReading->notes = $eachdata['value'];
                                } else if ($fields->system_name == 'city') {
                                    $option = ShiftModuleDropdownOption::where('id', '=', $eachdata['value'])->first();
                                    //$feverReading->city = isset($option->option_name)? $option->option_name : '--';
                                } else if ($fields->system_name == 'province') {
                                    $option = ShiftModuleDropdownOption::where('id', '=', $eachdata['value'])->first();
                                    //$feverReading->province = isset($option->option_name)? $option->option_name : '--';
                                } else if ($fields->system_name == 'location') {
                                    $latitiude = $eachdata['value']['latitude'];
                                    $longitude = $eachdata['value']['longitude'];
                                    $postalcode = "J6K2Z9              ";
                                    $apiKey = config("globals.google_api_curl_key");
                                    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitiude . ',' . $longitude . '&key=' . $apiKey;
                                    //dd($url);
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    $responseJson = curl_exec($ch);
                                    curl_close($ch);
                                    //echo($responseJson);
                                    $response = json_decode($responseJson, true);
                                    //dd($response);
                                    $city = '';
                                    $city2 = '';
                                    $state = '';
                                    if (count($response['results']) != 0) {

                                        foreach ($response['results'][0]['address_components'] as $addressComponent) {
                                            if (in_array('locality', $addressComponent['types'])) {
                                                $city = $addressComponent['short_name'];
                                            }
                                            if (in_array('administrative_area_level_1', $addressComponent['types'])) {
                                                $state = $addressComponent['long_name'];
                                            }
                                            if (in_array('administrative_area_level_2', $addressComponent['types'])) {
                                                $city2 = $addressComponent['long_name'];
                                            }
                                        }
                                        if ($city == '') {
                                            $city = $city2;
                                        }
                                        $location = $city . ', ' . $state;
                                    }

                                    $feverReading->city = $city;
                                    $feverReading->province = $state;
                                    $feverReading->geo_location_lat = $eachdata['value']['latitude'];
                                    $feverReading->geo_location_long = $eachdata['value']['longitude'];
                                }
                            }
                        }
                    }
                    $response = $this->feverReadingRepository->store($feverReading);
                    \DB::commit();
                    if ($response) {
                        return true;
                    } else {
                        return false;
                    }
                } catch (\Exception $e) {
                    \DB::rollBack();
                    return false;
                }
            }
            //END:- Fever Scan module changes

            //BEGIN:- Post Order changes
            $post_order = $this->model->where('id', $request->moduleId)->where('post_order', 1)->count();
            if ($post_order) {
                try {
                    \DB::beginTransaction();
                    $postOrder = new ShiftModulePostOrder;
                    foreach ($request->fieldData as $eachreq) {
                        $postOrder->customer_id = $request->customerId;
                        $postOrder->module_id = $request->moduleId;
                        $postOrder->shift_id = isset($request->shiftId) ? $request->shiftId : 0;
                        $postOrder->shift_start_date = $request->shiftStartTime;
                        $postOrder->created_by = $userid;
                        if (sizeof($eachreq) > 0) {
                            foreach ($eachreq as $eachdata) {
                                $fields = ShiftModuleField::where('id', '=', $eachdata['fieldId'])->first();
                                if ($fields->field_type == 8) {
                                    $postOrder->field_id = $eachdata['fieldId'];
                                    $postOrder->dropdown_id = $eachdata['value'];
                                    $option = ShiftModuleDropdownOption::where('id', '=', $eachdata['value'])->first();
                                    $postOrder->field_value = isset($option->option_name) ? $option->option_name : '';
                                }
                            }
                        }
                        $postOrder->updated_by = $userid;
                        $postOrder->save();
                    }
                    \DB::commit();
                    if ($postOrder->id) {
                        //END:- Post Order changes
                        $this->updateShiftModulePostOrders($postOrder->id);
                        return true;
                    } else {
                        return false;
                    }
                } catch (\Exception $e) {
                    Log::info($e);
                    \DB::rollBack();
                    return false;
                }
            }
            //END:- Post Order changes
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.shift_module_attachment_folder'), $request->customer_id, $request->user_id);
    }

    /**
     * Static function to return path as an array when file name is given
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = ShiftModuleEntryAttachment::with('shift_module_enrty')->where('attachment_id', $file_id)->first();
        if (isset($attachment)) {
            $customer_id = $attachment->shift_module_enrty->customer_id;
            $user_id = $attachment->shift_module_enrty->created_by;
        }
        return array(config('globals.shift_module_attachment_folder'), $customer_id, $user_id);
    }

    /**
     * Static function to return path as an array when file name is given
     * @param $file_id
     * @return array
     */
    public function storeShiftModule($request)
    {
        $id = $request->get('id');
        if (in_array(8, $request->get('field_type'))) {
            $post_order = 1;
        } else {
            $post_order = 0;
        }

        $obj_module = ShiftModule::updateOrCreate(
            array('id' => $request->get('id')),
            [
                'customer_id' => $request->get('customer_id'),
                'module_name' => $request->get('module_name'),
                'enable_timeshift' => $request->get('enable_timeshift'),
                //            'dashboard_view' => $request->get('dashboard_view'),
                'dashboard_view' => 1,
                'is_active' => $request->get('module_status'),
                'post_order' => $post_order,
            ]
        );
        $module_id = $obj_module->id;
        $arr_module_pos = $request->get('position');
        if ($request->get('module_exists') == 0) {
            ShiftModuleField::where(['module_id' => $module_id])->delete();
            foreach ($arr_module_pos as $key => $module_pos) {
                if ($request->get('field_dropdown_' . $key) !== null) {
                    $dropdownid = $request->get('field_dropdown_' . $key);
                } else if ($request->get('field_dropdown_info_' . $key) !== null) {
                    $dropdownid = $request->get('field_dropdown_info_' . $key);
                } else if ($request->get('field_post_order_' . $key) !== null) {
                    $dropdownid = $request->get('field_post_order_' . $key);
                } else {
                    $dropdownid = 0;
                }
                $templateFrom = ShiftModuleField::create(
                    [
                        'module_id' => $module_id,
                        'field_name' => $request->get('field_name')[$key],
                        'field_type' => $request->get('field_type')[$key],
                        'field_status' => ($request->get('field_status')[$key]) != null ? ($request->get('field_status')[$key]) : 0,
                        //'dropdown_id' => (null !== ($request->get('field_dropdown_' . $key))) ? ($request->get('field_dropdown_' . $key)) : 0,
                        'dropdown_id' => $dropdownid,
                        'is_multiple_photo' => (null !== ($request->get('multiple_photo_' . $key))) ? 1 : 0,
                        'order_id' => $request->get('field_order')[$key],
                    ]
                );
            }
        } else {
            foreach ($arr_module_pos as $key => $module_pos) {
                if ($request->get('field_dropdown_' . $key) !== null) {
                    $dropdownid = $request->get('field_dropdown_' . $key);
                } else if ($request->get('field_dropdown_info_' . $key) !== null) {
                    $dropdownid = $request->get('field_dropdown_info_' . $key);
                } else if ($request->get('field_post_order_' . $key) !== null) {
                    $dropdownid = $request->get('field_post_order_' . $key);
                } else {
                    $dropdownid = 0;
                }
                $templateFrom = ShiftModuleField::updateOrCreate(
                    [
                        'module_id' => $module_id,
                        'id' => $request->get('shiftmodulefield_id')[$key],
                    ],
                    [
                        'field_name' => $request->get('field_name')[$key],
                        'field_type' => $request->get('field_type')[$key],
                        'field_status' => ($request->get('field_status')[$key]) != null ? ($request->get('field_status')[$key]) : 0,
                        //'dropdown_id' => (null !== ($request->get('field_dropdown_' . $key))) ? ($request->get('field_dropdown_' . $key)) : 0,
                        'dropdown_id' => $dropdownid,
                        'is_multiple_photo' => (null !== ($request->get('multiple_photo_' . $key))) ? 1 : 0,
                        'order_id' => $request->get('field_order')[$key],
                    ]
                );
            }
        }

        return $obj_module;
    }
    /**
     * To get customer module details
     *
     * @param empty
     * @return array
     */
    public function getAllCustomerModule($customer_id)
    {
        return $modules = $this->model->where('customer_id', '=', $customer_id)->where('is_active', 1)->get();
    }

    /**
     * To add Shift Start rows in Shift module Entries
     *
     * @param [type] $customer_id
     * @param [type] $start_time
     * @param [type] $shift_id
     * @return void
     */

    public function shiftStartModule($customer_id, $start_time, $shift_id)
    {

        $modules = $this->getAllCustomerModule($customer_id);
        if (!empty($modules)) {
            foreach ($modules as $eachmodule) {
                $shiftModuleEntry['customer_id'] = $customer_id;
                $shiftModuleEntry['module_id'] = $eachmodule->id;
                $shiftModuleEntry['shift_id'] = $shift_id;
                $shiftModuleEntry['shift_start_date'] = $start_time;
                $shiftModuleEntry['field_id'] = 0;
                $shiftModuleEntry['field_value'] = 'Start';
                $shiftModuleEntry['created_by'] = \Auth::user()->id;
                $this->shiftModuleEntrymodel->create($shiftModuleEntry);
            }
        }
        return true;
    }

    public function updateShiftModulePostOrders($id)
    {
        $post_order = ShiftModulePostOrder::where('id', $id)->first();
        if (!empty($post_order)) {
            $all_post_orders = ShiftModulePostOrder::where('customer_id', $post_order->customer_id)
                ->whereDate('shift_start_date', \Carbon::parse($post_order->shift_start_date)->format("Y-m-d"))
                ->where('module_id', $post_order->module_id)
                ->where('created_by', $post_order->created_by)
                ->orderBy('created_at', 'asc')->get();
            $total_rows = count($all_post_orders);
            if ($total_rows > 0) {
                $min_time = \Carbon::parse($all_post_orders[0]->shift_start_date);
                $total_duration = $min_time->diffInSeconds(\Carbon::parse($all_post_orders[$total_rows - 1]->created_at));
                if ($total_rows == 1) {
                    $shift_start = \Carbon::parse($all_post_orders[0]->shift_start_date);
                    $duration = $shift_start->diffInSeconds(\Carbon::parse($all_post_orders[0]->created_at));
                    ShiftModulePostOrder::where('id', $id)->update(['duration' => $duration / 60, 'percentage' => 100]);
                } else {
                    foreach ($all_post_orders as $key => $each_post_order) {
                        if ($key != 0) {
                            $prev_post_order = ShiftModulePostOrder::where('customer_id', $each_post_order->customer_id)
                                ->whereDate('shift_start_date', \Carbon::parse($each_post_order->shift_start_date)->format("Y-m-d"))
                                ->where('module_id', $each_post_order->module_id)
                                ->where('created_by', $each_post_order->created_by)
                                ->where('created_at', '<=', $each_post_order->created_at)
                                ->where('id', '!=', $each_post_order->id)
                                ->orderBy('created_at', 'desc')->first();

                            $prev_created_at = \Carbon::parse($prev_post_order->created_at);
                            $duration_in_sec = $prev_created_at->diffInSeconds(\Carbon::parse($each_post_order->created_at));
                            $post_order_duration = $duration_in_sec / $total_duration;
                            $percentage = $post_order_duration * 100;
                            ShiftModulePostOrder::where('id', $each_post_order->id)->update(['duration' => $duration_in_sec / 60, 'percentage' => round($percentage, 2)]);
                        } else {
                            $shift_start = \Carbon::parse($each_post_order->shift_start_date);
                            $duration_in_sec = $shift_start->diffInSeconds(\Carbon::parse($each_post_order->created_at));
                            $post_order_duration = $duration_in_sec / $total_duration;
                            $percentage = $post_order_duration * 100;
                            ShiftModulePostOrder::where('id', $each_post_order->id)->update(['percentage' => round($percentage, 2)]);
                        }
                    }
                }
            }
        }
    }

    public function getShiftModulePostOrderDetails($customer_id)
    {
        $module = $this->model->where('customer_id', $customer_id)->where('post_order', 1)->where('is_active', 1)->first();
        if (!empty($module)) {
            $module_field = ShiftModuleField::where('module_id', $module->id)->where('field_type', 8)->where('field_status', 1)->first();
            if (!empty($module_field)) {
                $post_orders = ShiftModuleDropdownOption::where('shift_module_dropdown_id', $module_field->dropdown_id)->pluck('option_name', 'id');
            } else {
                return null;
            }
        } else {
            return null;
        }
        return $post_orders;
    }

    public function getShiftJournalSummary($request)
    {
        $details = [];
        $current = Carbon::now();
        $customers = $request->get("customer-id");
        $createdBy = ($request->has("employee-id") && !empty($request->get("employee-id"))) ? $request->get("employee-id") : null;
        $days = [];
        $days[0] = $current->format('Y-m-d');
        for ($i = 1; $i < 15; $i++) {
            $days[] = $current->addDays(-1)->format('Y-m-d');
        }
        $shift_module = $this->getShiftModulePostOrderDetails($customers);
        $result = [];
        $total_users = 1;
        $result = array_map(function ($days) {
            return Carbon::parse($days)->format('d-M-y');
        }, $days);
        $days_flip = array_flip($days);
        $details['days'] = $result;
        $details['title'] = $shift_module;

        if (!empty($shift_module)) {
            $module = $this->model->where('customer_id', $customers)->where('post_order', 1)->where('is_active', 1)->first();

            $postOrderQry = ShiftModulePostOrder::select(DB::raw('SUM(percentage) as total_percentage,dropdown_id, DATE(shift_start_date) as date'))
                ->where('customer_id', $customers)
                ->whereBetween('shift_start_date', [$days[14], $current->addDays(+15)->format('Y-m-d')])
                ->groupBy('dropdown_id', 'date');

            if (!empty($createdBy)) {
                $postOrderQry = $postOrderQry->where('created_by', $createdBy);
            }

            $all_post_orders = $postOrderQry->get();

            if (!empty($all_post_orders)) {
                foreach ($all_post_orders as $value) {
                    $total_user_query = ShiftModulePostOrder::select(DB::raw('COUNT(DISTINCT(created_by)) as users_count'))
                        ->where('customer_id', $customers)
                        ->whereDate('shift_start_date', $value->date)
                        ->where('module_id', $module->id);

                    if (!empty($createdBy)) {
                        $total_user_query = $total_user_query->where('created_by', $createdBy);
                    }

                    $total_users = $total_user_query->get();

                    if ($total_users[0]->users_count > 1) {
                        $details['orders'][$days_flip[$value->date]][$value->dropdown_id] = round($value->total_percentage / $total_users[0]->users_count, 1, PHP_ROUND_HALF_UP);
                    } else {
                        $details['orders'][$days_flip[$value->date]][$value->dropdown_id] = round($value->total_percentage, 1, PHP_ROUND_HALF_UP);
                    }
                }
            } else {
                $details['orders'] = null;
            }

        } else {
            $details['orders'] = null;
            return $details;
        }
        return $details;
    }
}
