@extends('layouts.app')
@section('content')
<div class="table_title">
	@if(isset($postorder_data))
    <h4>Edit Post Order</h4>
    @else
    <h4>Add Post Order</h4>
    @endif
</div>
<div id="post-order" class="container candidate-screen"><br>
   {{ Form::open(array('url'=>'#','id'=>'post-order-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
   {{csrf_field()}}
   {{ Form::hidden('id', null) }}
   {{ Form::hidden('customer_id', $customer_id) }}
   <div class="">
       <div class="form-group row" id="postOrderTopic">
           <label for="title" class="col-sm-4 control-label">Topic <span class="mandatory">*</span></label>
           <div class="col-sm-8">
               <select class="form-control select2" name="postOrderTopic" value="">
                   <option selected value="">Please Select</option>
                   @foreach($postOrderTopics as $id=>$eachPostOrderTopics)
                   <option value="{{$id}}" @if(isset($postorder_data) && $postorder_data->topic_id == $id ) {{ 'selected' }} @endif>{{$eachPostOrderTopics}}</option>
                   @endforeach
               </select>
               <small class="help-block"></small>
           </div>
       </div>
       <div class="form-group row" id="postOrderGroup">
           <label for="group" class="col-sm-4 control-label">Group <span class="mandatory">*</span></label>
           <div class="col-sm-8">
               <select class="form-control select2" name="postOrderGroup" value="">
                   <option selected value="">Please Select</option>
                   @foreach($postOrderGroups as $id=>$eachPostOrderGroups)
                   <option value="{{$id}}" @if(isset($postorder_data) && $postorder_data->group_id == $id ) {{ 'selected' }} @endif>{{$eachPostOrderGroups}}</option>
                   @endforeach
               </select>
               <small class="help-block"></small>
           </div>
       </div>
       <div class="form-group row" id="project">
           <label for="priority_id" class="col-sm-4 control-label">Client Name <span class="mandatory">*</span></label>
           <div class="col-sm-8">
               <select class="form-control option-adjust select2" id="customer" name="project">
                   <option selected value="">Please Select</option>
                   @foreach($projectList as $projectid => $eachProject)
                   <option value="{{$projectid}}" @if(isset($postorder_data) && $projectid == $postorder_data->customer_id) selected @endif >{{$eachProject}}</option>
                   @endforeach
               </select>
               <small class="help-block"></small>
           </div>
       </div>

       <div class="form-group row" id="postOrderDescription">
           <label for="custom_subject" class="col-sm-4 control-label">Description<span class="mandatory">*</span></label>
           <div class="col-sm-8">
               <textarea class="form-control" rows="6" name="postOrderDescription" placeholder="Description">@if(isset($postorder_data)) {{$postorder_data->description}} @endif </textarea>
               <small class="help-block"></small>
           </div>
       </div>
       <div class="form-group row" id="employee_name">
           <label for="employee_name" class="col-sm-4 control-label">Uploaded By</label>
           <div class="col-sm-8">
               <span>@if(isset($postorder_data)) {{$postorder_data->getCreatedby->full_name}} @else {{$user_name}} @endif</span>
               <small class="help-block"></small>
           </div>
       </div>
       <div class="form-group row" id="attachment">
           <label class="col-sm-4 " >Document<span class="mandatory">*</span></label>
           <div class="col-sm-8">
               <div class="attachment_div">
                   <div class="attachement-control col-sm-12" id="attachment_div_po">
                       <div class="form-group row col-sm-12">
                           <label for="file_attachment" class="col-sm-4 control-label">Upload File</label>
                           <div class="col-sm-8" id="attachment_div">
                               <input type="file" class="form-control file_attachment scroll-clear"
                                      id="file_attachment"
                                      name="file_attachment"
                                      placeholder="Attachment"
                                      value=""
                                      accept=".docx,.doc,.odt">
                               <small class="help-block" id="attachment-validation"></small>
                           </div>
                       </div>
                       <div class="form-group row  col-sm-12 short_description_file_div" id="short_description">
                           <label for="short_description" class="col-sm-4 control-label">File Name</label>
                           <div class="col-sm-8">
                               <input type="text" class="form-control" name="short_description" id="short_descriptions">
                               <small class="help-block"></small>
                           </div>
                       </div>
                       <div class="form-group row col-sm-12 file_info_btn_div">
                           <div class="col-sm-12">
                               <span class="upload-message" style="display: none"> Uploading...</span>
                               <input id="file_attachment_upload_btn"
                                      class="button btn btn-edit file_attachment_upload_btn"
                                      type="button" value="Upload">
                               <small class="help-block"></small>
                           </div>
                       </div>
                   </div>
                   <div class="attachment-list col-sm-12" id="attachment_div_po">
                   </div>
               </div>
           </div>
       </div>
    </div>
    <div class="col-sm-8 pull-right">
       {{ Form::submit('Save', array('class'=>'button btn btn-edit','id'=>'mdl_save_change'))}}
       {{ Form::reset('Cancel', array('class'=>'btn btn-edit','onclick'=>'window.history.back();'))}}
    </div>
    {{ Form::close() }}
</div>
@endsection
@section('scripts')

<script>

    $(function () {
       var post_order_data = {!! json_encode($postorder_data) !!};  
       
       if(post_order_data!=null)
       {
        $('input[name="id"]').val(post_order_data['id'])
        console.log(post_order_data['attachment_details'])
        onFileUploadSuccess(post_order_data['attachment_details']);
    }    
    $('.select2').select2();
});
    var filesize = 50;
    window.attachment.moduleName = 'post-order'
    window.attachment.extensionArr = Array('doc', 'docx', 'odt');
    window.attachment.fileCount = 1;



    $("#file_attachment").on('change',function(){$("#file_div .help-block").text("")})

        // $("#incidents-table").on("click", ".edit", function (e) {
        //     var id = $(this).data('id');
        //     var description = $(this).data('description');
        //     $("#incidentStatusModal").modal();
        //     $("#status-subject").val(description);
        //     $("#incident-id").val(id);
        // });

        /*Post-Order - Save - Start*/
        $('#post-order-form').submit(function (e) {
            e.preventDefault();
            if($('#file_attachment').val() != '' || ($('#file_attachment').val() == '' && $('#short_description').val() != '')){
                $('#attachment-validation').addClass('has-error').text('Please upload the file');
                return false;
            }
            var $form = $(this);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            url = '{{ route('post-order.create')}}';
            var formData = new FormData($('#post-order-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    console.log(data);
                    if (data.success) {
                        swal({ 
                          title: "Success",
                          text:  data.message,
                          type: "success" 
                      },
                      function(){
                        window.location.href =  "{{route('post-order.view')}}";
                    });
                        
                    } else {
                        swal("Oops", "Could not create", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });


    </script>
    @include('contracts::partials.fileupload-script')
    @endsection
