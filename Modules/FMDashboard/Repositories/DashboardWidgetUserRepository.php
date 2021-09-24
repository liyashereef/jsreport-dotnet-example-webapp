<?php

namespace Modules\FMDashboard\Repositories;

use Illuminate\Support\Facades\Auth;
use Modules\FMDashboard\Models\FmDashboardWidgetUser;

class DashboardWidgetUserRepository
{

    protected $model;
  
    public function __construct(FmDashboardWidgetUser $model)
    {
        $this->model = $model;
    }

    public function getAllByCurrentUser()
    {
        $user = Auth::user();
        return $this->model->where('user_id','=',$user->id)
        ->with(['widget','user']);
    }

    public function getAllWidgetIdsOfCurrentUser(){
        $widgetIds= [];
        $userWidgets = $this->getAllByCurrentUser()->get(); 
        
        foreach($userWidgets as $userWidget){
            array_push($widgetIds,$userWidget->widget->id);
        }
        return $widgetIds;
    }

    public function syncSections($widgets)
    {
        //delete all widgets
        $this->getAllByCurrentUser()->delete();
        $user = Auth::user();
        
        if(!is_array($widgets)){
            return;
        }
        foreach($widgets as $widget){
            $this->model->create([
                'user_id' => $user->id,
                'fm_dashboard_widget_id' => $widget
            ]);
        }
    }
    
}
