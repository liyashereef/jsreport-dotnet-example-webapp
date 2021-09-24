@extends('layouts.app')
@section('content')
<?php $error_block = '<span class="help-block text-danger align-middle font-12"></span>';?>
<div class="table_title">
    <h4> Screening Summary Onboarding
    </h4>
</div>
 

<div class="container">
    <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item complete">
                <a class="nav-link active" data-toggle="tab" href="#enrollment">
                    <span> Enrollment
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#security_clearance">
                    <span>  Security Clearance
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#tax_forms">
                    <span> Tax Forms
                    </span>
                </a>
            </li>
    
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#attachment">
                    <span> Attachments
                    </span>
                </a>
            </li>
            <li class="nav-item complete">
                <a class="nav-link" data-toggle="tab" href="#uniform">
                    <span> Uniform Measurements
                    </span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            
            <div id="enrollment" class="tab-pane active container-fluid">
                <br>
                 <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Enrollment
                </div>
                    <section class="candidate full-width">
                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents
                        </label>
                        @include('recruitment::candidate.application.document-view', ['document' =>$documents['enrollment']])
                    </section>
                </div>
            </div>
            <div id="security_clearance" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Enrollment
                </div>
                    <section class="candidate full-width">
                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents
                        </label>
                        @include('recruitment::candidate.application.document-view', ['document' =>$documents['securityclearance']])
                    </section> 
                </div>
            </div>
            <div id="tax_forms" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                   <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form -  Tax Forms
                   </div>
                   <section class="candidate full-width">
                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents
                        </label>
                        @include('recruitment::candidate.application.document-view', ['document' =>$documents['taxforms']])
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
                        @include('recruitment::candidate.application.attachements')
                    </section>
                </div>
            </div>
              <div id="uniform" class="container-fluid tab-pane fade">
                <br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head">
                        Candidate Screening Form - Uniforms

                    </div>
                    <section class="candidate full-width">
                        @include('recruitment::candidate.application.uniform_measurement')

                    </section>
                </div>
            </div>
           {{--  <div class="candidate-screen display-inline print-view-btn" style="float:right;">
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
        $('.add-previous-adresses,.add-position,.add-reference,.add-education').parents('.form-group').remove();
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
