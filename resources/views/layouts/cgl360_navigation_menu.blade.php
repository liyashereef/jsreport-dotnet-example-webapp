<style>
    .dropdown-submenu {
        position: relative;
    }
    .activeclass{
        display: block;
    }
    .inactiveclass{
        display: block;
    }
    .accordclass{
        display: none;
        max-width: 230px !important;
        padding-left: 0px !important
    }
    .dropdown-submenu .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -165px;
        margin-left: -27px;
    }

    .dropdown-position {
        /*position: relative !important;*/
        left: 77% !important;
        border-bottom: orange 1px solid;
        border-right: solid orange 1px;
        top: -10px;
        width: 110%;
    }
    .plihidden{
        display: none;
        font-weight: bold;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #f26321;
    }

    .pli{
        /* padding: 9px 37px 9px 16px; */
        font-size: 16px;
        font-weight: bold;
        
    }
    #sidebar{
        font-family: 'Montserrat' !important;
    }

    #sidebar ul.components{
        background: none !important
    }
    ::-webkit-scrollbar-thumb {
    -webkit-border-radius: 10px;
    border-radius: 10px;
    /* background: #f26321; */
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
}

::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}
::-webkit-scrollbar-thumb:window-inactive {
    /*background: #f26321; */
}
    .singlelinespan{
        /* margin-top:-20px !important ;
        margin-left:20px !important ; */
        display: inline !important;
        white-space: normal;
        padding-left: 0px !important;
        vertical-align: middle;

    }
    .multilinespan{
        /* margin-top:-20px !important ;
        margin-left:20px !important ; */
        display: inline !important;
        white-space: normal;
        padding-left: 0px !important
    }
    
    .iconclass{
        float: left;
        width: 47px !important;
        padding: 15px;
        padding-top: 4px;
        height: 37px !important;
        vertical-align: top !important;
    }
    
    .transform-class{
        transform: translate3d(5px, -151px, 0px) !important;

    }
    #sidebar ul li a{
        padding-left: 9px !important;
        padding-right: 8px !important;
        padding-top: 9px !important;
        padding-bottom: 7px !important
    }
    .mainhead{
       
        height: 42px !important;

    }
    #sidebar ul li a i{
        margin-right:2px !important;
    }


   
</style>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous">
        <link href="{{ asset('css/sidebar.css') }}?rev={{config('globals.resource_cache_rev','1')}}" rel="stylesheet">
        {{-- <link href="{{ asset('css/style.css') }}?rev={{config('globals.resource_cache_rev','1')}}" rel="stylesheet"> --}}

@include('layouts.partials.sidebar_dynamic_script')
<script>
    var block=0;
    $(document).on("click",".pli",function(e){
        e.preventDefault();
        $(".accordclass").slideUp();
        let accordBlock=$(this).attr("attr-block");
        if(accordBlock>0){
            if($(".block"+accordBlock).is(":visible")){
                $(".block"+accordBlock).slideUp();
            }else{
                $(".block"+accordBlock).slideDown();
            }
        }
    })
    $(document).ready(function() {
        $('.dropdown-submenu a.test').on("click", function(e) {
            if($(this).hasClass("secondmenu")){
                $('.dropdown-submenu .second-menu').hide();
                $(this).next('ul').toggle(); 
            }else{
              $('.dropdown-submenu .dropdown-menu').hide();
                $(this).next('ul').toggle();  
            }
            
            e.stopPropagation();
            e.preventDefault();
        });

        $(".secondmenu").on("click",function(e){
            e.preventDefault()
        })
        $('#sidebarCollapse').on('click', function() {
            let stat= $('#sidebar').toggleClass('active');
            if(stat){
                $("#sidebar").removeClass("dyn-sidebar")
                $(".accordclass").hide();
                removeDyns();
            }else{
                
            }
            $('#sidebar').find('.dropdown-menu').toggleClass('resp');
            $('.fa-caret-down').toggleClass('carat');

            if($("#sidebar").hasClass("active")){
                $(".plihidden").show();
                
            }else{
                $(".plihidden").hide();  
              
            }
        });
        @if(!in_array(\Request::route()->getName(), [null, 'home'])) $('#sidebarCollapse').trigger('click');
        @endif
    });
    $(document).on("click",".mhead",function(e){
        e.preventDefault();
        if($("#sidebar").hasClass("active")){
            $("#sidebarCollapse").trigger("click");
            let accordBlock=$(this).attr("attr-block");
            block=accordBlock;
            setTimeout(() => {
                if(accordBlock>0){
                if($(".block"+accordBlock).is(":visible")){
                    // $(".block"+accordBlock).slideUp();
                }else{
                    $(".block"+accordBlock).slideDown();
                }
            }
            }, 1000);
        }else{  
            
        }
    })

    $(document).on("click",".plihidden",function(e){
        let accordBlock=$(this).attr("attr-block");
        block=accordBlock;
  

        if(accordBlock>0){
            if($(".block"+accordBlock).is(":visible")){
                $(".block"+accordBlock).slideUp();
            }else{
                $(".block"+accordBlock).slideDown();
            }
        }
        
    })

