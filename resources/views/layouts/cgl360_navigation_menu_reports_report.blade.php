<style>
    .dropdown-submenu {
        position: relative;
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
</style>

<script>
    $(document).ready(function () {
        $('.dropdown-submenu a.test').on("click", function (e) {
            $('.dropdown-submenu .dropdown-menu').hide();
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
            $('#sidebar').find('.dropdown-menu').toggleClass('resp');
            $('.fa-caret-down').toggleClass('carat');
        });
        @if(!in_array(\Request::route()->getName(),[null,'home'])) $('#sidebarCollapse').trigger('click'); @endif
    });
</script>
<nav id="sidebar">
    <ul class="list-unstyled components">
        <li>
            <a id="sidebarCollapse" class="sidebarCollapseEl">
                <img class ="sidebarToggleImg" src="{{ asset('images/handburger.png') }}">
            </a>
        </li>
        <li>
            <a href="{{ url('/') }}"  class="dropdown-toggle">
                <i title="Back to Home" class="fa fa-arrow-left fa-fw" aria-hidden="true"></i>
                <span>Back to Home</span>
            </a>
        </li>
        @can('view_covid_daily_transaction_report')
            <li>
                <a href="{{ route('reports.dailytransactions') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->

                    <i title="Covid 19 Daily Transactions" class="fa fa-sticky-note fa-fw" aria-hidden="true"></i>
                    <span>Daily Transaction</span>

                </a>
            </li>
        @endcan
        @can('view_covid_compliance_report')
            <li>
                <a href="{{ route('reports.fevercompliancereport') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->

                    <i title="Covid 19 Daily Transactions" class="fa fa-sticky-note fa-fw" aria-hidden="true"></i>
                    <span>Compliance Report</span>

                </a>
            </li>
        @endcan
        @can('view_sitenote_reports')
            <li>
                <a href="{{ route('reports.sitenotes') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->

                    <i title="Site Notes Report" class="fa fa-sticky-note fa-fw" aria-hidden="true"></i>
                    <span>Site Notes Report</span>

                </a>
            </li>
        @endcan
        @can('view_customersurvey_reports')
            <li>
                <a href="{{ route('reports.surveryreport') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->
                    <i title="Survey Report" class="fas fa-poll fa-fw" aria-hidden="true"></i>
                 <span>Survey Report</span>
               </a>
            </li>
        @endcan

        @can('view_visitor_log_report')
            <li>
                <a href="{{ route('reports.visitorLogReport') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->
                    <i title="Visitor Log Report" class="fas fa-user-edit fa-fw" aria-hidden="true"></i>
                 <span>Visitor Log Report</span>
                </a>
             </li>
        @endcan

       {{--  @can('view_certificate_expiry_report') --}}
         @canany(['view_all_site_document_report','view_allocated_site_document_report'])
            <li>
                <a href="{{ route('reports.certificateExpiryReport') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->
                    <i title="Document Expiry Report" class="fas fa-ban fa-fw" aria-hidden="true"></i>
                 <span>Document Expiry</span>
               </a>
            </li>
              @endcan
       {{--  @endcan --}}

        @can('view_recruiting_analytics_report')
        <li>
                <a href="{{ route('reports.recruitinganalyticsreport') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->
                    <i title="Candidate Onboarding Status Report" class="fa fa-sticky-note fa-fw" aria-hidden="true"></i>
                 <span>Recruiting Report</span>
               </a>
            </li>
        @endcan

        @can('view_termination_report')
        <li>
            <a href="{{ route('reports.terminationReport') }}"  class="dropdown-toggle">
                <!--<img src="images/nav3.png">-->
                <i title="Termination Report" class="fa fa-minus-circle fa-fw" aria-hidden="true"></i>
             <span>Termination Report</span>
           </a>
        </li>
    @endcan
    </ul>
</nav>
