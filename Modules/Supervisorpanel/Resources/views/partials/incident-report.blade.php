<div class="table_title">
    <h3>Incident Reports for {{$payperiod_name}}</h3>
</div>
</section>
<section  class="content">
<!--        <div class="add-new" onclick="addnew()">Add <span class="add-new-label">New</span></div>-->
    @can('create-incident-report')
   {{--  <div class="form-group row">
        <div class="col-sm-12 col-xs-12 text-align-right text-left-mob">
            <a title="Add another" href="javascript:;" class="btn cancel ico-btn incident_add_button" data-toggle="modal" data-target="#incidentModal">
                <i class="fa fa-plus" aria-hidden="true"></i>Add Incident</a>
        </div>
    </div> --}}
    @endcan

    <table class="table table-bordered incident-table" id="incidents-table">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>#</th>
                <th>Title</th>
                <th>Subject</th>
                <th>Incident Report</th>
                <th>Attachment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Time Stamp</th>
                <th>Guard</th>
                <th>Employee Number</th>
                <th>Notes</th>
                 <th>Priority</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>



    <!-- Incident Add Modal - Start -->
    <div class="modal fade" id="incidentModal" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Incident Report</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

                </div>
                {{ Form::open(array('url'=>'#','id'=>'incident-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{csrf_field()}}
                {{ Form::hidden('id', null) }}
                {{ Form::hidden('customer_id', $customer_id) }}
                {{ Form::hidden('payperiod_id', $payperiod_id) }}
                <div class="modal-body">

                    <ul>
                    </ul>
                    <!-- <div class="form-group row" id="description">
                        <label for="description" class="col-sm-4 control-label">Description <span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <textarea id="modal-description" class="form-control" name="description" placeholder="Description" value="" required="required"></textarea>
                            <small class="help-block"></small>
                        </div>
                    </div> -->
                    <div class="form-group row" id="title">
                        <label for="title" class="col-sm-4 control-label">Title <span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="title" placeholder="Title" >
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="subject">
                        <label for="subject" class="col-sm-4 control-label">Subject <span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="subject" placeholder="Subject" value="">
                                <option selected value="">Please Select</option>
                                @foreach($subject_list as $id=>$each_subject)
                                    <option value="{{$id}}">{{$each_subject}}</option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div>
                   <div class="form-group row" id="priority_id">
                        <label for="priority_id" class="col-sm-4 control-label">Priority <span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <select class="form-control" id="priority" name="priority_id" placeholder="Priority" value="">
                                <option selected value="">Please Select</option>
                                @foreach($priorityLookUpRepository as $id=>$priority)
                                    <option value="{{$id}}">{{$priority}}</option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="custom_subject" style="display:none">
                        <label for="custom_subject" class="col-sm-4 control-label">Custom Subject<span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="custom_subject" placeholder="Custom Subject"></textarea>
                            <small class="help-block"></small>
                        </div>
                    </div>

                      <div class="form-group row" id="time_of_day">
                        <label for="subject" class="col-sm-4 control-label">Occurance
                            <span class="mandatory">*</span>
                        </label>

                        <div class="col-sm-8">
                            <div class="form-group row">
                                <label class="col-sm-4">During the</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="time_of_day" placeholder="Subject" value="" required>
                                            <option value="Morning">Morning</option>
                                            <option value="Afternoon">Afternoon</option>
                                             <option value="Night">Night</option>
                                    </select>
                                    <small class="help-block"></small>
                                </div>
                                <label class="col-sm-4">Shift</label>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-1">on</label>
                                <div class="col-sm-3"  id="date">
                                    <input type="number" placeholder="Date" name="date" class="text-line form-control" value="{{ date('j', strtotime($current_date)) }}" min="1" max="31">
                                      <small class="help-block"></small>
                                </div>
                                <div class="col-sm-4" id="month">
                                    <select  class="form-control" id="monthvalue" name="month">
                                        <option value=""> Select Month </option>
                                        @foreach($month_array as  $id=>$month)
                                            <option value={{$id}} {{ (date('n', strtotime($current_date)) == $id ? "selected":"") }}> {{ $month}}</option>
                                        @endforeach
                                    </select>
                                     <small class="help-block"></small>
                                </div>
                                <div class="col-sm-4" id="yearvalue">
                                    <input type="text" class="text-line form-control" name="yearvalue" placeholder="YYYY" value="{{ date('Y', strtotime($current_date)) }}">
                                     <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4">At Approximately</label>
                                <div class="col-sm-6"  id="time">
                                <input type="text" class="text-line form-control" placeholder="Time" name="time" value="{{ date('H:i', strtotime($current_date)) }}">
                                 <small class="help-block"></small>
                               </div>
                               <div class="col-sm-1">
                                <label class="col-sm-1">Hours</label>
                               </div>
                            </div>

                            <div class="form-group row">
                               <div class="col-sm-12"><label>At Site</label>
                                <b>({{$sitename->project_number}}) {{$sitename->client_name}}, {{$sitename->address}}, {{$sitename->city}}</b>
                            </div>
                        </div>
                   </div>
               </div>

                    <div class="form-group row" id="incident_detail">
                        <label for="custom_subject" class="col-sm-4 control-label">Explain the incident in detail<span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="6" name="incident_detail" placeholder="Incident Details"></textarea>
                            <small class="help-block"></small>
                        </div>
                    </div>


                     <div class="form-group row" id="upload_incident_report">
                        <label for="upload_incident_report" class="col-sm-4 control-label">Do you want to manually upload Incident Report<span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                           <div id="filter">
                      <label><input type="radio" name="upload_incident_report" value="1" checked>Yes</label>
                      <label><input type="radio" name="upload_incident_report" value="0">No</label>
                       </div>
                            <small class="help-block"></small>
                        </div>
                    </div>


                    <div class="form-group row" id="report_attachment">
                        <label for="report_attachment" class="col-sm-4 control-label">Upload Incident Report</label>
                        <div class="col-sm-8" id="file_div">
                            <input type="file" class="form-control scroll-clear" name="report_attachment" placeholder="Attachment" id="report_attachment" value="">
                            <small class="help-block"></small>
                        </div>
                    </div>

                  {{--   <div class="form-group row" id="status">
                        <label for="status" class="col-sm-4 control-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="status" placeholder="Status" value="">
                                @foreach($status_list as $each_status)
                                    @if(strtolower($each_status->status) == "open")
                                    <option value="{{$each_status->id}}">{{$each_status->status}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div> --}}
                    <div class="form-group row" id="alternate_email">
                        <label for="status_date" class="col-sm-4 control-label">Date</label>
                        <div class="col-sm-8">
                            <span>{{$formatted_date}}</span>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="employee_name">
                        <label for="employee_name" class="col-sm-4 control-label">Employee Name </label>
                        <div class="col-sm-8">
                            <span>{{$user_name}}</span>
                            <small class="help-block"></small>
                        </div>
                    </div>

                     <div class="form-group row" id="attachement">
                        <label for="attachement" class="col-sm-4 control-label">Add Attachments</label>
                        <div class="col-sm-8">
                          <span id="additional-attachment" style="cursor:pointer"><i class="fa fa-plus"></i>Add Attachment</span>
                            <small class="help-block"></small>
                        </div>
                    </div>
                   {{--  <div class="form-group row" id="notes">
                        <label for="notes" class="col-sm-4 control-label">Notes</label>
                        <div class="col-sm-8">
                            <textarea class="form-control phone" name="notes"></textarea>
                            <small class="help-block"></small>
                        </div>
                    </div> --}}
                    <div id="attachment">
                        <div class="form-group row">
                             <div class="col-sm-12"><h5 class="modal-title" id="myModalLabel">Attachments</h5></div>
                         </div>
                        <div class="attachment-list">
                        </div>
                        <div class="attachment_div">
                            <div class="form-group row col-sm-12">
                                <label for="file_attachment" class="col-sm-4 control-label">Upload File</label>
                                <div class="col-sm-8" id="attachment_div">
                                    <input type="file" class="form-control file_attachment scroll-clear" id="file_attachment" name="file_attachment" placeholder="Attachment" value="">
                                    <small class="help-block" id="attachment-validation"></small>
                                </div>
                            </div>
                            <div class="form-group row  col-sm-12 short_description_file_div" id="short_description">
                                <label for="short_description" class="col-sm-4 control-label">Short Description</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="short_description" id="short_descriptions">
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="form-group row col-sm-12 file_info_btn_div">
                                <div class="col-sm-4">
                                    <span class="upload-message" style="display: none"> Uploading...</span>
                                    <input id="file_attachment_upload_btn" class="button btn btn-edit file_attachment_upload_btn" type="button" value="Upload">
                                    <small class="help-block"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-edit','id'=>'mdl_save_change'))}}
                    <button class="btn btn-edit" data-dismiss="modal" aria-hidden="true" onclick="$('#incident-form').trigger('reset');">Cancel</button>
                </div>
                {{ Form::close() }}
            </div>
                </div>
            </div>
    <!-- Incident Add Modal - End -->


    <!-- Status Change Modal - Start -->
    <div class="modal fade" id="incidentStatusModal" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myEditModalLabel" aria-hidden="true" data-focus-on="input:first">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Incident Status Update</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                {{ Form::open(array('url'=>'#','id'=>'incident-status-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{csrf_field()}}
                {{ Form::hidden('id', null, array('id' => 'incident-id')) }}
                {{ Form::hidden('customer_id', $customer_id) }}
                {{ Form::hidden('payperiod_id', $payperiod_id) }}
                <div class="modal-body">
                    <ul>
                    </ul>
                    <!-- <div class="form-group row" id="description">
                        <label for="description" class="col-sm-4 control-label">Description <span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <textarea id="status-description" class="form-control" name="description" placeholder="Description" value="" disabled></textarea>
                            <small class="help-block"></small>
                        </div>
                    </div> -->
                    <div class="form-group row" id="subject">
                        <label for="subject" class="col-sm-4 control-label">Subject <span class="mandatory">*</span></label>
                        <div class="col-sm-8">
                            <input class="form-control" type='text' id='status-subject' readonly>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="status">
                        <label for="status" class="col-sm-4 control-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="status" placeholder="Status" value="" id="status-change">
                                @foreach($status_list as $each_status)
                                   <option value="{{$each_status->id}}" @if($each_status->id == 2) selected @endif>{{$each_status->status}}</option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="alternate_email">
                        <label for="status_date" class="col-sm-4 control-label">Date</label>
                        <div class="col-sm-8">
                            <span>{{$formatted_date}}</span>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="employee_name">
                        <label for="employee_name" class="col-sm-4 control-label">Employee Name </label>
                        <div class="col-sm-8">
                            <span>{{$user_name}}</span>
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group row" id="notes">
                        <label for="notes" class="col-sm-4 control-label">Notes</label>
                        <div class="col-sm-8">
                            <textarea class="form-control phone" name="notes"></textarea>
                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'status button btn btn-edit','id'=>'mdl_status_save_change'))}}
                    <button class="btn btn-edit" data-dismiss="modal" aria-hidden="true" onclick="$('#incident-status-form').trigger('reset');">Cancel</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Status Change Modal - End -->
</section>

<script>
    $(function() {
        let attachment_div =  $(".attachment_div")[0];
        $("#add_attachment_btn").click(function(){
            $(".attachment-list").append(attachment_div.outerHTML);
            if($(".attachment_div").length > 1){
                $(".attachment_div:last .file_remove_btn_div").show();
            }
        });

        /*Additional attachments - Start*/
        $("#attachment").hide();
        $('#additional-attachment').on('click',function(){
            $("#attachment").show();
            $('.modal').animate({
                scrollTop: $("#attachment").offset().top
            }, 2000);
        });
        $('#incidentModal').on('hidden.bs.modal', function () {
            $('.attachment_file').remove('');
            $('#file_div .has-error').removeClass('has-error').html('');
            $('#attachment-validation').removeClass('has-error').html('');
            $('#incidentModal').find("#attachment").hide();
            $(".attachment_div input#short_description,input#file_attachment").val('');
        });
        /*Additional attachments - End*/
    });

    $(document).on('click','.file_attachment_remove_btn',function(){
        $(this).closest('.attachment_file').remove();
    });

     // console.log($('input#report_attachment')[0])
     //    var allowedFileSize = 10;
     //    var report_attachment = $('input#report_attachment')[0];
     //    if(showFileSize(report_attachment) > allowedFileSize){
     //        $("#file_div .help-block").addClass("has-error").text("Maximum file size allowed is "+allowedFileSize+" MB");
    $(".file_attachment_upload_btn").click(function(){
        /*Validation*/
        var allowedFileSize = 20;
        var file_attachment = $('input#file_attachment')[0];
        if(showFileSize(file_attachment) > allowedFileSize){
            $("#attachment_div .help-block").addClass("has-error").text("Maximum file size allowed is "+allowedFileSize+" MB");
            return false;
        }
        if ($('#file_attachment').val() == ""){
            $('#attachment-validation').addClass('has-error').text('Please attach a file to upload');
            return false;
        }
        if ($('#short_descriptions').val() == ""){
            $('#short_descriptions').val(getFileName());
        }

        /*Validation*/
        var fileUploadData = new FormData();
        fileUploadData.append( 'customer_id', {{$customer_id}});
        fileUploadData.append( 'payperiod_id', {{$payperiod_id}});
        fileUploadData.append( 'file', $('#file_attachment')[0].files[0]);
        let url = "{{ route('fileupload',['module' => 'incident'])}}";
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: fileUploadData,
            success: function (data) {
                if (data.success) {
                    onFileUploadSuccess(data.data);
                    $('#attachment-validation').text('');
                     $('#short_descriptions').val('');
                } else {
                    console.log(data);
                    swal("Oops", "Could not upload", "warning");
                }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            error: function (xhr, textStatus, thrownError) {
                //associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });



        function onFileUploadSuccess(responseData){
            var file_display = '<input type="hidden" name="all_attachments[]" value="'+responseData.id+'">';
            file_display += '<div class="form-group row attachment_file"><div class="col-sm-8 control-label scroll-clear">'+$('#short_descriptions').val()+'</div>';
            file_display += '<input name="attachment_list[]" type="hidden" value=\''+JSON.stringify({id:responseData.id,name:$('#short_descriptions').val()})+'\'>';
            file_display += '<div class="col-sm-4 file_remove_btn_div">';
            file_display += '<input class="button btn btn-edit file_attachment_remove_btn" type="button" value="Remove"></div>';
            file_display += '<small class="help-block"></small>';
            file_display += '</div>';
            $(".attachment-list").append(file_display);
            $("#file_attachment").val('');
            $('#short_description').val('');
        }
    });
    var table = $('#incidents-table').DataTable({
        processing: false,
        serverSide: true,
        responsive: true,
        ajax: {
                "url":'{{ route('incident.list',["customer_id"=>$customer_id,"payperiod_id"=>$payperiod_id])}}',
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        columnDefs: [
            {'targets': 0, 'className': 'text-center'},
            {'targets': 1, 'className': 'nowrap text-center'},
            {'targets': 2, 'className': 'text-center'},
            {'width': '200px', 'targets': 3, 'className': 'nowrap text-center'},
            {'width': '75px', 'targets': 4, 'className': 'nowrap text-center'},
            {'width': '75px', 'targets': 5},
            {'width': '60px', 'targets': 6},
            {'width': '100px', 'targets': 7},
            {'width': '100px', 'targets': 8},
            {'width': '100px', 'targets': 9},
            {'targets': 10},
            {'targets': 11},
            {'targets': 12},
          ],
          "order": [[ 0, "desc" ]],
        fnRowCallback: function (nRow, aData, iDisplayIndex) {
            /* Append the grade to the default row class name */
            var status = aData['final_status'].toLowerCase();
            if (status == "open"){
                $(nRow).addClass('open');
            } else if(status == "in progress"){
                $(nRow).addClass('in_progress');
            } else {
                $(nRow).addClass('closed');
            }
        },
        columns: [
         {data: 'updated_at_time', name: 'updated_at_time',visible:false},
            {data: 'index', name: 'index',visible:false},
             {data: 'DT_RowIndex', name: '',sortable:false},
             {data: 'title', name: 'title'},
            {data: 'description', name: 'description', className: "description"},
            {data: null, name: 'incident_report',
                sortable: false,
                render: function (o) {
                    if(o.attachment != ""){
                        return '<a href="'+o.attachment+'" target="_blank" title="Incident Report"  class="fa fa-lg fa-list-alt cgl-font-blue"></a>';
                    } else{
                        return '';
                    }
                },
            },
            {
                data: null, name: 'attachment',
                sortable: false,
                render: function (data, type, row) {
                    if(data.attachment_arr.length > 0){
                        let attachment_str = "";
                        for(let i in data.attachment_arr){
                            attachment_str += '<a title="Download attachment" href="' + data.attachment_arr[i].url + '" target="_blank">'+data.attachment_arr[i].name+'</a> <br><br>';
                        }
                        return attachment_str;
                    }
                    else{
                        return '';
                    }
                },
            },
            {data: 'status.[ <br><br>].status', name: 'final_status'},
            {data: 'status.[ <br><br>].date', name: 'status.0.date', sortable: false,},
            {data: 'status.[ <br><br>].time', name: 'status.0.time', sortable: false,},
            {data: 'status.[ <br><br>].user_name', name: 'status.0.user_name', sortable: false,},
            {data: 'status.[ <br><br>].employee_no', name: 'status.0.employee_no', sortable: false,},
            {data: null,
                    render:function (data, type, row) {
                        //console.log(data.status[0].notes);
                        var notes_str = note ="";
                        for(var index in data.status){
//                                notes_str = notes_str+'<span class="show-btn">View note</span><span class = "notes" style="display:none">'+ data.status[index].notes+'</span><br/><br/>\r\n';
                            note = data.status[index].notes;
                            if(note.length > 35){
                                new_notes_str = '<span class="show-btn nowrap" onclick="$(this).hide();$(this).next().show();">'+ note.substr(0,40)+'..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a></span><span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">'+ note+'&nbsp;&nbsp;<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a></span><br/><br/>\r\n';
                            } else{
                                new_notes_str = '<span class="nowrap">'+note+'</span><br/><br/>\r\n';
                            }
                            notes_str += new_notes_str;
                            new_notes_str = '';
                        }
                        return notes_str;
                    },
                    name: 'status.0.notes', sortable: false,},
                     {data: 'priority', name: 'priority'},
            {
                data: null,
                sortable: false,
                render: function (o) {
                    var actions = '';
                    if(o.final_status.toLowerCase() !== "closed"){
                        @can('create-incident-report')
                         actions +=  '<a title="Update Progress" href="javascript:;" data-description="'+o.description+'" class="edit fa fa-list-ol cgl-font-blue" data-id=' + o.id + '></a>';
                        @endcan
                    }
                     return actions;

                },
            }
        ]
    });



//    function correctDatePickerZIndex() {
//        $(".gj-calendar").css("z-index", 2000);
//    }

    function showFileSize(file_name) {
        var input, file;
        var file_size = 0;
        if (!window.FileReader) {
            console.log("The file API isn't supported on this browser yet.");
            return;
        }
        input = $(file_name);
        if(input[0].files.length > 0){
            file_size = (input[0].files[0].size)/(1000000);
        }
        return file_size;
    }

    function getFileName() {
        var input, file;
        var file_size = 0;
        if (!window.FileReader) {
            console.log("The file API isn't supported on this browser yet.");
            return;
        }
        input = $('#file_attachment');
        var file_name = input[0].files[0].name
        return file_name;
    }

    $("#file_attachment").on('change',function(){$("#file_div .help-block").text("")})

    $("#incidents-table").on("click", ".edit", function (e) {
        var id = $(this).data('id');
        var description = $(this).data('description');
        $("#incidentStatusModal").modal();
        $("#status-subject").val(description);
        $("#incident-id").val(id);
    });

    /* Show/Hide Custom subject in add and edit modal- Start */
    $('#incidentModal').on('change','select[name="subject"]',function(){
        if($('#incidentModal').find('select[name="subject"] option:selected').text() == 'Others'){
            $('#incidentModal #custom_subject').show();
            $('#incidentModal textarea[name="custom_subject"]').prop('required',true);
        }else{
            $('#incidentModal #custom_subject').hide();
            $('#incidentModal #custom_subject').val();
            $('#incidentModal textarea[name="custom_subject"]').prop('required',false);
        }
    });
    /* Show/Hide Custom subject in add and edit modal - End */

    /* Reset Modal value on hide - Start */
    $('.modal').on('hidden.bs.modal', function() {
        $("#incident-form").find('textarea').val('');
        $("#incident-form").find('select[name="subject"]').val('');
         $("#incident-form").find('select[name="time_of_day"]').val('Morning');
        $("#incident-form").find('input[name="file_attachment"]').val('');
        $('#custom_subject').hide();
        var $form = $(this);
        $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
        $('#report_attachment').show();
    });
    /* Reset Modal value on hide - End */

    /*Incident - Update - status*/
    $('#incident-status-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        var incidentId = $("#incident-id").val();
        $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
        var url = "{{ route('incident.status') }}?id=" + incidentId;
        var formData = new FormData($('#incident-status-form')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    swal("Updated", "Incident updated successfully", "success");
                    $("#incidentStatusModal").modal('hide');
                    $('#incident-status-form').trigger('reset');
                    table.ajax.reload();
                } else {
                    console.log(data);
                    swal("Oops", "Could not update", "warning");
                }
            },
            fail: function (response) {
                swal("Oops", "Something went wrong", "warning");
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });

    });
    /*Incident - Update - status*/

    /*Incident - Save - Start*/
    $('#incident-form').submit(function (e) {
        e.preventDefault();
        console.log($('input#report_attachment')[0])
        var allowedFileSize = 20;
        var report_attachment = $('input#report_attachment')[0];
        if(showFileSize(report_attachment) > allowedFileSize){
            $("#file_div .help-block").addClass("has-error").text("Maximum file size allowed is "+allowedFileSize+" MB");
            return false;
        } else{
            $("#file_div .help-block").removeClass("has-error").text("");
        }
        if($('#file_attachment').val() != '' || ($('#file_attachment').val() == '' && $('#short_description').val() != '')){
            $('#attachment-validation').addClass('has-error').text('Please upload the file');
            return false;
        }
        var $form = $(this);
        swal({
            title: "Are you sure?",
                    text: "Do you want to create the incident report?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: true
            },
            function () {
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
            url = "{{ route('incident.store',['customer_id' => $customer_id,'payperiod_id' => $payperiod_id]) }}";
            var formData = new FormData($('#incident-form')[0]);
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
                        swal("Created", "Incident created successfully", "success");
                    } else {
                        swal("Oops", "Could not create", "warning");
                    }
                    $("#incidentModal").modal('hide');
                    $('#incident-form').trigger('reset');
                    table.ajax.reload();
                    $('.modal-backdrop').remove();
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
    });
    $('input[type="radio"]').click(function() {
       if($(this).val() == '1') {
            $('#report_attachment').show();
       }

       else {
            $('#report_attachment').hide();
       }
   });

    /* Dynamically fetching subject dropdown values - Start */
    $('.incident_add_button').on('click', function(){
        var customer_id=($('input:hidden[name=customer_id]').val());
        var base_url="{{route('incidentreportsubjects.getSubjectLookup',':customer_id')}}";
        var url = base_url.replace(':customer_id', customer_id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                if (data) {
                    d = new Date();
                    localTime = d.getTime();
                    localOffset = d.getTimezoneOffset() * 60000;
                    utc = localTime + localOffset;
                    offset = -5.0;
                    canada = utc + (3600000*offset);
                    currentTime = new Date(canada);
                    var year=currentTime.getFullYear();
                    var date=currentTime.getDate();
                    var month=currentTime.getMonth()+1;
                    var hours=currentTime.getHours();
                    var minutes=(currentTime.getMinutes()<10?'0':'') + currentTime.getMinutes() ;
                    $('#incidentModal input[name="yearvalue"]').val(year);
                    $('#incidentModal input[name="date"]').val(date);
                    $('#incidentModal input[name="time"]').val(("0" + hours).slice(-2)+':'+minutes);
                     $('#incidentModal select[name="month"] option[value="'+month+'"]').prop('selected',true);
                    $('#incidentModal').find('select[name="subject"] option').remove();
                    $('#incidentModal').find('select[name="subject"]').append('<option value="">Please Select</option>');
                    $("#priority").prop('selectedIndex',0)
                    $.each(data, function(subject_id, subject_value) {
                        $('#incidentModal').find('select[name="subject"]').append('<option value="'+subject_id+'">'+subject_value+'</option>');
                    });
                     //$('#incidentModal').find('select[name="subject"]').append('<option value="'+0+'">'+'Others'+'</option>');
                } else {
                    console.log('else',data);
                }
            },
            fail: function (response) {
                console.log(response);
            },
        });
    });
    /* Dynamically fetching subject dropdown values - End */
</script>
<style type="text/css">
    input[type="radio"]
{
margin-right: 4px;
}
#filter label
{
    padding-left:10px;
}

</style>
