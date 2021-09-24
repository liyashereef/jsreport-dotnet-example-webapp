@extends('layouts.app') @section('content')
<div class="table_title">
    <h4>Dashboard </h4>
</div>
<!--Tabbed container-->
<section class="tabbed-content">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#jobPosting" role="tab" data-toggle="tab">Job Posting</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#candidateScreen" role="tab" data-toggle="tab">Candidate Screen</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#candidateDetails" role="tab" data-toggle="tab">Candidate Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#screen" role="tab" data-toggle="tab">Screen</a>
        </li>
    </ul>
    <!-- Tab content -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane in active" id="jobPosting">
            @include('hranalytics::dashboard.charts.job')
        </div>
        <div role="tabpanel" class="tab-pane" id="candidateScreen">
            @include('hranalytics::dashboard.charts.candidate-screening')
        </div>
        <div role="tabpanel" class="tab-pane" id="candidateDetails">
            @include('hranalytics::dashboard.charts.candidate-details')
        </div>
        <div role="tabpanel" class="tab-pane" id="screen">
            @include('hranalytics::dashboard.charts.screen')
        </div>
    </div>
    <!-- Tab content END-->
</section>
<!--Tabbed container END-->

@endsection
@section('scripts')
{!! Charts::scripts() !!}
@if(isset($charts['job']))
@foreach($charts['job'] as $chart)
{!!$chart->script() !!}
@endforeach
@endif
@if(isset($charts['candidate']))
@foreach($charts['candidate'] as $chart)
{!! $chart->script()!!}
@endforeach
@endif
@if(isset($charts['candidate_details']))
@foreach($charts['candidate_details'] as $chart)
{!! $chart->script()!!}
@endforeach
@endif
@if(isset($charts['candidate_screen']))
@foreach($charts['candidate_screen'] as $chart)
{!! $chart->script()!!}
@endforeach
@endif
<script>
    function chartClickHandle($var) {
        var $url;
        console.log($var);
        switch ($var) {
            /* Job Posting - Start */
            case 'Job Requisitions':
                $url = "{{ route('dashboard.drilldown','job-requisitions') }}";
                break;
            case 'Position By Region':
                $url = "{{ route('job') }}";
                break;
            case 'Highest Turnover':
                $url = "{{ route('job') }}";
                break;
            case 'Position By Reasons':
                $url = "{{ route('job') }}";
                break;
            case 'Planned OJT':
                $url = "{{ route('job') }}";
                break;
            /* Job Posting - End */
            /* Candidate Screen - Start */
            case 'Candidates':
                $url = "{{ route('candidate') }}";
                break;
            case 'Candidates Regions':
                $url = "{{ route('candidate') }}";
                break;
            case 'Candidates Certificates':
                $url = "{{ route('dashboard.drilldown','candidate-certificates') }}";
                break;
            case 'Candidates Experiences(Categories)':
                $url = "{{ route('candidate') }}";
                break;
            case 'Candidates Experiences(Regions)':
                $url = "{{ route('candidate') }}";
                break;
            case 'Wage Expectation by Region':
            case 'Wage Expectation by Position':
            case 'Wage by Competitor':
                $url = "{{ route('candidate') }}";
                break;
            /* Candidate Screen - End */
            /* Candidate Details - Start */
            case 'Candidate Resident Status':
                $url = "{{ route('candidate') }}";
                break;
            case 'Guards Drivers license':
                $url = "{{ route('candidate') }}";
                break;
            case 'Access to Public Transit':
                $url = "{{ route('candidate') }}";
                break;
            case 'Limited Transportation':
                $url = "{{ route('candidate') }}";
                break;
            case 'Candidates By Level of Language Fluency(English)':
            case 'Candidates By Level of Language Fluency (French)':
            case 'Candidates Skills (Computer)':
            case 'Candidates Skills (Soft Skills)':
            case 'Employment Entities':
                $url = "{{ route('candidate') }}";
                break;
            /* Candidate Details - End */
            /* Screen - Start */
            case 'Candidates By Military Experience':
                $url = "{{ route('candidate') }}";
                break;
            case 'Fired/Convicted Candidates':
                $url = "{{ route('candidate') }}";
                break;
            case 'Candidates By Career Interset In CGL':
                $url = "{{ route('candidate') }}";
                break;
            case 'Candidates By Average Score':
                $url = "{{ route('candidate') }}";
                break;
            case 'Loading Documents':
                $url = "{{ route('candidate') }}";
                break;
            case 'Average Cycle Time':
                $url = "{{ route('candidate.summary') }}";
                break;
            /* Screen - End */
        }
        if ($url !== undefined)
            window.location = $url;
    }
</script>
@endsection
