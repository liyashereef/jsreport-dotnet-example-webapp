
<style>
    .card-title{
        font-size: 16px;
        font-weight: 600;
        line-height: 18px;
        margin-bottom: 0;
        color: #4d4d4d;
        margin: 0 0 12.5px;
    }
    .course-card{
        width: 15.5rem; margin: 5px; float: left
    }
    .progress {
        height: 0.3rem !important;
        line-height: 1.4;
    }
    .progress-bar{
        background-color:#0ccf6b !important;
    }
    a{
        cursor: pointer;
    }

    /*.widget-color-change{*/
    /*    background-color:#333f50 !important;*/
    /*}*/

</style>
<link href="{{ asset('css/training/leaner-dashboard/css/dashboard-styles.css') }}" rel="stylesheet">

<!-- Page Content  -->
@include('learningandtraining::learner.partials.dashboard-content')
@include('learningandtraining::learner.partials.dashboard-scripts')
