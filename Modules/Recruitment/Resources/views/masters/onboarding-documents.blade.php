@extends('adminlte::page')
@section('title', 'Onboarding Documents')
@section('content_header')
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Aws S3 Direct File Uploader</title>

<h1>Onboarding Documents</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new"  data-title="Add New Onboarding Document">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="onboarding-documents-table">
    <thead>
        <tr>
            <th>#</th>
            <th width="70%">Document Name</th>
            <th>Created Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myAwsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Question</h4>
            </div>

            {{ Form::open(array('url'=>'#','id'=>'onboarding-documents-form','class'=>'form-horizontal', 'method'=> 'POST', 'enctype'=>'multipart/form-data')) }} {{ Form::hidden('id',null) }}
             <div class="modal-body">

             <div class="form-group row" id="document_name">
             <label for="document_name" class="col-sm-12">Document Name</label>
             <div class="col-sm-10">
             {{ Form::text('document_name',null,array('class' => 'form-control', 'Placeholder'=>'Document Name')) }}
             <small class="help-block"></small>
             </div>
             </div>



           {{--  <div class="form-group row" id="process_tab_id">
             <label for="process_tab_id" class="col-sm-12">Document Category</label>
             <div class="col-sm-10">
             {{ Form::select('process_tab_id',[null=>'Please Select']+$document_category, old('process_tab_id'),array('class' => 'form-control')) }}
             <small class="help-block"></small>
             </div>
             </div> --}}
             <input type="hidden" name="video_file_id" value="">
             <input type="hidden" name="doc_file_id" value="">
             <input type="hidden" name="video_id" value="">
             <input type="hidden" name="doc_id" value="">

             {{ Form::close() }}




            <div class="form-group row" id="process_tab_id">
             <label for="process_tab_id" class="col-sm-12">Instruction Video</label>
             <div class="col-sm-12">
             <form action="https://{{$my_bucket}}.s3-{{$region}}.amazonaws.com" method="post" id="aws_upload_video_form"  enctype="multipart/form-data">
                <input type="hidden" name="acl" value="private">
                <input type="hidden" name="success_action_status" value="201">
                <input type="hidden" name="policy" value="{{$policybase64}}">
                <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
                <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
                <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
                <input type="hidden" name="X-amz-signature" value="{{$signature}}">
                <input type="hidden" name="key" id="video-key" value="">
                <input type="hidden" name="Content-Type" id="video-type" value="">
                <div id="upload-video-file" class="row form-group">
                <div class="col-sm-7">
                <input class="form-control" type="file" id="video-file" name="file" />
                <span class="help-block"></span>
                </div>
                <div class="col-sm-2">
                <input class="button btn btn-primary blue form-control video-button" type="submit" value="Upload" />
                </div>
                <div class="col-sm-2" id="video-success" style="display:none;">
                <span  style="color: #35af3f;">Uploaded Successfully</span>
                </div>
                </div>
                <div id="uploaded-video-file" style="display: none;" class="row">
                <div class="col-sm-5">
                <span id="video-file-name"></span>
                </div>
                <div class="col-sm-2" id="video-success">
                <a href="" target="_blank">Download <i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i></a>
                </div>
                <div class="col-sm-2">
                <input class="button btn btn-primary blue form-control" id="video-remove" type="button" value="Remove" />
                </div>
                </div>
                <div class="col-sm-7" id="video-results"><!-- server response here --></div>
                 </form>
             <span class="help-block"></span>
             </div>
             </div>


             <div class="form-group row" id="process_tab_id">
             <label for="process_tab_id" class="col-sm-12">Document</label>
             <div class="col-sm-12">
             <form action="https://{{$my_bucket}}.s3-{{$region}}.amazonaws.com" method="post" id="aws_upload_doc_form"  enctype="multipart/form-data">
                <input type="hidden" name="acl" value="private">
                <input type="hidden" name="success_action_status" value="201">
                <input type="hidden" name="policy" value="{{$policybase64}}">
                <input type="hidden" name="X-amz-credential" value="{{$access_key}}/{{$short_date}}/{{$region}}/s3/aws4_request">
                <input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256">
                <input type="hidden" name="X-amz-date" value="{{$iso_date}}">
                <input type="hidden" name="X-amz-expires" value="{{$presigned_url_expiry}}">
                <input type="hidden" name="X-amz-signature" value="{{$signature}}">
                <input type="hidden" name="key" id="doc-key" value="">
                <input type="hidden" name="Content-Type" id="doc-type" value="">
                <div id="upload-doc-file" class="row form-group">
                <div class="col-sm-7">
                <input class="form-control" type="file" id="doc-file" name="file" />
                <span class="help-block"></span>
                </div>
                <div class="col-sm-2">
                <input class="button btn btn-primary blue form-control document-button" type="submit" value="Upload" />
                </div>
                <div class="col-sm-2" id="doc-success" style="display:none;">
                <span  style="color: #35af3f;">Uploaded Successfully</span>
                </div>
                 </div>
                 <div id="uploaded-doc-file" style="display: none;" class="row">
                <div class="col-sm-5">
                <span id="doc-file-name"></span>
                </div>
                <div class="col-sm-2" id="doc-success">
                <a href="" target="_blank">Download<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i></a>
                </div>
                <div class="col-sm-2">
                <input class="button btn btn-primary blue form-control" id="doc-remove" type="button" value="Remove" />
                </div>
                </div>
                <div class="col-sm-7" id="doc-results"><!-- server response here --></div>
                 </form>
             <small class="help-block"></small>
             </div>
             </div>


          </div>

        <div class="modal-footer">
