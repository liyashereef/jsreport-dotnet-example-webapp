    @extends('layouts.app')
    @section('title', 'Detailed Timesheet')
    @section('content_header')
    <h1 class="ts-approve">Incident Status</h1>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    @stop
    @section('content')
    <style>
        .popover {
        z-index: 1010; /* A value higher than 1010 that solves the problem */
    }
    .time-container{
        padding: 1px 1px 0px 0px !important;
    }

    .time-container label{
        padding-bottom: 1px;
        float: left;
        clear: both;
    }

    .position-name-label{
        font-size: 13px;
    }
    .total-hours-span{
        color: red;

    }
    #total-row{
        font-weight: bold;
        height: 50px;
    }
    </style>

    <div class="table_title">
    <h4>Incident Report</h4></div>
    <div class="customer-details-block">
    <div class="row">
        {{ Form::hidden('id', $report->id ) }}
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-4 col-form-label  col-xs-3"><b>Project No.</b></label>
                <label  class="col-md-8 col-form-label col-xs-3">{{$customer['details']['project_number'] or "--"}}</label>

            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-5 col-form-label  col-xs-3"><b>Client Contact</b></label>
                <label  class="col-md-7 col-form-label col-xs-3">{{$customer['details']['contact_person_name'] or "--"}}
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-6 col-form-label col-xs-3"><b>Supervisor</b></label>
                <label  class="col-md-6 col-form-label  col-xs-3">{{ isset($customer['supervisor']['full_name']) ? $customer['supervisor']['full_name'] : "--"}} </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-6 col-form-label col-xs-3"><b>Area Manager</b></label>
                <label  class="col-md-6 col-form-label  col-xs-3">
                {{isset($customer['areamanager']['full_name']) ? $customer['areamanager']['full_name'] : "--"}}</label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-4 col-form-label  col-xs-3"><b>Client</b></label>
                <label  class="col-md-8 col-form-label col-xs-3">{{$customer['details']['client_name'] or "--"}}
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-5 col-form-label  col-xs-3"><b>Client Phone</b></label>
                <label  class="col-md-7 col-form-label col-xs-3">{{ ( $customer['details']['contact_person_phone_ext']!=null?($customer['details']['contact_person_phone'].' x'.$customer['details']['contact_person_phone_ext']):(null!=$customer['details']['contact_person_phone']?$customer['details']['contact_person_phone']:'--') )}}</label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-6 col-form-label col-xs-3"><b>Supervisor Phone</b></label>
                <label  class="col-md-6 col-form-label  col-xs-3">{{ ( isset($customer['supervisor']['phone']) && isset($customer['supervisor']['phone_ext'])?($customer['supervisor']['phone'].' x'.$customer['supervisor']['phone_ext']):(isset($customer['supervisor']['phone'])?$customer['supervisor']['phone']:'--') )}}</label>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-6 col-form-label col-xs-3"><b>Area Manager Phone</b></label>
                <label  class="col-md-6 col-form-label  col-xs-3">{{ (isset($customer['areamanager']['phone_ext'])?($customer['areamanager']['phone'].' x'.$customer['areamanager']['phone_ext']):(isset($customer['areamanager']['phone'])?$customer['areamanager']['phone']:'--') )}}</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-4 col-form-label  col-xs-3"><b>Address</b></label>
                <label  class="col-md-8 col-form-label col-xs-3">{{$customer['details']['address'] or ""}}, {{$customer['details']['city'] or ""}}, {{$customer['details']['province'] or ""}}, {{$customer['details']['postal_code'] or ""}}.
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-5 col-form-label  col-xs-3"><b>Client Email</b></label>
                <label  class="col-md-7 col-form-label col-xs-3 email-break">{{$customer['details']['contact_person_email_id'] or "--"}}</label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-6 col-form-label col-xs-3"><b>Supervisor Email</b></label>
                <label  class="col-md-6 col-form-label  col-xs-3 email-break">
                    {{$customer['supervisor']['email'] or "--"}}
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label  class="col-md-6 col-form-label col-xs-3"><b>Area Manager Email</b></label>
                <label  class="col-md-6 col-form-label  col-xs-3 email-break">
                    {{$customer['areamanager']['email'] or "--"}}
                </label>
            </div>
        </div>
    </div>
    </div>




    <div id="tab-content2">
       {{--   <div class="table-fixedth full-width" style="margin-bottom: 5px;">
                   Incident Report for {{ $report->title }}
        </div> --}}
        <div class="timesheet-filters mb-2">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3"><label class="filter-text">Title</label></div>

                    <div class="col-md-8">
                        <input type="text" name="title" class="form-control" value="{{ $report->title }}" readonly>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="filter-text">Subject</label>
                    </div>
                    <div class="col-md-8">
                         <input type="text" name="subject" class="form-control" value="{{ $report->subject_name_with_fallback }}" readonly>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="filter-text">Priority</label>
                    </div>
                    <div class="col-md-8">
                         <input type="text" name="priority" class="form-control" value="{{ $report->priority->value }}" readonly>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <?php
            $class_name= strtolower(str_replace(' ', '_', $report->incidentStatusLogWtihList->last()->incidentStatusList->status));
            ?>
            <div class="col-md-2">
                <div class="row">
                    <div class="col-md-3">
                        <label class="filter-text">Status</label>
                    </div>
                    <div class="col-md-8">
                         <input type="text" name="status" class="form-control {{ $class_name}}" id="last_status" value="{{ $report->incidentStatusLogWtihList->last()->incidentStatusList->status}}" readonly>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            {{-- @if($report->incidentStatusLogWtihList->last()->incident_status_list_id!=3) --}}
             <div class="col-md-1">
                <div class="row">
                    <div class="col-md-3">
                        <label><button  class="btn fa fa-plus-square edit"  data-id='{{ $report->id }}'></button></label>
                    </div>
                    <div class="col-md-8">
            
                    </div>
                </div>
            </div>
            {{-- @endif --}}
        </div>
    </div>
        <div class="container-fluid">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-right text-sm-right text-md-right text-lg-right text-xl-right margin-top-1">
                <a href="javascript:  w=window.open('{{ $incident_pdf_url }}'); w.print();">
        <button type="button" class="btn submit">Print Report</button>
    </a>
           {{--  <input class="btn submit" type="submit" value="Print Report"  onclick="window.location='{{ $incident_pdf_url }}'"> --}}
        </div>
    {{-- <div class="form-group row ">route('incident.attachement', ['incident_report_id' => $each_job->id]);
                                    <div class="col-sm-12 candidate-screen-head"> Incident Details
                                    </div>
    </div> --}}

    <div class="form-group row">
    <div class="col-md-1"><b>Reported on</b></div>
    <div class="col-md-5"> {{  date('l, M d, Y', strtotime($report->occurance_datetime))}}  at  {{ date('h:i A', strtotime($report->occurance_datetime))}}</div>
    <div  class="col-md-4"><label class="error" for="customer_client"></label>   </div>
    </div>
    <div class="form-group row">
    <div class="col-md-1"><b>Reported by</b></div>
    <div class="col-md-5">{{ $report->reporter->full_name }}</div>
    <div  class="col-md-4"><label class="error" for="contract_number"></label>   </div>
    </div>
    @if($attchment_url)
    <div class="form-group row ">
    <div class="col-md-1"><b>Images</b></div>
    <div class="col-md-5 report_image"><img src="{{ $attchment_url }}" alt="Incident Attachment"></div>
    <div  class="col-md-4"><label class="error" for="contract_number"></label>   </div>
    </div>
    @endif
    <div class="form-group row">
    <div class="col-md-4"><b>Incident Description</b></div>
    </div>
    <div class="form-group row">
    <div class="col-md-12">{{ $report->incident_description}}</div>
    </div>
    <div class="form-group row">
                                    <div class="col-sm-12 candidate-screen-head"> Amendment
                                    </div>
    </div>
    @foreach ($report->amendmentList as $amendment)
    <div class="customer-details-block">
    <div class="form-group row ">
    <div class="col-md-1"><b>Date</b></div>
    <div class="col-md-5">  {{  date('l, M d, Y', strtotime($amendment->created_at))}}  at  {{ date('h:i A', strtotime($amendment->created_at))}}</div>
    <div  class="col-md-4"><label class="error" for="customer_client"></label>   </div>
    </div>
    <div class="form-group row">
    <div class="col-md-1"><b>Guard</b></div>
    <div class="col-md-5">{{$amendment->user->full_name  }}</div>
    <div  class="col-md-4"><label class="error" for="contract_number"></label>   </div>
    </div>
    <div class="form-group row">
    <div class="col-md-1"><b>Suggested Status</b></div>
    <div class="col-md-5">@if($amendment->amendment==1){{$amendment->incidentSuggestedStatusList->status}}@else {{$amendment->incidentStatusList->status}} @endif</div>
    <div  class="col-md-4"><label class="error" for="contract_number"></label>   </div>
    </div>
    <div class="form-group row">
    <div class="col-md-1"><b>Source</b></div>
    <div class="col-md-5">@if($amendment->amendment==1) Mobile @else Web @endif</div>
    <div  class="col-md-4"><label class="error" for="contract_number"></label>   </div>
    </div>
    <div class="form-group row">
    <div class="col-md-1"><b>Notes</b></div>
    <div class="col-md-10">{{$amendment->notes or '--'}}</div>
   
    </div>
                            </div>
                            @endforeach
                        </div>
       
    </div>
     
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
                    {{ Form::hidden('customer_id', $report->customer_id) }}
                    {{ Form::hidden('payperiod_id', $report->payperiod_id) }}
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
                                <input class="form-control" type='text'  value="{{ $report->subject_name_with_fallback}}" readonly>
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
                                <span>{{  date('M d, Y', strtotime($report->created_at))}}  </span>
                               {{--  <span>{{$formatted_date}}</span> --}}
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="employee_name">
                            <label for="employee_name" class="col-sm-4 control-label">Employee Name </label>
                            <div class="col-sm-8">
                                <span>{{Auth::user()->full_name}}</span>
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group row" id="notes">
                            <label for="notes" class="col-sm-4 control-label">Notes</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="notes"></textarea>
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

        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog " style="height: 80% !important;">
    <div class="modal-content">              
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <img src="" class="imagepreview img-responsive" style=" width:100%;object-fit: contain;">
      </div>
    </div>
  </div>
