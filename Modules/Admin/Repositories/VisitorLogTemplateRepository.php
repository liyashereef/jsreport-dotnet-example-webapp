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
}