<button id="save" class="button btn btn-primary blue">Save</button>
<button class="button btn btn-primary blue" data-dismiss="modal" aria-hidden="true">Cancel</button>
       </div>
</div>
</div>
</div>




@stop
@section('js')
<script>
    $(function () {



        $('.add-new').on('click', function () {
            var title = $(this).data('title');
            $("#myAwsModal").modal();
            $('#is_valid').hide();

            $('#myAwsModal #video-results').empty();
            $('#myAwsModal #doc-results').empty();
            $('#myAwsModal #uploaded-video-file').hide();
            $('#myAwsModal #upload-video-file').show();
            $('#myAwsModal #uploaded-doc-file').hide();
            $('#myAwsModal #upload-doc-file').show();
            $('#myAwsModal input[name="document_name"]').val('');
            $('#myAwsModal input[name="video_file_id"]').val('');
            $('#myAwsModal input[name="doc_file_id"]').val('');
            $('#myAwsModal .modal-title').text(title);
            $('#myAwsModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        });


        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#onboarding-documents-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Services');
                    }
                }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('recruitment.onboarding-documents.list') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [[ 1, "desc" ]],
                lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable: false
                },
                {
                    data: 'document_name',
                    name: 'document_name'
                },


                {
                data: 'created_at',
                name: 'created_at'
                },
                {
                data: null,
                orderable: false,
                render: function (o) {
                 var actions = "";
                @can('edit_masters')
                actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' +o.id + '></a>';
                @endcan
                 @can('lookup-remove-entries')
                 actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' +o.id + '></a>';
                 @endcan
                 return actions;
             },
         }
         ]
     });
        } catch (e) {
            console.log(e.stack);
        }


        $('#save').on('click', function (e) {
            e.preventDefault();
            var $form = $('#onboarding-documents-form');
            $('#aws_upload_doc_form,#aws_upload_video_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            var formData = new FormData($('#onboarding-documents-form')[0]);
            if(!$('#doc-success').is(':visible') && (!$('#uploaded-doc-file').is(':visible')) || (!$('#video-success').is(':visible') && (!$('#uploaded-video-file').is(':visible'))))
            {
            if(!$('#doc-success').is(':visible') && (!$('#uploaded-doc-file').is(':visible')  ))
            $('#aws_upload_doc_form').find('.form-group').addClass('has-error').find('.help-block').text('Add file and click upload button');

            if(!$('#video-success').is(':visible') && (!$('#uploaded-video-file').is(':visible')  ))
            $('#aws_upload_video_form').find('.form-group').addClass('has-error').find('.help-block').text('Add file and click upload button');
             return false;
            }
            url = "{{ route('recruitment.onboarding-documents.store') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                         swal("Saved", "Onboarding Documents has been created successfully", "success");

                          $('#myAwsModal form').trigger('reset');
                          $("#myAwsModal").modal('hide');
                         table.ajax.reload();
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
                contentType: false,
                processData: false,
            });
        });

        $("#aws_upload_doc_form, #aws_upload_video_form").submit(function(e) {
            e.preventDefault();
            if($(this).closest("form").attr("id") == 'aws_upload_video_form'){
                $('#aws_upload_video_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                the_file = $("#video-file")[0].files[0];
                var filename = 'instruction_document/'+Date.now() + '.' + the_file.name.split('.').pop();
	            $("#video-key").val(filename);
	            $("#video-type").val(the_file.type);
                var results =  'video-results';
                var fileid = 'video_file_id';
                var success = 'video-success';

            }else{
                $('#aws_upload_doc_form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                the_file = $("#doc-file")[0].files[0];
                var filename = 'instruction_document/'+Date.now() + '.' + the_file.name.split('.').pop();
	            $("#doc-key").val(filename);
	            $("#doc-type").val(the_file.type);
                var results =  'doc-results';
                var fileid = 'doc_file_id';
                var success = 'doc-success';

            }
/*	the_file = this.elements['file'].files[0];
	var filename = Date.now() + '.' + the_file.name.split('.').pop();
	$(this).find("input[name=key]").val(filename);
	$(this).find("input[name=Content-Type]").val(the_file.type); */


    var post_url = $(this).attr("action"); //get form action url
    var form_data = new FormData(this); //Creates new FormData object
    $('#myAwsModal #'+results).show();
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
                $("#"+results).empty();
				var progressbar = $("<div>", { style: "background:#607D8B;height:10px;margin:10px 0;" }).appendTo("#"+results); //create progressbar
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
        $('#myAwsModal input[name="'+fileid+'"]').val(the_file_name);
        $('#myAwsModal #'+success).show();
    });
});



        /* Service Edit - Start*/
        $("#onboarding-documents-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            $('#myAwsModal #video-results').hide();
            $('#myAwsModal #doc-results').hide();

            var url = '{{ route("recruitment.onboarding-documents.single",":id") }}';
            var url = url.replace(':id', id);
            $('#onboarding-documents-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        $('#myAwsModal input[name="id"]').val(data.id)
                        $('#myAwsModal input[name="document_name"]').val(data.document_name);

                        if(data.attachments.length > 0){
                        let attachment_str = "";
                        for(let i in data.attachments){
                            if(data.attachments[i].file_type ==1 ){
                                $('#myAwsModal #video-results').empty();
                                $('#myAwsModal #upload-video-file').hide();
                                $('#myAwsModal #uploaded-video-file').show();
                                $('#myAwsModal #video-success').show();
                                $('#myAwsModal span[id="video-file-name"]').text(data.attachments[i].file_name);
                                $('#myAwsModal input[name="video_id"]').val(data.attachments[i].id);
                                $('#myAwsModal input[name="video_file_id"]').val(data.attachments[i].file_name);
                                $('#myAwsModal #video-success a').attr("href",data.attachments[i].file_url);
                            }else if(data.attachments[i].file_type ==2 ){
                                $('#myAwsModal #doc-results').empty();
                                $('#myAwsModal #upload-doc-file').hide();
                                $('#myAwsModal #uploaded-doc-file').show();
                                $('#myAwsModal #doc-success').show();
                                $('#myAwsModal span[id="doc-file-name"]').text(data.attachments[i].file_name);
                                $('#myAwsModal input[name="doc_id"]').val(data.attachments[i].id);
                                $('#myAwsModal input[name="doc_file_id"]').val(data.attachments[i].file_name);
                                 $('#myAwsModal #doc-success a').attr("href",data.attachments[i].file_url);


                            }
                           // attachment_str += '<a title="Download attachment" href="' + data.attachment_arr[i].url + '" target="_blank">'+data.attachment_arr[i].name+'</a> <br><br>';
                        }
                    }





                      //  $('#myAwsModal select[name="process_tab_id"] option[value="'+data.process_tab_id+'"]').prop('selected',true);
                        $("#myAwsModal").modal();
                        $('#myAwsModal .modal-title').text("Edit Onboarding Documents")
                    } else {
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Service Edit - End*/

        /* Service Delete  - Start */
        $('#onboarding-documents-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.onboarding-documents.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Onboarding Documents tab has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Service Delete  - End */



    });

    $("#video-remove,#doc-remove").on("click", function (e) {
        if(this.id == 'video-remove'){
         $('#myAwsModal #uploaded-video-file').hide();
         $('#myAwsModal #upload-video-file').show();
         $('#myAwsModal #video-success').hide();
        
        }else if(this.id == 'doc-remove'){
         $('#myAwsModal #uploaded-doc-file').hide();
         $('#myAwsModal #upload-doc-file').show();
        $('#myAwsModal #doc-success').hide();
        }
    });

        /* Reset Modal value on hide - Start */
         $('.modal').on('hidden.bs.modal', function() {
            $('#myAwsModal form').trigger('reset');
         });
        /* Reset Modal value on hide - End */
              $('.add-new').click(function () {
                $("#myAwsModal").modal();
                $('#myAwsModal form').trigger('reset');
                $("#myAwsModal #aws_upload_doc_form").find('#doc-success,#doc-results').css('display','none')
                 $("#myAwsModal #aws_upload_video_form").find('#video-success,#video-results').css('display','none')
                $('#myAwsModal').find('input[name=id]').val('');
            });

</script>
<style type="text/css">
    .button-disabled{
        pointer-events: none;
    }
</style>
@stop