</div>



    <script>

        // Shorthand for $( document ).ready()
    $(function() {

            $('.report_image').on('click', function() {
            $('.imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');   
        });  
        let report = {!! json_encode($report) !!};
        
         //var table1=$('#subt').DataTable();
         $("#tab-content2").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            // var description = $(this).data('description');
            $("#incidentStatusModal").modal();
            // $("#status-subject").val(description);
             $("#incident-id").val(id);
        });
       

        });
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
                        location.reload();
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



        

           
    function getTime(created_at) {
        var datetime = new Date(created_at);
        var time = datetime.toLocaleString([], {
            hour: '2-digit',
            minute: '2-digit'
        }); 
        return  time;
    }

    </script>

    <!-- Style -->
    <style>
       #timesheet-tabs .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            color: #f48452;
        }
        .timesheet-filters{
            background: #f9f1ec;
            padding: 11px 5px;
        }
        .timesheet-filters .filter-text{
            position: absolute;
            top: 1;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }  
        .customer-details-block
        {
            font-size:1rem;
        }
        .open {
        background: #ff9999 !important;
    }

    .in_progress {
        background: #ffe690 !important;
    }

    .closed {
        background: rgba(36, 169, 66, 0.62) !important;
    }
    .report_image img
    {
        height: 500px;
    }
    </style>

    @stop
