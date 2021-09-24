@extends('layouts.app')
@section('content')
<?php $error_block = '<span class="help-block text-danger align-middle font-12"></span>';?>
<div class="container">
   <div class="row">
      <ul class="breadcrumb breadcrumb-arrow nav nav-tabs width-100" role="tablist">
         <li class="nav-item complete">
            <a class="nav-link active" data-toggle="tab" href="#profile"><span>1. Profile</span></a>
         </li>
         <li class="nav-item complete">
            <a class="nav-link " data-toggle="tab" href="#questions"><span>2. Questions</span></a>
         </li>
         @if(isset($candidateJob))
         <li class="nav-item complete">
            <a class="nav-link " data-toggle="tab" href="#enrollment"><span>3. Enrollment</span></a>
         </li>
         <li class="nav-item complete">
            <a class="nav-link " data-toggle="tab" href="#securityclearance"><span>4. Security Clearance</span></a>
         </li>
         <li class="nav-item complete">
            <a class="nav-link " data-toggle="tab" href="#taxforms"><span>5. Tax Forms</span></a>
         </li>
          <li class="nav-item complete">
            <a class="nav-link " data-toggle="tab" href="#attachment"><span>6. Attachments</span></a>
         </li>
         <li class="nav-item complete">
            <a class="nav-link " data-toggle="tab" href="#uniform"><span>7. Uniforms</span></a>
         </li>
         @endif
      </ul>
      <div class="tab-content">
         <div id="profile" class="tab-pane active candidate-screen">
            <br>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Profile </div>
            <section class="candidate">
               {{ Form::open(array('id'=>'apply-job-form','class'=>'form-horizontal','method'=> 'POST','autocomplete'=>'dfgggf')) }}
               {{csrf_field()}}
               {{Form::hidden('mode','edit')}}
               @include('recruitment::job-application.partials.profile')
               <div class="text-center margin-bottom-5">
                  <button type="button" class="yes-button" id="cancel" >
                     <div class="back"><span class="backtext">Cancel</span></div>
                  </button>
                  <button type="submit" id="add-data" class="yes-button" disabled="true">
                     <div class="yes"><span class="yestext">Update</span></div>
                  </button>
               </div>
               {{ Form::close() }}
            </section>
         </div>
         <div id="questions" class="container-fluid tab-pane fade">
            <br>
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Scenario Based Questions </div>
               <section class="candidate full-width">
                  {{ Form::open(array('id'=>'screening-questions-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                  {{csrf_field()}}
                  @include('recruitment::job-application.partials.screening_questions')
                  <div class="text-center margin-bottom-5">
                     <button type="button" class="yes-button backtab" >
                        <div class="back"><span class="backtext">Back</span></div>
                     </button>
                     <button type="submit" id="true" class="yes-button" disabled="true">
                        <div class="yes"><span class="yestext">Update</span></div>
                     </button>
                  </div>
                  {{ Form::close() }}
               </section>
            </div>
         </div>
         @if(isset($candidateJob))
         <div id="attachment" class="container-fluid tab-pane fade">
            <br>
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Attachments </div>
               <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                  {{-- ** Please note, your documents will be uploaded to our secure server. Your privacy and security will be protected using advanced encryption. --}}
                  <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents(Please use files that are 3 MB or under)</label>

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
                           <a class="nav-link" target="_blank" href="{{Storage::disk('s3')->url('attachement/'.$attachment->attachment_name) }}" />Click here to download the file
                           </a>
                           <input type="hidden" id="uploaded_files_count" value="{{count($attachement_ids)}}">
                        </div>


                         <div style="display: none;"  id="upload_file_div_{{$attachment->id}}" id="attachment_upload" class="col-sm-7" >
                         <form action="https://{{$my_bucket}}.s3-{{$region}}.amazonaws.com" method="post" class="aws_upload_attachment_form"  enctype="multipart/form-data">
                              <input type="hidden" name="acl" value="private">
                              <input type="hidden" name="success_action_status" value="201">
                              <input type="hidden" name="policy" value="{{$policybase64}}">
                              <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
                              <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                              <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
                              <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
                              <input type="hidden" name="X-amz-signature" value="{{$signature}}">
                             <input type="hidden" name="key" id="attachment-key" value="">
                              <input type="hidden" name="Content-Type" id="attachment-type" value="">

                        <div class="col-sm-12" >
                            <div id="upload-attachment-file" class="row form-group">
                                    <div class="col-sm-7">
                                        <input class="form-control" type="file" id="attachment-file" data-aid="{{$attachment->id }}" name="file" />
                                       <span class="help-block"></span>
                                    </div>
                                    <div class="col-sm-2">
                                       <input class="button btn btn-edit file_attachment_upload_btn" type="submit" value="Upload" />
                                    </div>
                                    <div class="col-sm-2" id="attachement-success" style="display:none;">
                                       <span  style="color: #35af3f;">Uploaded Successfully</span>
                                    </div>
                                 </div>
                          {{--  <input  id="file_attachment_upload_btn"  data-aid="{{$each_document['id'] }}" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload" data-id="{{$attachment->id }}"> --}}
                        </div>
                        <div class="col-sm-7" id="attachment-results">
                                 <!-- server response here -->
                              </div>
                           </form>
                        </div>


                       {{--  <div style="display: none;" id="upload_file_div_{{$attachment->id }}" class="col-sm-4">
                           {{Form::file('attachment_file_name[' .$attachment->id. ']',array('class'=>'form-control file_attachment scroll-clear','id'=>'attach_id_'.$attachment->id,'onchange'=>'validateFileSize(this);'))}}
                           <small class="help-block"  id="attachment-validation"></small>
                           <div class="status_upload{{$attachment->id }}" style="padding-bottom:40px;">
                           </div>
                        </div>
                        <div style="display: none;" id="upload_btn_div_{{$attachment->id }}" class="col-sm-4" id="attachment_upload">
                           <input id="file_attachment_upload_btn" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload" data-id="{{$attachment->id }}">
                        </div> --}}
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
                        <div class="col-sm-12">
                           <form action="https://{{$my_bucket}}.s3-{{$region}}.amazonaws.com" method="post" class="aws_upload_attachment_form"  enctype="multipart/form-data">
                              <input type="hidden" name="acl" value="private">
                              <input type="hidden" name="success_action_status" value="201">
                              <input type="hidden" name="policy" value="{{$policybase64}}">
                              <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
                              <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                              <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
                              <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
                              <input type="hidden" name="X-amz-signature" value="{{$signature}}">
                              <input type="hidden" name="key" id="attachment-key" value="">
                              <input type="hidden" name="Content-Type" id="attachment-type" value="">
                              <div class="col-sm-7">
                                 <div id="upload-attachment-file" class="row form-group">
                                    <div class="col-sm-7">
                                       <input class="form-control" type="file" id="attachment-file" data-aid="{{$attachment->id }}" name="file" />
                                       <span class="help-block"></span>
                                    </div>
                                    <div class="col-sm-2">
                                       <input class="button btn btn-edit file_attachment_upload_btn" type="submit" value="Upload" />
                                    </div>
                                    <div class="col-sm-2" id="attachement-success" style="display:none;">
                                       <span  style="color: #35af3f;">Uploaded Successfully</span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div id="uploaded-attachment-file" style="display: none;" class="row">
                                    <div class="col-sm-5">
                                       <span id="video-file-name"></span>
                                    </div>
                                    <div class="col-sm-2" id="attachment-success">
                                       <a href="" target="_blank">Download <i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i></a>
                                    </div>
                                    <div class="col-sm-2">
                                       <input class="button btn btn-primary blue form-control" id="video-remove" type="button" value="Remove" />
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-7" id="video-results">
                                 <!-- server response here -->
                              </div>
                           </form>
                           <span class="help-block"></span>
                        </div>

                     </div>
                  </div>
                  @endif
                  @endforeach
                   {{ Form::open(array('url'=>'#','id'=>'attachment-form','class'=>'form-horizontal', 'method'=> 'POST', 'files'=>'true','data-action'=>'edit', 'enctype'=>'multipart/form-data')) }}
                  {{csrf_field()}}
                  <input type="hidden" name="attachment_file_id[]" value="">
                  <input type="hidden" name="attachment_id[]" value="">
                  <div class="text-center margin-bottom-5">
                     <button type="button" class="yes-button backtab" >
                        <div class="back"><span class="backtext">Back</span></div>
                     </button>
                     <button type="submit" id="save" class="yes-button" disabled="true">
                        <div class="yes"><span class="yestext">Update</span></div>
                     </button>
                  </div>
                  {{ Form::close() }}

               </section>
            </div>
         </div>

      {{-- </div> --}}
      @endif
      @if(isset($candidateJob))
       <div id="enrollment" class="container-fluid tab-pane fade">
            <br>
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Enrollment </div>
               <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                  {{-- ** Please note, your documents will be uploaded to our secure server. Your privacy and security will be protected using advanced encryption. --}}
                  <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents(Please use files that are 3 MB or under)</label>
                  {{ Form::open(array('url'=>'#','id'=>'enrollment-form','class'=>'form-horizontal', 'method'=> 'POST', 'files'=>'true','data-action'=>'edit', 'enctype'=>'multipart/form-data')) }}
                  {{csrf_field()}}
                  <input type="hidden" name="document_file_id[]" value="">
                  <input type="hidden" name="document_id[]" value="">
                  <input type="hidden" name="candidate_id" value="{{$candidateJob->candidate_id}}">

                  {{ Form::close() }}
                    @include('recruitment::candidate.application.document', ['document' =>$documents['enrollment']])
                  <div class="text-center margin-bottom-5">
                     <button class="yes-button backtab" >
                        <div class="back"><span class="backtext">Back</span></div>
                     </button>
                     <button type="submit"  class="yes-button saveEnrollment" disabled="true">
                        <div class="yes"><span class="yestext">Update</span></div>
                     </button>
                  </div>
               </section>
            </div>
         </div>
      @endif
       @if(isset($candidateJob))
       <div id="securityclearance" class="container-fluid tab-pane fade">
            <br>
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Security Clearance </div>
               <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                  {{-- ** Please note, your documents will be uploaded to our secure server. Your privacy and security will be protected using advanced encryption. --}}
                  <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents(Please use files that are 3 MB or under)</label>
                  {{ Form::open(array('url'=>'#','id'=>'securityclearance-form','class'=>'form-horizontal', 'method'=> 'POST', 'files'=>'true','data-action'=>'edit', 'enctype'=>'multipart/form-data')) }}
                  {{csrf_field()}}
                  <input type="hidden" name="document_file_id[]" value="">
                  <input type="hidden" name="document_id[]" value="">
                  <input type="hidden" name="candidate_id" value="{{$candidateJob->candidate_id}}">

                  {{ Form::close() }}
                     @include('recruitment::candidate.application.document', ['document' =>$documents['securityclearance']])
                  <div class="text-center margin-bottom-5">
                     <button class="yes-button backtab" >
                        <div class="back"><span class="backtext">Back</span></div>
                     </button>
                     <button type="submit" class="yes-button saveEnrollment" disabled="true">
                        <div class="yes"><span class="yestext">Update</span></div>
                     </button>
                  </div>
               </section>
            </div>
         </div>
      @endif
      @if(isset($candidateJob))
       <div id="taxforms" class="container-fluid tab-pane fade">
            <br>
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Tax forms </div>
               <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                  {{-- ** Please note, your documents will be uploaded to our secure server. Your privacy and security will be protected using advanced encryption. --}}
                  <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Copy of the required documents(Please use files that are 3 MB or under)</label>
                  {{ Form::open(array('url'=>'#','id'=>'taxforms-form','class'=>'form-horizontal', 'method'=> 'POST', 'files'=>'true','data-action'=>'edit', 'enctype'=>'multipart/form-data')) }}
                  {{csrf_field()}}
                  <input type="hidden" name="document_file_id[]" value="">
                  <input type="hidden" name="document_id[]" value="">
                  <input type="hidden" name="candidate_id" value="{{$candidateJob->candidate_id}}">

                  {{ Form::close() }}
                     @include('recruitment::candidate.application.document', ['document' =>$documents['taxforms']])
                  <div class="text-center margin-bottom-5">
                     <button class="yes-button backtab" >
                        <div class="back"><span class="backtext">Back</span></div>
                     </button>
                     <button type="submit"  class="yes-button saveEnrollment" disabled="true">
                        <div class="yes"><span class="yestext">Update</span></div>
                     </button>
                  </div>
               </section>
            </div>
         </div>
      @endif
      @if(isset($candidateJob))
         <div id="uniform" class="container-fluid tab-pane fade">
            <br>
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head"> Candidate Screening Form - Uniform </div>
               <section class="candidate full-width">
                  {{ Form::open(array('id'=>'screening-uniform-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                  {{csrf_field()}}
                  @include('recruitment::candidate.application.uniform_measurement')
                  <div class="text-center margin-bottom-5">
                     <button type="button" class="yes-button backtab" >
                        <div class="back"><span class="backtext">Back</span></div>
                     </button>
                     <button type="submit" id="true" class="yes-button" disabled="true">
                        <div class="yes"><span class="yestext">Update</span></div>
                     </button>
                  </div>
                  {{ Form::close() }}
               </section>
            </div>
         </div>
         @endif
   </div>
</div>
 @include('recruitment::job-application.partials.scripts')
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

                   window.location = "{{ route('recruitment.candidate.summary') }}";
               });

   });
   var items=[];
   var form_arr=[];
   $(".aws_upload_attachment_form").submit(function(e) {
           e.preventDefault();
               // $('#aws_upload_attachment_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
               the_file = $(this).find("#attachment-file")[0].files[0];
               var filename = 'attachement/'+Date.now() + '.' + the_file.name.split('.').pop();
               $("#attachment-key").val(filename);
               $("#attachment-type").val(the_file.type);
               var results = $(this).find("#video-results");
               var fileid = 'attachment_file_id';
               var success = 'attachement-success';
   $(this).find("input[name=key]").val(filename);
   $(this).find("input[name=Content-Type]").val(the_file.type);
     var file_attach_id=$(this).find('input[name=file]').data('aid')
   var post_url = $(this).attr("action"); //get form action url
   var form_data = new FormData(this); //Creates new FormData object
   $(this).find($(results)).show();
   var linkurl=$(this);
   $.ajax({
       url : post_url,
       type: 'post',
       datatype: 'xml',
       data : form_data,
       contentType: false,
       processData:false,
       xhr: function(){
           var xhr = $.ajaxSettings.xhr();
           if (xhr.upload){
               var progressbar = $("<div>", { style: "background:#607D8B;height:10px;margin:10px 0;" }).appendTo(results); //create progressbar
               xhr.upload.addEventListener('progress', function(event){
                       var percent = 0;
                       var position = event.loaded || event.position;
                       var total = event.total;
                       if (event.lengthComputable) {
                           percent = Math.ceil(position / total * 100);
                           progressbar.css("width", + percent +"%");
                       }
               }, true);
           }
           return xhr;
       }
   }).done(function(response){

       var url = $(response).find("Location").text(); //get file location
       console.log(url);
       var the_file_name = $(response).find("Key").text(); //get uploaded file name
     //  $("#results").html("<span>File has been uploaded, Here's your file <a href=" + url + ">" + the_file_name + "</a></span>"); //response

      var added=false;
     $.map(items, function(elementOfArray, indexInArray) {
     if (elementOfArray.id == file_attach_id) {
     elementOfArray.filename = the_file_name;
      added = true;
      }
      });
      if (!added) {
      items.push({id: file_attach_id, filename: the_file_name})
       }


       $(linkurl).find('input[name="'+fileid+'[]"]').val(the_file_name);
       $(linkurl).find('#'+success).show();
       $(linkurl).find('#video-results').hide();

   });
   });
           $('#save').on('click', function (e) {
           console.log(items)
           e.preventDefault();
           var $form = $('#attachment-form');
           // $('#aws_upload_attachment_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
           var formData = new FormData($('#attachment-form')[0]);
           // if(!$('#doc-success').is(':visible') && (!$('#uploaded-doc-file').is(':visible')) || (!$('#video-success').is(':visible') && (!$('#uploaded-video-file').is(':visible'))))
           // {
           // if(!$('#doc-success').is(':visible') && (!$('#uploaded-doc-file').is(':visible')  ))
           // $('#aws_upload_doc_form').find('.form-group').addClass('has-error').find('.help-block').text('Upload file');

           // if(!$('#video-success').is(':visible') && (!$('#uploaded-video-file').is(':visible')  ))
           // $('#aws_upload_video_form').find('.form-group').addClass('has-error').find('.help-block').text('Upload file');
           //  return false;
           // }
           url = "{{ route('recruitment.applyjob.attachment') }}";
           $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url: url,
               type: 'POST',
               data: {items:items},
               success: function (data) {
                   if (data.success) {
                        // showMessageIfUpdate($form);
                        current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
                        //current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
                        //current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
                        current_active_li.removeClass('active').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');

                        $('html, body').animate({
                            scrollTop: $("form").offset().top
                        }, 1000);

                   } else {
                       console.log(data);
                   }
               },
               fail: function (response) {
                   console.log(response);

               },
               error: function (xhr, textStatus, thrownError) {
                   console.log(xhr.responseJSON.errors);
                   associate_errors(xhr.responseJSON.errors, $form,true);
               },

           });
       });


       $(".document_upload_form").submit(function(e) {
           e.preventDefault();
               // $('#aws_upload_attachment_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
               the_file = $(this).find("#document-file")[0].files[0];
               var filename = 'document'+'/'+Date.now() + '.' + the_file.name.split('.').pop();
               var formid=$(this).parents(':eq(2)').siblings('form').attr('id');
               form_arr=formid
               $("#document-key").val(filename);
               $("#document-type").val(the_file.type);
               var results = $(this).find("#document-results");
               var fileid = 'document_file_id';
               var success = 'document-success';

   // the_file = this.elements['file'].files[0];
   // var filename = Date.now() + '.' + the_file.name.split('.').pop();
   console.log(filename)
   $(this).find("input[name=key]").val(filename);
   $(this).find("input[name=Content-Type]").val(the_file.type);
    var file_attach_id=$(this).find('input[name=file]').data('aid')
   var post_url = $(this).attr("action"); //get form action url
   var form_data = new FormData(this); //Creates new FormData object
   $(this).find($(results)).show();
   var linkurl=$(this);

   $.ajax({
       url : post_url,
       type: 'post',
       datatype: 'xml',
       data : form_data,
       contentType: false,
       processData:false,
       xhr: function(){
           var xhr = $.ajaxSettings.xhr();
           if (xhr.upload){
               var progressbar = $("<div>", { style: "background:#607D8B;height:10px;margin:10px 0;" }).appendTo(results); //create progressbar
               xhr.upload.addEventListener('progress', function(event){
                       var percent = 0;
                       var position = event.loaded || event.position;
                       var total = event.total;
                       if (event.lengthComputable) {
                           percent = Math.ceil(position / total * 100);
                           progressbar.css("width", + percent +"%");
                       }
               }, true);
           }
           return xhr;
       }
   }).done(function(response){

       var url = $(response).find("Location").text(); //get file location
       console.log(url);
       var the_file_name = $(response).find("Key").text(); //get uploaded file name
     //  $("#results").html("<span>File has been uploaded, Here's your file <a href=" + url + ">" + the_file_name + "</a></span>"); //response

      var added=false;
      var candidate_id=$('#'+formid+' input[name="candidate_id"]').val();
     $.map(items, function(elementOfArray, indexInArray) {
     if (elementOfArray.id == file_attach_id) {
     elementOfArray.filename = the_file_name;
      added = true;
      }
      });
      if (!added) {
      items.push({id: file_attach_id, filename: the_file_name,candidate_id:candidate_id})
       }


       $('#'+formid+' input[name="'+fileid+'[]"]').val(the_file_name);
       $('#'+formid+' #'+success).show();
        form_arr=items

       $(linkurl).find('input[name="'+fileid+'[]"]').val(the_file_name);
       $(linkurl).find('#'+success).show();
       $(linkurl).find('#document-results').hide();
   });
   });

        $('.saveEnrollment').on('click', function (e) {
           e.preventDefault();
           formid=$(this).parent().siblings('form').attr('id');
           var $form = $('#'+formid);
           url = "{{ route('recruitment.document.store') }}";
           $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url: url,
               type: 'POST',
               data: {items:form_arr},
               success: function (data) {
                   if (data.success) {
                        current_active_li = $('a[href="#' + $form.parents('.tab-pane').prop('id') + '"]').parents('li');
                        //current_active_li.nextAll().removeClass('active').removeClass('success').find('a').addClass('disabled');
                        //current_active_li.removeClass('active').addClass('success').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');
                        current_active_li.removeClass('active').next('li').addClass('active').find('a').removeClass('disabled').trigger('click');

                        $('html, body').animate({
                            scrollTop: $("form").offset().top
                        }, 1000);

                   } else {
                       console.log(data);
                   }
               },
               fail: function (response) {
                   console.log(response);

               },
               error: function (xhr, textStatus, thrownError) {
                   console.log(xhr.responseJSON.errors);
                   associate_errors(xhr.responseJSON.errors, $form,true);
               },

           });
       });

</script>
@stop
