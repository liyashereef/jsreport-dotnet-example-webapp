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
        @can('learner_admin')
            <li>
                <a href="{{ route('learningandtraining.dashboard') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->
                    <i title="Admin-Dashboard " class="fa fa-book" aria-hidden="true"></i>
                    <span>Dashboard </span>
                </a>
            </li>

            <li>
                <a href="homeSubmenu" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                    <!--<img src="images/nav1.png">-->
                    <i title="Course " class="fa fa-university" aria-hidden="true"></i>
                    <span>Course </span>
                </a>
                <ul class="dropdown-menu first-menu-list" role="menu" aria-labelledby="menu1">


{{--                </li>--}}

                        <li class="dropdown-submenu">
                            <a  href="{{ route('course-category-admin') }}">Categories</a>

                        </li>
                        <li class="dropdown-submenu">
                            <a  href="{{ route('course-admin') }}">Courses</a>


                        </li>
                        
{{--                        <li class="dropdown-submenu">--}}
{{--                            <a  href="{{ route('course-content-admin') }}">Create Contents</a>--}}
{{--                        </li>--}}
                </ul>
            </li>

            <li>
                <a href="homeSubmenu" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                    <i title="Team " class="fa fa-users" aria-hidden="true"></i>
                    <span>Team </span>
                </a>
                <ul class="dropdown-menu first-menu-list" role="menu" aria-labelledby="menu1">

                    <li class="dropdown-submenu">
                        <a  href="{{ route('learningandtraining.team.create') }}">Create</a>

                    </li>
                    <li class="dropdown-submenu">
                        <a  href="{{ route('learningandtraining.team.list.page') }}">List</a>
                    </li>
                    <li class="dropdown-submenu">
                        <a  href="{{ route('learningandtraining.team.employee-allocation.page') }}">Allocation</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('learningandtraining.dashboard.reports') }}"  class="dropdown-toggle">
                    <i title="Admin-Dashboard " class="fa fa-signal" aria-hidden="true"></i>
                    <span>Reports </span>
                </a>
            </li>

            @can('learner_view')
            <li>
                <a href="{{ route('learning.dashboard') }}"  class="dropdown-toggle">
                    <i title="Learner View" class="fa fa-book" aria-hidden="true"></i>
                    <span>Learner View</span>
                </a>
            </li>
            @endcan
        @endcan



{{--        <li>--}}
{{--            <a href="homeSubmenu2" data-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">--}}
{{--                <!--<img src="images/nav3.png">-->--}}
{{--                <i title="Capacity Tool" class="fa fa-wrench" aria-hidden="true"></i>--}}
{{--                <span>Reports</span>--}}
{{--            </a>--}}
{{--            <ul class="dropdown-menu menu-list" role="menu">--}}
{{--                @can('create_entry')--}}
{{--                    <li class="dropdown-submenu">--}}
{{--                        <a href="{{ route('capacitytool.create') }}"> Track Capacity </a>--}}
{{--                    </li>--}}
{{--                @endcan--}}
{{--                <li class="dropdown-submenu">--}}
{{--                    <a href="{{ route('capacitytool') }}"> View Capacity </a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </li>--}}

        
       
       
       
    </ul>
</nav>
