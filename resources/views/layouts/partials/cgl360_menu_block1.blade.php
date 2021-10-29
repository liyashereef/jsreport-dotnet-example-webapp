<li attr-block="1"  class="pli">
    <a href="#" attr-block="1"  class="mainhead mhead">
        <div attr-block="1" class="plihidden ">A </div>

        <span attr-block="1" >Administration </span>
    </a>
</li>
@canany(['add_client_document','view_client_document','add_allocated_client_document','view_allocated_client_document','add_employee_document','view_employee_document','add_allocated_employee_document','view_allocated_employee_document','add_other_document','view_other_document'])

<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle" style="display: table">
        <!--<img src="images/nav3.png">-->
        <i title="Documents " class="iconclass fa fa-file fa-fw"  aria-hidden="true"></i>
        <span class="multilinespan">Documents Management </span>
    </a>
    <ul class="dropdown-menu menu-list" role="menu">
        @canany(['add_client_document','view_client_document','add_allocated_client_document','view_allocated_client_document'])
        <li class="dropdown-submenu">
            <a tabindex="-1" href="{{ route('documents.client-document') }}">Client</a>

        </li>
        @endcan
        @canany(['add_employee_document','view_employee_document','add_allocated_employee_document','view_allocated_employee_document'])
        <li class="dropdown-submenu">
            <a tabindex="-1" href="{{ route('documents.employee-document') }}">Employee</a>

        </li>
        @endcan
        @canany(['add_other_document','view_other_document'])
        <li class="dropdown-submenu">
            <a class="test" tabindex="-1" href="#">Others</a>
            <?php $other_documents = Modules\Admin\Models\OtherCategoryLookup::get();?>
            <ul class="dropdown-menu">
                @foreach($other_documents as $doc)
                <li>
                    <a tabindex="-1" href="{{ route('documents.other-vendor',['id'=>$doc['id']]) }}">{{$doc['category_name']}}</a>
                </li>
                @endforeach
            </ul>
        </li>
        @endcan
    </ul>
</li>
@endcanany
@canany(['view_contracts'])
<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Contracts " class="iconclass fa fa-handshake" aria-hidden="true"></i>
        <span class="multilinespan">Contract Management </span>
    </a>
    <ul class="dropdown-menu menu-list" role="menu">

        @canany(['view_rfp_catalogue','create_rfp_catalogue'])
        <li class="dropdown-submenu">
            <a class="test" tabindex="-1" href="#">
                RFP Catalog
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('rfp-catalogue.view') }}"> View </a>
                </li>
                 @can(['create_rfp_catalogue'])
                <li>
                    <a href="{{ route('rfp-catalogue.create') }}"> Create </a>
                </li>
                 @endcan
            </ul>
        </li>
        @endcan
            @canany(['view_contracts'])
            <li class="dropdown-submenu">
                    <a tabindex="-1" href="{{ route('contracts.all-cmuf') }}">View All Contracts</a>
            </li>
            @endcan
            <li class="dropdown-submenu">
                <a class="test" tabindex="-1" href="#">RFP Tracking
                </a>

                <ul class="dropdown-menu">
                        @can(['create_rfp'])
                    <li>
                        <a href="{{ route('rfp.create') }}"> New RFP </a>
                    </li>
                    @endcan
                    <li>
                        <a href="{{ route('rfp.summary') }}"> RFP Summary </a>
                    </li>
                    {{-- <li>
                        <a href="{{route('rfp.rfplink')}}"> RFP link </a>
                    </li> --}}

                </ul>

            </li>
    </ul>
</li>
@endcanany
@canany(['view_all_expense_claim','view_allocated_expense_claim','view_all_mileage_claim','view_allocated_mileage_claim','expense_send_statements'])

