@extends('layouts.candidate-layout')
@section('content')
<?php $error_block = '<span class="help-block text-danger align-middle font-12"></span>';?>
<div class="container">
    <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item active">
                <a class="nav-link" data-toggle="tab" href="#profile"><span>1. Profile</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link disabled" data-toggle="tab" href="#questions"><span>2. Questions</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" data-toggle="tab" href="#personality_inventory"><span>3. Personality</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" data-toggle="tab" href="#comptency_matrix"><span>4. Competency</span></a>
            </li>
            <li class="nav-item disabled ">
                <a class="nav-link disabled" data-toggle="tab" href="#uniform"><span>5. Uniform</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" data-toggle="tab" href="#attachment"><span>6. Attachments</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" data-toggle="tab" href="#submit"><span>7. Submit</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" data-toggle="tab" href="#print"><span>8. Print Application</span></a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="profile" class="tab-pane active candidate-screen"><br>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Profile </div>
                <section class="candidate">
                    {{ Form::open(array('id'=>'apply-job-form','class'=>'form-horizontal','method'=> 'POST','autocomplete'=>'true','files'=>'true', 'enctype'=>'multipart/form-data')) }}
                    {{csrf_field()}}
                    @include('hranalytics::job-application.partials.profile')
                    <div class="text-center margin-bottom-5">
                        <button type="button" class="yes-button" id="cancel" ><div class="back"><span class="backtext">Cancel</span></div></button>
                        <button type="submit" id="add-data" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Next</span></div></button>
                    </div>
                    {{ Form::close() }}
                </section>
            </div>
            <div id="uniform" class="container-fluid tab-pane fade"><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Uniform </div>
                    <section class="candidate full-width">
                        {{ Form::open(array('id'=>'screening-uniform-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                        {{csrf_field()}}

                        @include('hranalytics::job-application.partials.uniform_measurement')
                        <div class="text-center margin-bottom-5">
                            <button type="button" class="yes-button backtab" ><div class="back"><span class="backtext">Back</span></div></button>
                            <button type="submit" id="true" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Next</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>
            <div id="questions" class="container-fluid tab-pane fade"><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Scenario Based Questions </div>
                    <section class="candidate full-width">
                        {{ Form::open(array('id'=>'screening-questions-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                        {{csrf_field()}}

                        @include('hranalytics::job-application.partials.screening_questions')
                        <div class="text-center margin-bottom-5">
                            <button type="button" class="yes-button backtab" ><div class="back"><span class="backtext">Back</span></div></button>
                            <button type="submit" id="true" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Next</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>

            <div id="personality_inventory" class="container-fluid tab-pane fade"><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Personality Inventory Questions </div>
                    <section class="candidate full-width">
                        {{ Form::open(array('id'=>'personality-inventory-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                        {{csrf_field()}}

                        @include('hranalytics::job-application.partials.personality_test')
                        <div class="text-center margin-bottom-5" id="navigation-btn">
                            <button type="button" class="yes-button backtab" ><div class="back"><span class="backtext">Back</span></div></button>
                            <button type="submit" id="true" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Next</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>

            <div id="comptency_matrix" class="container-fluid tab-pane fade"><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Competencies </div>
                    <section class="candidate full-width">
                        {{ Form::open(array('id'=>'competency-matrix-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                        {{csrf_field()}}
                        @include('hranalytics::job-application.partials.competency_matrix')
                        <div class="text-center margin-bottom-5" id="navigation-btn">
                            <button type="button" class="yes-button backtab" ><div class="back"><span class="backtext">Back</span></div></button>
                            <button type="submit" id="true" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Next</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>

            <div id="attachment" class="container-fluid tab-pane fade"><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Attachments </div>
                    <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">** Please note, your documents will be uploaded to our secure server. Your privacy and security will be protected using advanced encryption.
                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents (Please use files that are 3 MB or under)</label>
                        {{ Form::open(array('url'=>route('applyjob.attachment'),'id'=>'attachment-form','class'=>'form-horizontal', 'method'=> 'POST', 'files'=>'true', 'enctype'=>'multipart/form-data')) }}
                        {{csrf_field()}}
                        @include('hranalytics::job-application.partials.attachements')
                        <div class="text-center margin-bottom-5">
                            <button type="button" class="yes-button backtab"><div class="back"><span class="backtext">Back</span></div></button>
                            <button type="submit" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Next</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>
            @include('hranalytics::job-application.partials.review-submit')
            @include('hranalytics::job-application.partials.scripts')
        </div>
    </div>
</div>

<script>
    $("#cancel").click(function () {

        swal({
            title: "Are you sure?",
            text: "Are you sure you want to cancel this job application?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },
                function () {

                    //window.location = "{{ (isset(Session::get('CANINFO')['candidate'])) ? route('applyjob.dashboard'):route('applyjob.logout')}}";
                    window.location = "{{ route('applyjob.logout')}}";
                });

    });

$(".select2").select2();
</script>
@stop
