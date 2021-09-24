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
        @can('learner_view')
            <li>
                <a href="{{ route('learning.dashboard') }}"  class="dropdown-toggle">
                    <!--<img src="images/nav3.png">-->
                    <i title="Leaner-Dashboard" class="fa fa-book" aria-hidden="true"></i>
                    <span>Dashboard </span>
                </a>
            </li>
            @can('learner_admin')
            <li>
                <a href="{{ route('learningandtraining.dashboard') }}"  class="dropdown-toggle">
                    <i title="Learner View" class="fa fa-briefcase" aria-hidden="true"></i>
                    <span>Admin View</span>
                </a>
            </li>
            @endcan
            

        @endcan




        
       
       
       
    </ul>
</nav>