<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Financial Management " class="iconclass fa fa-dollar-sign fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Financial Management </span>
    </a>
    <ul class="dropdown-menu menu-list" role="menu">
        @canany(['view_all_expense_claim','view_allocated_expense_claim','view_all_mileage_claim','view_allocated_mileage_claim'])
        <li class="dropdown-submenu">
        @if(auth()->user()->can('view_all_expense_claim') || auth()->user()->can('view_allocated_expense_claim'))
            <a href="{{ route('expense-dashboard.index') }}">Dashboard</a>
        @else
            <a href="{{ route('mileage-dashboard.index') }}">Dashboard</a>
        @endif
        </li>
        @endcan
        @can('expense_send_statements')
        <li class="dropdown-submenu">
          <a href="{{ route('expense-statements.create') }}">Expense Statements</a>
        </li>
        @endcan
    </ul>
</li>
@endcanany
@canany(['initiate_vehicle','edit_initiated_vehicle','view_completed_maintenance','view_vehicle_cumilative_km','view_pending_maintenance'])

<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Fleet Management " class="iconclass fa fa-car fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Fleet Management </span>

    </a>
    <ul class="dropdown-menu menu-list" role="menu">
        @canany(['initiate_vehicle','edit_initiated_vehicle'])
       <li class="dropdown-submenu">
           <a href="{{ route('vehicle.initiate') }}"> Initiate Vehicle </a>
       </li>
       @endcan
       @canany(['view_completed_maintenance','view_vehicle_cumilative_km','view_pending_maintenance'])
       <li class="dropdown-submenu">
           @if (\Auth::user()->can('view_pending_maintenance'))
           <a href="{{ route('vehicle.pending.maintenance') }}"> Maintenance </a>
           @elseif(\Auth::user()->can('view_completed_maintenance'))
           <a href="{{ route('vehicle.maintenance') }}"> Maintenance </a>
           @elseif(\Auth::user()->can('view_vehicle_cumilative_km'))
           <a href="{{ route('vehicle.cumilative_km') }}"> Maintenance </a>
           @endif

       </li>
        @endcan
   </ul>
</li>
@endcanany
@canany(['uniform_view_all_appointment','view_ura_transactions','view_uniform_orders'])

<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Uniform Management " class="iconclass fa fa-tshirt fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Uniform Management </span>
    </a>
    <ul class="dropdown-menu menu-list" role="menu">
        @can(['uniform_view_all_appointment'])
            <li class="dropdown-submenu">
                <a href="{{ route('uniform-admin') }}"> View Schedule</a>
            </li>
            <li class="dropdown-submenu">
                <a href="{{ route('uniform-admin.slot-booking.list-page') }}"> Appoinment Lists</a>
            </li>
        @endcan
        @can(['view_ura_transactions'])
        <li class="dropdown-submenu">
                <a href="{{route('ura.transactions') }}">URA Transactions</a>
        </li>
        @endcan
        @can(['view_uniform_orders'])
        <li class="dropdown-submenu">
            <a href="{{route('uniform.orders') }}">Order Details</a>
        </li>
        @endcan
    </ul>
