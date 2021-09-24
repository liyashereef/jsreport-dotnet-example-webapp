@extends('layouts.app')
@section('content')
<?php $error_block = '<span class="help-block text-danger align-middle font-12"></span>';?>
<div class="table_title">
    <h4> Screening Summary
    </h4>
</div>
<div class="container">
    <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item success">
                <a class="nav-link " data-toggle="tab" href="#profile">
                    <span>1. Profile
                    </span>
                </a>
            </li>
            <li class="nav-item success">
                <a class="nav-link" data-toggle="tab" href="#questions">
                    <span>2. Screening Questions
                    </span>
                </a>
            </li>
            <li class="nav-item success">
                <a class="nav-link" data-toggle="tab" href="#attachment">
                    <span>3. Attachments
                    </span>
                </a>
            </li>
            @canany(['view_interview_notes','candidate-add-interview-notes'])
            @if($candidateJob->candidate_status == 'Proceed')
            <li class="nav-item success">
                <a class="nav-link" data-toggle="tab" href="#interview">
                    <span>4. Interview Notes
                    </span>
                </a>
            </li>
            @endif
            @endcanany
        </ul>

        <div class="tab-content">

            <div id="profile" class="tab-pane active candidate-screen">
                <br>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Profile
                </div>
                <section class="candidate full-width">
                    @include('hranalytics::job-application.partials.profile')
                </section>
            </div>
            <div id="questions" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Scenario Based Questions
                    </div>
                    <section class="candidate full-width">
                        @include('hranalytics::candidate.application.screening-question')
                    </section>
                </div>
            </div>
            <div id="attachment" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Attachments
                    </div>
                    <section class="candidate full-width">
                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents
                        </label>
                        @include('hranalytics::candidate.application.attachements')
                    </section>
                </div>
            </div>
            @canany(['view_interview_notes','candidate-add-interview-notes'])
            @if($candidateJob->candidate_status == 'Proceed')
                <br> @include('hranalytics::candidate.application.interview-notes')
            @endif
            @endcanany
            <div class="candidate-screen display-inline print-view-btn" style="float:right;">
                <a title="Print application" href="{{route('candidate-job.print-view',$candidateJob->id)}}">
                    <i class="fa fa-print" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script>
    $(document).ready(function () {
        $('.add-previous-adresses,.add-position,.add-reference,.add-education').parents('.form-group').remove();
        $('#profile input,#profile select,#profile textarea').prop('disabled', true);
    });

</script>
@stop
