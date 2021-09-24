<?php

namespace Modules\Supervisorpanel\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\TemplateQuestionsCategory;
use Modules\Admin\Models\Template;
use Modules\Admin\Models\TemplateForm;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;


class OperationalDashboardController extends Controller
{


    public function __construct(CustomerReportRepository $customerReportRepository)
    {
       $this->customer_report_repository = $customerReportRepository;
    }
    /**
     * Show operational dashboard
     * 
     */
    public function index()
    {
        $template_categories = $this->customer_report_repository->getCurrentTemplateCategories();
        return view('supervisorpanel::operational-dashboard',compact('template_categories'));
    }


  
    /**
     * Get parent answers of a template category
     * 
     *  @param integer template_category_id
     *  @return json array
     */
    public function getTemplateCategoryParentQuestionAnswers($template_category_id)
    {
        try{
            $current_template = $this->customer_report_repository->getCurrentTemplateParentQuestionsAnswers($template_category_id);
            return response()->json(['success' => true,'message' => 'success','data' => $current_template]);
                        
        }
        catch(\Exception $e){
            return response()->json(['success' => false,'message' => $e->getMessage(),'line' => $e->getLine()]);
        }
    }

}
