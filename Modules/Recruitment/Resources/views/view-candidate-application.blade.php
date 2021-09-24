@extends('layouts.app')
@section('content')
<?php $error_block = '<span class="help-block text-danger align-middle font-12"></span>';?>
<div class="table_title">
    <div class="row">
    <div class="col-md-6">
    <h4> Screening Summary
    </h4>
</div>
@if( isset($job_onboarded))
 <div class="col-md-6" style="text-align: right;padding-right: 60px;">
     <form action="{{ route('recruitment.candidate-onboarding.view', ['candidate_id' =>  $candidate->id] ) }}" target="_blank" method="GET">
            <input type="submit"  value="Onboarding" class="btn submit" />
        </form>
</div>
@endif</div>
</div>

<div class="container">
    <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item complete">
                <a class="nav-link active" data-toggle="tab" href="#profile">
                    <span> Profile
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#questions">
                    <span>   Screening Questions
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#personality_inventory">
                    <span> Personality
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#competency_matrix">
                    <span> Competency
                    </span>
                </a>
            </li>
           {{--  <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#attachment">
                    <span> Attachments
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#eventlog">
                    <span> Event Log
                    </span>
                </a>
            </li> --}}
        </ul>
        <div class="tab-content">
            <div id="profile" class="tab-pane active candidate-screen">
                <br>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Profile
                </div>
                <section class="candidate full-width">
                    @include('recruitment::job-application.partials.profile')
                </section>
            </div>
            <div id="questions" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Scenario Based Questions
                    </div>
                    <section class="candidate full-width">
                        @include('recruitment::screening-question')
                    </section>
                </div>
            </div>
            <div id="personality_inventory" class="container-fluid tab-pane fade">

                <div class="row">
                   <!--  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Scenario Based Questions
                   </div> -->
                    <section class="candidate full-width">
                        @include('recruitment::personality-inventory')
                    </section>
                </div>
            </div>
            <div id="competency_matrix" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Competency Matrix
                   </div>
                    <section class="candidate full-width">
                        @include('recruitment::candidate.application.competency-matrix')
                    </section>
                </div>
            </div>
           {{--  <div id="attachment" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Attachments
                    </div>
                    <section class="candidate full-width">
                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents
                        </label>
                        @include('recruitment::candidate.application.attachements')
                    </section>
                </div>
            </div>
            <div id="eventlog" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    @include('recruitment::schedule-event-log-summary')
                </div>
            </div> --}}
            {{-- <div class="candidate-screen display-inline print-view-btn" style="float:right;">
                <a title="Print application" href="{{route('candidate-job.print-view',$candidateJob->id)}}">
                    <i class="fa fa-print" aria-hidden="true"></i>
                </a>
            </div> --}}
        </div>
    </div>
</div>
@stop @section('scripts')
<script>
    $(document).ready(function () {
        $('.add-previous-adresses,.add-position,.add-reference,.add-education,.add-languages,.remove-language').parents('.form-group').remove();
        $('#profile input,#profile select,#profile textarea').prop('disabled', true);
        $(window).scroll(function(){
              $('.datepicker').blur();
          });
    });
</script>
@stop
<style>
.subTabs{
    margin-left: 0px !important;
    margin-top: -5px !important;
}
#engilshrating{
    margin: 0;
    padding: 0;
}
</style>