</script>
<nav id="sidebar">
    <ul class="list-unstyled components">
        <li>
            <a id="sidebarCollapse" class="sidebarCollapseEl">
                <img class ="sidebarToggleImg" src="{{ asset('images/handburger.png') }}">
            </a>
        </li>
        @canany(['add_client_document','view_client_document','add_allocated_client_document','view_allocated_client_document','add_employee_document','view_employee_document','add_allocated_employee_document','view_allocated_employee_document','add_other_document','view_other_document',
        'view_contracts',
        'view_all_expense_claim','view_allocated_expense_claim','view_all_mileage_claim','view_allocated_mileage_claim','expense_send_statements',
        'initiate_vehicle','edit_initiated_vehicle','view_completed_maintenance','view_vehicle_cumilative_km','view_pending_maintenance',
        'uniform_view_all_appointment','view_ura_transactions','view_uniform_orders',
        'user_view','customer_view','view_covid_daily_transaction_report',
'view_covid_compliance_report','view_sitenote_reports','view_customersurvey_reports',
'view_visitor_log_report','view_all_site_document_report','view_allocated_site_document_report',
'view_recruiting_analytics_report','view_termination_report',
'view_osgc_registered_users',
'create_task_all_customer','create_task_allocated_customer','view_all_reports','view_allocated_customer_reports','view_assigned_reports','view_all_performance_reports','view_allocated_performance_reports',
'view_admin'])
        @include('layouts.partials.cgl360_menu_block1')
        @endcanany
        @canany(['view_cglmeet','view_sensors'])
        @include('layouts.partials.cgl360_menu_block2')
        @endcanany
        @canany(['view_supervisorpanel','view_hranalytics','view_timetracker','view_clientscheduling',
        'view_contracts','view_post_order','view_client','view_keymanagement','ids_view_all_schedule','ids_view_allocated_locaion_schedule',
        'ids_refund_list','ids_refund_update_status','ids_view_report','view_all_customer_facility','view_allocated_customer_facility',
        'manage_all_facility_users','manage_allocated_facility_users',
        'manage_user_allocation','view_facilityscheduleview','view_video_post_summary'])
        @include('layouts.partials.cgl360_menu_block3')
        @endcanany
        @canany(['rec-candidate-credential','rec-create-candidate-credential','rec-edit-candidate-credential','rec-delete-candidate-credential',
        'rec-candidate-screening-summary','rec-view-allocated-candidates-summary',
        'rec-candidate-tracking-summary','rec-view-allocated-candidates-tracking',
        'rec-candidate-selection','rec-candidate-uniform-shipment','rec_candidate_transition_process',
        'rec-view-candidate-training','rec-create-job', 'rec-edit-job', 'rec-archive-job', 'rec-job-approval', 'rec-hr-tracking',
                 'rec-job-attachement-settings', 'rec-list-jobs-from-all', 'rec-job-tracking-summary','rec-view-allocated-job-requisitions',
                 'rec-view-allocated-candidates-geomapping','view_compliance','view_training',
                 'create-job', 'edit-job', 'delete-job', 'archive-job', 'job-approval', 'hr-tracking', 'job-attachement-settings', 'list-jobs-from-all', 'job-tracking-summary', 'view_recruitinganalyticswidgets', 'view_admin', 'candidate-screening-summary', 'candidate-mapping', 'candidate-tracking-summary', 'employee-mapping-rating', 'supervisor-mapping-rating','candidate_transition_process','view_all_whistleblower','create_employee_whistleblower','create_all_whistleblower','create_allocated_whistleblower','view_employee_whistleblower','view_allocated_whistleblower','create_exit_interview','create_all_exit_interview','view_all_exit_interview','view_exit_interview','view_all_candidates','view_all_candidates_candidate_geomapping','view_all_employee_surveys','view_allocated_employee_surveys','view_allocated_sites_in_employeefeedback','view_all_sites_in_employeefeedback','view_transaction_department_allocation'])
        @include('layouts.partials.cgl360_menu_block4')
        @endcanany

        
        
       
        
        
       
    </ul>
</nav>
