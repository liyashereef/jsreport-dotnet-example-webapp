@extends('layouts.app')
@section('content')
<?php $error_block = '<span class="help-block text-danger align-middle font-12"></span>';?>
<div class="container">
    <div class="row">
        <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
            <li class="nav-item active">
                <a class="nav-link " data-toggle="tab" href="#profile"><span>1. Profile</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link " data-toggle="tab" href="#questions"><span>2. Questions</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link " data-toggle="tab" href="#uniform"><span>3. Uniforms</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link " data-toggle="tab" href="#attachment"><span>4. Attachments</span></a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="profile" class="tab-pane active candidate-screen"><br>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Profile </div>
                <section class="candidate">
                    {{ Form::open(array('id'=>'apply-job-form','class'=>'form-horizontal','method'=> 'POST','autocomplete'=>'dfgggf')) }}
                    {{csrf_field()}}
                    {{Form::hidden('mode','edit')}}
                    @include('hranalytics::job-application.partials.profile')
                    <div class="text-center margin-bottom-5">
                        <button type="button" class="yes-button" id="cancel" ><div class="back"><span class="backtext">Cancel</span></div></button>
                        <button type="submit" id="add-data" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Update</span></div></button>
                    </div>
                    {{ Form::close() }}
                </section>
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
                            <button type="submit" id="true" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Update</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>
            <div id="uniform" class="container-fluid tab-pane fade"><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Uniform </div>
                    <section class="candidate full-width">
                        {{ Form::open(array('id'=>'screening-uniform-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                        {{csrf_field()}}

                        @include('hranalytics::candidate.application.uniform_measurement')
                        <div class="text-center margin-bottom-5">
                            <button type="button" class="yes-button backtab" ><div class="back"><span class="backtext">Back</span></div></button>
                            <button type="submit" id="true" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Next</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>
@php
$mandatory_items = json_decode($session_obj['job']->required_attachments);
@endphp
             <div id="attachment" class="container-fluid tab-pane fade"><br>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Attachments </div>
                    <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">** Please note, your documents will be uploaded to our secure server. Your privacy and security will be protected using advanced encryption.

                        <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents(Please use files that are 3 MB or under)</label>
                        {{ Form::open(array('url'=>route('applyjob.attachment'),'id'=>'attachment-form','class'=>'form-horizontal', 'method'=> 'POST', 'files'=>'true','data-action'=>'edit', 'enctype'=>'multipart/form-data')) }}
                        {{csrf_field()}}

     @foreach($lookups['attachmentLookups'] as $i=>$attachment)
        @if(array_key_exists($attachment->id, $attachement_ids))
        <span class="col-sm-12 name-label">{{$attachment->attachment_name}}
                        @if(is_array($mandatory_items) && in_array($attachment->id,$mandatory_items))
                        <span class="mandatory">*</span>
                        @endif
        </span>
        <div class="form-group row attachment_div success" id="attachment_file_name.{{$attachment->id}}" >
               <div class="form-group row col-sm-12">

                        <div id="attachment_name_div_{{$attachment->id }}" class="col-sm-4">
                        <a class="nav-link" target="_blank" href="{{ asset('attachments') }}/{{$attachement_ids[$attachment->id]}}" />Click here to download the file
                        </a>



                         <input type="hidden" id="uploaded_files_count" value="{{count($attachement_ids)}}">
                         </div>

                        <div style="display: none;" id="upload_file_div_{{$attachment->id }}" class="col-sm-4">
                                    {{Form::file('attachment_file_name[' .$attachment->id. ']',array('class'=>'form-control file_attachment scroll-clear','id'=>'attach_id_'.$attachment->id,'onchange'=>'validateFileSize(this);'))}}
                                     <small class="help-block"  id="attachment-validation"></small>
                                    <div class="status_upload{{$attachment->id }}" style="padding-bottom:40px;">

                        </div>
                        </div>
                        <div style="display: none;" id="upload_btn_div_{{$attachment->id }}" class="col-sm-4" id="attachment_upload">
                                     <input id="file_attachment_upload_btn" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload" data-id="{{$attachment->id }}">
                        </div>


                        <div class="col-sm-4" id="attachment_remove_div_{{$attachment->id }}">
                                     <input id="file_attachment_remove_btn" class="button btn btn-edit file_attachment_remove_btn" onclick="removeAttachment('{{$candidateJob->candidate_id}}','{{$attachment->id }}')" type="button" value="Remove" data-id="{{$attachment->id }}">
                        </div>
                </div>
        </div>
              @else
        <span class="col-sm-12 name-label">{{$attachment->attachment_name}}
                        @if(is_array($mandatory_items) && in_array($attachment->id,$mandatory_items))
                        <span class="mandatory">*</span>
                        @endif
        </span>
        <div class="form-group row attachment_div {{ $errors->has('attachment_file_name') ? 'has-error' : '' }}" id="attachment_file_name.{{$attachment->id }}" >
               <div class="form-group row col-sm-12">

                        <div class="col-sm-4">
                                    {{Form::file('attachment_file_name[' .$attachment->id. ']',array('class'=>'form-control file_attachment scroll-clear','id'=>'attach_id_'.$attachment->id,'onchange'=>'validateFileSize(this);'))}}
                                     <small class="help-block"  id="attachment-validation"></small>
                                    <div class="status_upload{{$attachment->id }}" style="padding-bottom:40px;">

                        </div>
                        </div>

                        <div class="col-sm-4" id="attachment_upload">
                                     <input id="file_attachment_upload_btn" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload" data-id="{{$attachment->id }}">
                        </div>
                </div>
        </div>
              @endif

@endforeach
                        <div class="text-center margin-bottom-5">
                             <button type="button" class="yes-button backtab" ><div class="back"><span class="backtext">Back</span></div></button>
                             <button type="submit" id="add-data" class="yes-button" disabled="true"><div class="yes"><span class="yestext">Update</span></div></button>
                        </div>
                        {{ Form::close() }}
                    </section>
                </div>
            </div>

            @include('hranalytics::job-application.partials.scripts')
        </div>
    </div>
</div>

<script>
    $("#cancel").click(function () {

        swal({
            title: "Are you sure?",
            text: "Are you sure you want to cancel the edits?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },
                function () {

                    window.location = "{{ route('candidate') }}";
                });

    });
$(function () {
    $(".languageblockselect").select2();
});
</script>
@stop