</li>
@endcanany
@canany(['user_view','customer_view','view_covid_daily_transaction_report',
'view_covid_compliance_report','view_sitenote_reports','view_customersurvey_reports',
'view_visitor_log_report','view_all_site_document_report','view_allocated_site_document_report',
'view_recruiting_analytics_report','view_termination_report'])
<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Management Reports " class="iconclass fa fa-chart-bar fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Management Reports </span>
    </a>
    <ul class="dropdown-menu menu-list" role="menu">
        @canany(['user_view','customer_view'])
        <li class="dropdown-submenu">
            <a class="test" tabindex="-1" href="#">List</a>
            <ul class="dropdown-menu menu-list mst-side-menu" role="menu">
            @can(['user_view'])
            <li class="dropdown-submenu">
                <a tabindex="-1" href="{{ route('management.userList') }}">User</a>
            </li>
            @endcan
            @can(['customer_view'])
            <li class="dropdown-submenu">
                <a tabindex="-1" href="{{ route('management.customerList') }}">Customer</a>
            </li>
            @endcan
        </ul>
        </li>
        @endcanany
        @canany(['view_covid_daily_transaction_report','view_covid_compliance_report','view_sitenote_reports','view_customersurvey_reports','view_visitor_log_report','view_all_site_document_report','view_allocated_site_document_report','view_recruiting_analytics_report','view_termination_report'])

        <li class="dropdown-submenu">
            <a class="test" tabindex="-1" href="#">Reports</a>
            <ul class="dropdown-menu menu-list " role="menu">
                @can('view_covid_daily_transaction_report')
                    <li>
                        <a href="{{ route('reports.dailytransactions') }}">Daily Transaction Report</a>
                    </li>
                @endcan
                @can('view_covid_compliance_report')
                    <li>
                        <a href="{{ route('reports.fevercompliancereport') }}">Compliance Report</a>
                    </li>
                @endcan
                @can('view_sitenote_reports')
                    <li>
                        <a href="{{ route('reports.sitenotes') }}">Site Notes Report</a>
                    </li>
                @endcan
                @can('view_customersurvey_reports')
                    <li>
                        <a href="{{ route('reports.surveryreport') }}">Survey Report</a>
                    </li>
                @endcan

                @can('view_visitor_log_report')
                    <li>
                        <a href="{{ route('reports.visitorLogReport') }}">Visitor Log Report</a>
                    </li>
                @endcan

              {{--   @can('view_certificate_expiry_report') --}}
                 @canany(['view_all_site_document_report','view_allocated_site_document_report','view_certificate_expiry_report'])
                    <li>
                        <a href="{{ route('reports.certificateExpiryReport') }}">Document Expiry</a>
                    </li>
                 {{-- @endcan --}}
                @endcan

                @can('view_recruiting_analytics_report')
                    <li>
                        <a href="{{ route('reports.recruitinganalyticsreport') }}">Recruiting Report</a>
                    </li>
                @endcan
                @can('view_termination_report')
                    <li>
                        <a href="{{ route('reports.terminationReport') }}">Termination Report</a>
                    </li>
                @endcan
            </ul>
        </li>
        @endcan

    </ul>
</li>
@endcanany
@canany(['view_osgc_registered_users'])

<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="OSGC Training " class="iconclass fa fa-school fa-fw" aria-hidden="true"></i>
        <span class="singlelinespan">OSGC </span>
    </a>
    <ul class="dropdown-menu menu-list" role="menu">
        @can('view_osgc_registered_users')
            <li>
                <a href="{{ route('osgc.registered-users') }}">Registered Users</a>
            </li>
        @endcan
    </ul>
</li>
@endcan
@canany(['create_task_all_customer','create_task_allocated_customer','view_all_reports','view_allocated_customer_reports','view_assigned_reports','view_all_performance_reports','view_allocated_performance_reports'])

<li class="block1 accordclass">
    <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
        <!--<img src="images/nav3.png">-->
        <i title="Project Management " class="iconclass fa fa-project-diagram fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">Project Management </span>
    </a>
    <ul class="dropdown-menu menu-list" role="menu">
        @canany(['create_task_all_customer','create_task_allocated_customer'])
       <li>
           <a href="{{ route('project') }}">Projects</a>
       </li>
       @endcan
       @canany(['view_all_reports','view_allocated_customer_reports','view_assigned_reports'])
       <li>
           <a href="{{ route('pm.report') }}">Reports</a>
       </li>
       @endcan
        @canany(['view_all_performance_reports','view_allocated_performance_reports'])
       <li>
           <a href="{{ route('pm.get-project-rating') }}">Performance Report</a>
       </li>
        @endcan
   </ul>
</li>
@endcan
@can('view_admin')

<li class="block1 accordclass">
    <a href="{{ route('admin') }}">
        <!--<img src="images/nav3.png">-->
        <i title="Documents " class="iconclass fa fa-cog fa-fw" aria-hidden="true"></i>
        <span class="multilinespan">System Administration </span>
    </a>
</li>
@endcan
