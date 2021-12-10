<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\VisitorLogTemplates;
use Modules\Admin\Models\VisitorLogTemplateFields;
use Modules\Admin\Models\VisitorLogTemplateFeature;

class VisitorLogTemplateRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $visitorLogTemplatefields, $visitorLogTemplateFeature;

    /**
     *
     * @param  \App\Models\VisitorLogTemplates $visitorLogTemplates
     */
    public function __construct(VisitorLogTemplates $visitorLogTemplates, VisitorLogTemplateFields $visitorLogTemplatefields, VisitorLogTemplateFeature $visitorLogTemplateFeature)
    {
        $this->model = $visitorLogTemplates;
        $this->visitorLogTemplatefields = $visitorLogTemplatefields;
        $this->visitorLogTemplateFeature = $visitorLogTemplateFeature;
    }

    /**
     * Get allocation user list
     * @param $customer_id
     *@param Array $role,$role_except(Is role to be excluded=false)
     */

    public function allocationTemplateList($customer_id)
    {
        $get_employees = $this->model
            ->with('visitorLogTemplate.customer');
        // ->whereHas('roles', function ($query) use ($role, $role_except) {
        //     $query->whereNotIn('roles.name', ['super_admin', 'admin']);
        //     if ($role != null) {
        //         ($role_except === false) ? $query->whereIn('roles.name', $role) : $query->whereNotIn('roles.name', $role);
        //     }
        // });

        if ($customer_id != null) {
            $get_employees->whereHas('visitorLogTemplate', function ($query) use ($customer_id) {
                if (is_array($customer_id)) {
                    $query->whereIn('customer_id', $customer_id);
                } else {
                    $query->where('customer_id', $customer_id);
                }
            });
        }
        // dd($get_employees->get());
        return $get_employees->get();
    }

    public function getBasicTemplateFields()
    {
        $basic_fields = $this->visitorLogTemplatefields->where('is_custom', 0)->where('template_id', 0)->get();
        return $basic_fields;
    }

    public function getTemplateFeatures()
    {
        $features = $this->visitorLogTemplateFeature->where('template_id', 0)->get();
        return $features;
    }

    public function getCustomerTemplateFields($template_id)
    {
        if ($template_id != 0 && $template_id != '') {
            $template_fields = $this->visitorLogTemplatefields->where('is_custom', 0)->where('template_id', $template_id)->get();
            return $template_fields;
        } else {
            return false;
        }
    }

    public function getTemplateCustomFields($template_id)
    {
        if ($template_id != 0 && $template_id != '') {
            $template_fields = $this->visitorLogTemplatefields->where('is_custom', 1)->where('template_id', $template_id)->get();
            return $template_fields;
        } else {
            return false;
        }
    }

    public function getCustomerTemplateFeatures($template_id)
    {
        if ($template_id != 0 && $template_id != '') {
            $template_features = $this->visitorLogTemplateFeature->where('template_id', $template_id)->get();
            return $template_features;
        } else {
            return false;
        }
    }

    public function fetchTemplateDetails($templateId)
    {

        $templates = $this->model->find($templateId);
        $templatelistarray = [];
        $templatelistarray["id"] = $templates->id;
        $templatelistarray["name"] = $templates->template_name;

        foreach ($templates->template_feature as $temp_feature) {

            // For Mandatory validation
            $reqval = false;
            if ($temp_feature->is_required == 1) {
                $reqval = true;
            }

            // For Mandatory validation
            $is_visible = false;
            if ($temp_feature->is_visible    == 1) {
                $is_visible = true;
            }

            if ($temp_feature->feature_name == "picture") {
                $templatelistarray["reqImageCapture"] = $reqval;
                $templatelistarray["enImageCapture"] = $is_visible;
            }
            if ($temp_feature->feature_name == "signature") {
                $templatelistarray["reqSignature"] = $reqval;
                $templatelistarray["enSignature"] = $is_visible;
            }
        }

        $fieldarray = [];
        $j = 0;
        foreach ($templates->visible_template_fields as $temp_fields) {

            // Validation
            $reqval = false;
            if ($temp_fields->is_required == 1) {
                $reqval = true;
            }
            $templatelistarray["fields"][$j]["name"] = $temp_fields->fieldname;
            if ($temp_fields->fieldname == "first_name") {
                $field_type = "text";
            } else if ($temp_fields->fieldname == "email") {
                $field_type = "email";
            } else if ($temp_fields->fieldname == "phone") {
                $field_type = "phone";
            } else if ($temp_fields->fieldname == "visitor_type_id") {
                $field_type = "radio";
            } else if ($temp_fields->fieldname == "checkin") {
                $field_type = "time";
            } else {
                $field_type = "text";
            }
            $templatelistarray["fields"][$j]["type"] = $field_type;
            $templatelistarray["fields"][$j]["label"] = $temp_fields->field_displayname;
            $templatelistarray["fields"][$j]["mandatory"] = $reqval;
            $templatelistarray["fields"][$j]["pattern"] = "";
            $j++;
        }

        return  $templatelistarray;
    }

}
