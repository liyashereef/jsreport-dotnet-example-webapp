<?php

namespace App\Repositories;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Repositories\ShiftModuleRepository;
use Modules\Documents\Repositories\DocumentsRepository;
use Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository;
use Modules\Hranalytics\Repositories\CandidateRepository;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Expense\Repositories\ExpenseClaimRepository;
use Modules\Contracts\Repositories\PostOrderRepository;
use Modules\Contracts\Repositories\RfpCatalogueRepository;
use Modules\Vehicle\Repositories\VehicleTripRepository;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;
use Modules\Vehicle\Repositories\VehicleMaintenanceRepository;
use Modules\KeyManagement\Repositories\CustomerKeyDetailRepository;
use Modules\KeyManagement\Repositories\IdentificationAttachmentRepository;
use Modules\KeyManagement\Repositories\KeyLogRepository;
use Modules\Recruitment\Repositories\RecCandidateRepository;

class AttachmentRepository
{

    private $directory_seperator;
    private $extension_seperator;

    public function __construct()
    {
        $this->directory_seperator = "/";
        $this->extension_seperator = ".";
    }

    /**
     * [getModuleUploadProperties description]
     * @param  [type] $module          [Module name]
     * @param  [type] $request         [Request]
     * @param  [string] $given_file_name File name if has to be specified seperatly in case of file upload array
     * @return [array]                  [Array with file path, name and persistant]
     */
    private function getModuleUploadProperties($module, $request, $given_file_name = null)
    {

        $file_path = $module . '_attachment';
        $persistant = PERM_FILE;
        $file_name = 'file';
        switch ($module) {
            case 'incident':
                $file_path = implode($this->directory_seperator, IncidentReportRepository::getAttachmentPathArr($request));
                $persistant = TEMP_FILE;
                $file_name = 'file';
                if ($given_file_name != null) {
                    $file_name = $given_file_name;
                }
                break;
            case 'candidate-transition':
                $file_path = implode($this->directory_seperator, CandidateRepository::getAttachmentPathArr($request));
                $file_name = 'file';
                break;
            case 'employeeTimeOff':
                $file_path = implode($this->directory_seperator, EmployeeTimeOffRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'time_off_attachment';
                break;
            case 'documents':
                $file_path = implode($this->directory_seperator, DocumentsRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'document_attachment';
                break;
            case 'contracts':
                $file_path = implode($this->directory_seperator, ContractsRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'contracts_attachment';
                break;

            case 'shift-module':
                $file_path = implode($this->directory_seperator, ShiftModuleRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
            case 'expense-send-statements':
                $file_path = implode($this->directory_seperator, ExpenseClaimRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'expense_send_statements';
                break;
            case 'post-order':
                $persistant = TEMP_FILE;
                $file_path = implode($this->directory_seperator, PostOrderRepository::getAttachmentPathArr($request));
                $file_name = 'file';
                break;
            case 'rfp-catalogue':
                $persistant = TEMP_FILE;
                $file_path = implode($this->directory_seperator, RfpCatalogueRepository::getAttachmentPathArr($request));
                $file_name = 'file';
                break;
            case 'vehicle-module':
                $file_path = implode($this->directory_seperator, VehicleTripRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
            case 'qr-patrol':
                $file_path = implode($this->directory_seperator, EmployeeShiftRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
            case 'vehicle-maintenance':
                $file_path = implode($this->directory_seperator, VehicleMaintenanceRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
            case 'keymanagement-key-image':
                $file_path = implode($this->directory_seperator, CustomerKeyDetailRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
            case 'keymanagement-identification':
                $file_path = implode($this->directory_seperator, IdentificationAttachmentRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
            case 'keymanagement-signature':
                $file_path = implode($this->directory_seperator, KeyLogRepository::getAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
            case 'candidate-recruitment':
                $file_path = implode($this->directory_seperator, CandidateRepository::getRecruitmentAttachmentPathArr($request));
                $file_name = ($given_file_name) ?? 'file';
                break;
        }
        return array('file_path' => $file_path, 'persistant' => $persistant, 'file_name' => $file_name);
    }

    private function getModuleDownloadProperties($module, $file_id)
    {

        $file_path = $module . '_attachment';
        $file_name = $this->getFileName($file_id);
        $local_name = $this->getLocalFileName($file_id);
        $file_ext = $this->getFileExtension($file_id);
         
        switch ($module) {
            case 'incident':
                $file_path = implode($this->directory_seperator, IncidentReportRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'employeeTimeOff':
                $file_path = implode($this->directory_seperator, EmployeeTimeoffRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'documents':
                $file_path = implode($this->directory_seperator, DocumentsRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'shift-module':
                $file_path = implode($this->directory_seperator, ShiftModuleRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'contracts':
                $file_path = implode($this->directory_seperator, ContractsRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'expense-send-statements':
                $file_path = implode($this->directory_seperator, ExpenseClaimRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'post-order':
                $file_path = implode($this->directory_seperator, PostOrderRepository::getAttachmentPathArr($file_id));
                break;
            case 'rfp-catalogue':
                $file_path = implode($this->directory_seperator, RfpCatalogueRepository::getAttachmentPathArr($file_id));
                break;
            case 'vehicle-module':
                $file_path = implode($this->directory_seperator, VehicleTripRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'qr-patrol':
                $file_path = implode($this->directory_seperator, EmployeeShiftRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'vehicle-maintenance':
                $file_path = implode($this->directory_seperator, VehicleMaintenanceRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'keymanagement-key-image':
                $file_path = implode($this->directory_seperator, CustomerKeyDetailRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'keymanagement-identification':
                $file_path = implode($this->directory_seperator, IdentificationAttachmentRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'keymanagement-signature':
                $file_path = implode($this->directory_seperator, KeyLogRepository::getAttachmentPathArrFromFile($file_id));
                break;
            case 'candidate-transition':
                $file_path = implode($this->directory_seperator, DocumentsRepository::getTransitionAttachmentPathArrFromFile($file_id));
                break;
            case 'rec_candidate_application':
                $file_path = implode($this->directory_seperator, RecCandidateRepository::getRecAttachmentPathArrFromFile($file_id));
                break;
        }
        return array('file_path' => $file_path, 'local_name' => $local_name, 'file_name' => $file_name, 'file_ext' => $file_ext);
    }

    public function storeAttachment($hash_name, $assumed_ext, $original_name, $original_ext, $persistant, $module)
    {
        $file_attachment = new Attachment;
        $file_attachment->hash_name = $hash_name;
        $file_attachment->assumed_ext = $assumed_ext;
        $file_attachment->original_name = $original_name;
        $file_attachment->original_ext = $original_ext;
        $file_attachment->persistent = $persistant;
        $file_attachment->file_module = $module;
        $result = $file_attachment->save();
        $id = false;
        if ($result) {
            $id = $file_attachment->id;
        }
        return $id;
    }

    /*
    private function getFileName(){
    if
     *  assumed extention
    }
     */

    public function saveAttachmentFile($module, $request, $file_name = null, $custom_name = null)
    {

        try {
            $module_properties = $this->getModuleUploadProperties($module, $request, $file_name);

            $file_name = $module_properties['file_name'];
            $file_obj = $request->file($file_name);
            $hash_name = $file_obj->hashName();
            $guessed_ext = $file_obj->guessExtension();
            $original_ext = $file_obj->getClientOriginalExtension();
            if ($custom_name!=null) {
                $custom_name=base64_decode($custom_name);
                if ((strpos($custom_name, ".") !== false)) {
                    $original_name =   $custom_name;
                } else {
                    $original_name =   $custom_name. $this->extension_seperator . $original_ext;
                }
            } else {
                $original_name = $file_obj->getClientOriginalName();
            }
            $file_path = $module_properties['file_path'];

            $persistant = $module_properties['persistant'];
            //$file_name = pathinfo($file_obj->getClientOriginalName())['filename'] . date("U") . $this->extension_seperator . $file_obj->getClientOriginalExtension();
            if ($guessed_ext == null) {
                $file_name = $hash_name . $this->extension_seperator . $original_ext;
            } else {
                $file_name = $hash_name;
            }

            $saved_path = Storage::putFileAs($file_path, $file_obj, $file_name);
            $db_result = $this->storeAttachment($hash_name, $guessed_ext, $original_name, $original_ext, $persistant, $module);
            if (!$db_result) {
                throw new Exception("Save Failed");
            }
            //$request->file('file_attachment')->store($file_path);
            return array('file_id' => $db_result);
        } catch (Exception $ex) {
            return false;
        }

        //$request->file('file_attachment')->storeAs($file_path, $file_name);
        //return $file_name;
    }



    public function saveBase64ImageFile($module, $request, $image)
    {
        try {
            $filename = uniqid($module.'_img_');
            $module_properties = $this->getModuleUploadProperties($module, $request, $filename);
            $file_path = $module_properties['file_path'];
            $hash_name = '';
            $guessed_ext = '';
            $original_ext = $image['extension'];
            $persistant = 1;
            $original_name = $filename.'.'.$image['extension'];

            $path = storage_path('app/') . $file_path . $this->directory_seperator .  $original_name;
            if (!file_exists(storage_path('app/') . $file_path)) {
                mkdir(storage_path('app/') . $file_path, 0755, true);
            }
            $entry = file_put_contents($path, $image['image']);
            $db_result = $this->storeAttachment($hash_name, $guessed_ext, $original_name, $original_ext, $persistant, $module);
            if (!$db_result) {
                throw new Exception("Save Failed");
            }

            return $db_result;
        } catch (Exception $ex) {
            return false;
        }
    }





    public function downloadDetails($request, $file_id = null, $module_id = null)
    {
        if (isset($request)) {
            $module_id = $request->module;
            $file_id = $request->file_id;
        } elseif (isset($module_id) && isset($file_id)) {
            $module_id = $module_id;
            $file_id = $file_id;
        }
        $file_download_prop = $this->getModuleDownloadProperties($module_id, $file_id);
        $name = $file_download_prop['file_name'];
        $local_name = $file_download_prop['local_name'];
        $ext = $file_download_prop['file_ext'];
        $relative_path = $file_download_prop['file_path'] . $this->directory_seperator . $local_name;
        $path = storage_path('app') . $this->directory_seperator . $file_download_prop['file_path'] . $this->directory_seperator . $local_name;
        return compact('path', 'name', 'relative_path', 'ext');
    }

    public function getFileName($file_id)
    {
        $original_name = Attachment::select('original_name')->find($file_id);
        return $original_name->original_name;
    }

    public function getLocalFileName($file_id)
    {
        $local_name = Attachment::select('hash_name', 'assumed_ext', 'original_ext', 'original_name')->find($file_id);
        if ($local_name->hash_name != null) {
            if ($local_name->assumed_ext == null) {
                $file_name = $local_name->hash_name . $this->extension_seperator . $local_name->original_ext;
            } else {
                $file_name = $local_name->hash_name;
            }
        } else {
            $file_name = $local_name->original_name;
        }
        return $file_name;
    }

    public function getFileExtension($file_id)
    {
        return Attachment::where('id', $file_id)->pluck('original_ext')->first();
    }

    public function setFilePersistant($file_id)
    {
        Attachment::where('id', $file_id)->update(['persistent' => true]);
    }

    public function unsetFilePersistant($file_id)
    {
        Attachment::where('id', $file_id)->update(['persistent' => false]);
    }

    public function removeTempFile($module = null, $file_id = null)
    {
        $remove_temp_att = Attachment::select('id', 'file_module')->where('persistent', TEMP_FILE);
        if (isset($module)) {
            $remove_temp_att->where('file_module', $module);
        }
        if (isset($file_id)) {
            $remove_temp_att->where('id', $file_id);
        }
        $files_to_remove = $remove_temp_att->get();
        foreach ($files_to_remove as $each_file) {
            $file_details = $this->downloadDetails(null, $each_file->id, $each_file->file_module);
            $file_delete = Storage::delete($file_details['relative_path']);
            if ($file_delete) {
                $remove_attachment = Attachment::where('file_module', $each_file->file_module)->where('id', $each_file->id)->delete();
            }
        }
    }

    public function removeFile($file_id, $module)
    {
        $file_details = downloadDetails(null, $file_id, $module);
        $file_delete = Storage::delete($file_details['relative_path']);
        if ($file_delete) {
            $remove_attachment = Attachment::where('file_module', $module)->where('id', $file_id)->delete();
        }
    }
}
