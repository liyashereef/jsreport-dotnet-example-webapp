@extends('layouts.app')
@section('content')
<div class="selection-container">
     <form id="selection-filter-form">
<div class="table_title row">

    <div class="col-md-6">
    <h4>Candidates Selection</h4>
</div>
        <div class="col-md-2 pull-right">
               {{--  <div class="col-md-12 row border p-2 m-0">
                    <label for="employee-filter" class="col-md-2 align-self-end p-0">Employee</label>  --}}
                   </div>
                    <div class="col-md-4 text-align-right align-self-end">
                            <input type="checkbox" name="viewmytickets" id="viewmytickets">
                            <label for="viewmyexpense">View my Tickets</label>
                    </div>
                {{-- </div> --}}

</div>
<div class="selection-filter border">
        <div class="row col-md-12 ">
            <div class="col-md-2">
                <label>Site</label>
                <select class="form-control option-adjust select2" name="client_id" id="clients">
                    <option value="" selected>All</option>
                    @foreach($customers as $key => $value)
                    <option value="{{$key}}" >{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Job</label>
                <select class="form-control option-adjust select2" name="job_id" id="jobs">
                    <option value="" selected>All</option>
                    @foreach($job as $key => $value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Applicant</label>
                <select class="form-control option-adjust select2" name="candidate_id">
                    <option value="" selected>All</option>
                    @foreach($candidates as $key => $value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Preference</label>
                <select class="form-control option-adjust select2" name="preference_id" >
                    <option value="" selected>All</option>
                     @for ($i = 1; $i <= 5 ; $i++) <option value="{{$i}}">{{$i}}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select class="form-control option-adjust select2" name="status">
                    <option value="" selected>All</option>
                    @foreach($candidate_selection_status as $key => $value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>


             <div class="col-md-2">
                <label>Match Score (From - To)</label>
                <div class="row">
                    <div class="col-md-4">
                        <input type="number" name="score_from" placeholder="Low" value="" max="100" min="0" class="form-control stat-field" />

                    </div>
                    <div class="col-md-4">
                        <input type="number" name="score_to" placeholder="High" value="" max="100" min="0" class="form-control stat-field" />
                    </div>
                  {{--   <div class="col-md-4"> --}}
                        <button id="filter-search"  type="button" class="btn pm-filter-btn"><i class="fa fa-search"></i></button>
                        <button id="filter-reset"   style="margin-left: 6px;" type="button" class="btn pm-filter-btn"><i class="fa fa-refresh"></i></button>
                  {{--   </div> --}}
                </div>
            </div>

        </div>

</div>
  </form>
</div>
<table class="table table-bordered" id="candidates-credential-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Applicant</th>
            <th>Preference </th>
            <th>Project Number</th>
            <th>Client</th>
            <th>Job</th>
            <th>Position</th>
            <th>Wage</th>
            <th>Match Score</th>
            <th>Schedule</th>
            <th>Shift</th>
            <th>Hours Per Week</th>
            <th>Open Post</th>
            <th>Primary Recruiter</th>
             <th style="width:20%">
                <div class="row">
                    <div style="text-align: center; width:30%;">Status</div>
                    <div style="text-align: center; width:30%;">Date</div>
                    <div style="text-align: center; width:30%;">Recruiter</div>
                </div>
            </th>
            {{-- <th>Status</th>
            <th>Date</th>
            <th>Recruiter</th> --}}
            <th>Action</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url'=>'#','id'=>'candidate-selection-action-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('open_positions', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}" id="candidate_status">
                    <label for="status" class="col-sm-12 control-label">Choose Status</label>
                    <div class="col-sm-12">
                        {{ Form::select('status', [null=>'Please Select']+$candidate_selection_status, null,array('class' =>'form-control','required'=>true,'id'=>'changeStatus')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group hide-this-block" id="participant">
                    <label for="particpants" class="col-sm-12 control-label">Add Participants</label>
                    <div class="col-sm-12">
                           {{ Form::select('particpants[]', $users, null,array('multiple'=>'multiple','class' =>'form-control select2','id'=>'particpantsArr')) }}

                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group hide-this-block" id="email_script_block">
                    <label for="emailScript" class="col-sm-12 control-label">Email Script</label>
                    <div class="col-sm-12">
                         {{Form::textarea('emailScript',old('emailScript',@$mail_content['body']),array('class'=>'form-control ckeditor','id'=>'editor'))}}

                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
                {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop

@section('scripts')
<script>
    const sel = {
        reports: [],
        init() {
            //Fetch report data
            this.getReportData();
            //Register all event listeners
            this.registerEventListeners();
            //Init select2
            this.initSelect2();
        },
        registerEventListeners() {
            let root = this;
            let table = $('#candidates-credential-table').DataTable();
            $('#clients').on('change', function() {
               root.onChangeClients($(this));
            });
            //On filter search
            $('#filter-search').on('click', function() {
                root.getFilterData();
            });
            //On filter reset
            $('#filter-reset').on('click', function() {
                root.resetFilters();
                root.getFilterData();
            });

             $('#candidate-selection-action-form').submit(function(e) {
                e.preventDefault();
                root.onChangeStatus();
            });
             $('#changeStatus').on('change', function() {
               if( this.value==1 ){
                $("#email_script_block").removeClass('hide-this-block');
                 $("#participant").removeClass('hide-this-block');
                  $('#particpantsArr').val(null).trigger("change");

               }
               else
               {
               $("#email_script_block").addClass('hide-this-block');
                $("#participant").addClass('hide-this-block');
               }
              });



            //On submit Task status
             $('#candidates-credential-table').on('click', ".match_score_calculation", function(e) {
                e.preventDefault();
                root.onShowCalculations($(this).attr('data-job-id'),$(this).attr('data-candidate-id'));
            });
              $('#viewmytickets').on('click', function() {
                if ($(this).is(":checked")) {
                    $(this).val(1);
                } else {
                     $(this).val(0);
                }
                root.getFilterData();
            });

        },
        collectFilters() { //Collect filter data
            return $('#selection-filter-form').serialize();

        },
        resetFilters() { //Reset filters
            $('#selection-filter-form')[0].reset();
            $(".select2").trigger("change");
        },
        initSelect2() {
            $('.select2').select2();
        },
        getFilterData()
        {
            let root = this;
            var table = $('#candidates-credential-table').DataTable();
            let url = '{{ route('recruitment.candidate.selection.list') }}';
             url += `?${this.collectFilters()}`;

              table.ajax.url( url ).load();
        },
        getReportData() {
            let url = '{{ route('recruitment.candidate.selection.list') }}';
            var table = $('#candidates-credential-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            destroy:true,
            ajax: url,
            dom: 'Blfrtip',
             createdRow: function (row, data) {
                  $(row).find('td:eq(8)').css('background-color', data.color);
                  $(row).find('td:eq(8) a').css('color', data.fontcolor);
                  $(row).find('td:eq(8)').css('text-align','center');

                },
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A0',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                         orthogonal: 'export'

                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        orthogonal: 'export'
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A0',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        //stripHtml: false,
                        orthogonal: 'export'

                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            order: [
                [8, "desc"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable: false
                },
                {
                    data: null,
                    name: 'candidate_name',
                    render: function (row) {
                        actions = '';
                        var url = '{{ route("recruitment.candidate.view", [":candidate_id"]) }}';
                        url = url.replace(':candidate_id', row.candidate_id);
                        actions += '<a title="View application" href="' + url + '">' + row.candidate_name + '</a>';
                        return actions;
                    }
                },

                {
                    data: 'rec_preference',
                    name: 'rec_preference',
                    defaultContent: '--'
                },
                {
                    data: 'project_number',
                    name: 'project_number',
                    defaultContent: '--'
                },

                {
                    data: null,
                    name: 'client_name',
                    defaultContent: '--',
                    render: function(row) {
                        actions = '';
                        var view_url = '{{ route("recruitment-job.view", ":id") }}';
                        view_url = view_url.replace(':id', row.job_id);
                        actions += '<a title="View" href="' + view_url + '">' + row.client_name + '</a>';
                        return actions;
                    }
                },
                {
                    data: 'unique_key',
                    name: 'unique_key',
                    defaultContent: '--'
                },
                {
                    data: 'position',
                    name: 'position',
                    defaultContent: '--'
                },
                {
                    data: 'wage_expectation',
                    name: 'wage_expectation',
                    defaultContent: '--'
                },
                {
                    data: null,
                    name: 'rec_match_score',
                    defaultContent: '--',
                    render: function(o) {
                        var actions = '';
                        actions += '<a href="#" style="font-weight: bold;" data-job-id=' + o.job_id + ' data-candidate-id=' + o.candidate_id + ' class="match_score_calculation">' + o.rec_match_score + '</a>';
                        return actions;
                    },
                },
                {
                    data: null,
                    name: 'days_required',
                    defaultContent: '--',
                    render:function(o)
                    {
                        return o.days_required.replace(/,/g, '<br>');
                    },
                },
                {
                    data: null,
                    name: 'shifts',
                    defaultContent: '--',
                    render:function(o)
                    {
                        return o.shifts.replace(/,/g, '<br>');
                    },
                },
                {
                    data: 'prefered_hours_per_week',
                    name: 'prefered_hours_per_week',
                    defaultContent: '--'
                },
                {
                    data: 'no_of_vaccancies',
                    name: 'no_of_vaccancies',
                    defaultContent: '--'
                },
                {
                    data: 'primary_recruiter',
                    name: 'primary_recruiter',
                    defaultContent: '--'
                },
                // {
                //     data: 'status',
                //     name: 'status',
                // },
                {data: null, name: 'status_html', sortable: false, defaultContent: '--',render:function(data,type,row)
                    {
                    return type === 'export' ?
                    data.status_html.replace( /\<tr\>/g, "\r\n" ).replace( /\<\/td\>/g, "  " ) :
                    data.status_html;
                        // return '<table style="border:none"><tr><td style="width:40%">Begin Onboarding</td><td style="width:30%">24-10-1994 <br>34:45:34</td><td style="width:30%">Admin Admin</td></tr><tr><td>Selected for Interview</td><td>24-10-1994 <br>34:45:34</td><td>Admin</td></tr><table>';
                    },},
                // {data: 'status_log.[ <br><br>].datetime', name: 'status_log.0.datetime', sortable: false, defaultContent: '--'},
                // {data: 'status_log.[ <br><br>].recruiter', name: 'status_log.0.recruiter', sortable: false, defaultContent: '--'},
                // {
                //     data: 'recruiter',
                //     name: 'recruiter',
                //     defaultContent: '--'
                // },
                {
                    data: null,
                    orderable: false,
                    render: function(o) {
                        var id = o.id;
                        var no_of_vaccancies = o.no_of_vaccancies;
                        var status_id=(o.status_id)?o.status_id:null;
                        var selected_for_interview_count=o.selected_for_interview_count;
                        var is_interview_completed=o.is_interview_completed;
                        var actions = '';
                     if(o.status!=="Pending for Onboarding" )
                        //if(o.status_id){
                        actions += '<a href="#" onclick="openModal(' + id +','+ no_of_vaccancies + ','+ status_id + ','+selected_for_interview_count+','+is_interview_completed+')" class="fa fa-podcast fa-lg link-ico" data-id=' + id + ' data-open-positions=' + no_of_vaccancies + '></a>';
                   // }
                    // else
                    // {
                    //     actions += '<a href="#" onclick="openModal(' + o.id +','+ o.no_of_vaccancies + ')" class="fa fa-podcast fa-lg link-ico" data-id=' + id + ' data-open-positions=' + no_of_vaccancies + '></a>';
                    // }
                    else {
                         actions += '<a href="#" class="fa fa-podcast fa-lg link-ico fa-disabled" data-id=' + id + '></a>';
                     }
                        return actions;
                    },
                }

            ]
        });
        },

        onShowCalculations(job_id,candidate_id) {
            var base_url = "{{route('recruitment.candidate-match-score.show',[':candidate_id',':job_id'])}}";
            var url1 = base_url.replace(':candidate_id', candidate_id);
            var url = url1.replace(':job_id', job_id);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data.data.length > 0) {
                        var swal_html = '<div class="panel"> <div class="panel-body"><table align="center" class="table">';
                        swal_html += '<thead><tr><th>No:</th><th>Criteria</th><th>&nbsp;Mapping Value</td><th style="text-align: center; vertical-align: middle;">&nbsp;Weighted Score</th></tr></thead>';
                        $.each(data.data, function(index, value) {
                            swal_html += '<tr><td style="text-align: center; vertical-align: middle;">' + (index+1) + '</td><td style="text-align: left; vertical-align: middle;">' + value.criteria.criteria_name + '</td><td style="text-align: center; vertical-align: middle;">&nbsp' + value.mapping_value + '</td><td style="text-align: center; vertical-align: middle;">&nbsp' + value.weighted_score + '</td></tr>';
                        });
                        swal_html += '</table></div></div>';
                    } else {
                        var swal_html = '<p>No record found</p>';

                    }
                    swal({
                        title: "Match Score",
                        text: swal_html,
                        html: true
                    });
                },

                fail: function(response) {
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, true);
                },

            })
        },
        onChangeStatus() {
            let root = this;
            let $form = $('#candidate-selection-action-form');
            url = "{{ route('recruitment.candidate-selection-update') }}";
             for ( instance in CKEDITOR.instances )
             CKEDITOR.instances[instance].updateElement();
            let formData = new FormData($('#candidate-selection-action-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function(data) {
                    if (data.success==true) {
                        swal("Saved", "Status of this application has been updated",
                            "success");

                        var table = $('#candidates-credential-table').DataTable();
                         table.ajax.reload();
                    }
                    else if(data.success=="interviewincomplete")
                            {
                        swal("Interview not Completed", "The candidate inteview has not yet Completed", "warning");
                            }
                      else if(data.success=="closedPositions")
                        {
                        swal("Closed", "The position has been closed", "warning");
                        }
                        else if(data.success=="alreadyOnboarded")
                            {
                        swal("Already Onboarded", "The candidate already Onboarded for another job", "warning");
                            }
                            else {
                        swal("Oops", "The record has not been saved", "warning");
                    }
                    $("#myModal").modal('hide');
                },
                fail: function(response) {
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form, true);
                },
                contentType: false,
                processData: false,
            });
        },
        onChangeClients(selected)
        {
            var base_url = "{{route('recruitment.customer.getJob',[':customer_id'])}}";
            var url = base_url.replace(':customer_id', $(selected).val());
            let formData = new FormData($('#candidate-selection-action-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data.success) {
                        $('#jobs').empty().append($("<option></option>").attr("value",'').text('All'));
                        $.each(data.result, function(id, job) {
                            $('#jobs').append($("<option></option>")
                            .attr("value",id)
                            .text(job));
                        });
                    }
                },
            });
        }
    }
   function openModal(id,open_positions,status=null,selected_for_interview_count,is_interview_completed) {
    // if(open_positions==0)
    // {
    //     swal("","No Open Position for this job","warning");
    //     return false;
    // }
    console.log('selected_for_interview_count',selected_for_interview_count)
     console.log('count1',is_interview_completed)
    console.log(status)
    console.log('position',open_positions)
    $('#myModal form')[0].reset();
    var emailScript = <?php echo json_encode($mail_content['body']) ?>;
    CKEDITOR.instances['editor'].setData(emailScript)
    $("#email_script_block").addClass('hide-this-block');
    $('#myModal').find('input[name="id"]').val(id);
    $('#myModal').find('input[name="open_positions"]').val(open_positions);
    $('#myModal').modal();
    if(open_positions==0)
     {
      $("#changeStatus option:contains('Selected for Interview')").attr("disabled","disabled");
      $("#changeStatus option:contains('Rejection Note')").attr("disabled",false);
      $("#changeStatus option:contains('Begin Onboarding')").attr("disabled","disabled");
      $("#changeStatus option:contains('Reject for Role')").attr("disabled",false);
    //     return false;
     }
     else if(selected_for_interview_count>0 && is_interview_completed==1 && status==1 || status==4)
     {
       $("#changeStatus option:contains('Reject for Role')").attr("disabled","disabled");
       $("#changeStatus option:contains('Begin Onboarding')").attr("disabled",false);
       $("#changeStatus option:contains('Selected for Interview')").attr("disabled","disabled");
       $("#changeStatus option:contains('Rejection Note')").attr("disabled",false);
     }
     else if(selected_for_interview_count>0 && status!=3)
     {
       $("#changeStatus option:contains('Reject for Role')").attr("disabled","disabled");
       $("#changeStatus option:contains('Begin Onboarding')").attr("disabled","disabled");
       $("#changeStatus option:contains('Selected for Interview')").attr("disabled","disabled");
       $("#changeStatus option:contains('Rejection Note')").attr("disabled",false);
     }
     else  if(status==null)
     {
      $("#changeStatus option:contains('Reject for Role')").attr("disabled","disabled");
      $("#changeStatus option:contains('Begin Onboarding')").attr("disabled","disabled");
      $("#changeStatus  option:contains('Selected for Interview')").attr("disabled",false);
      $("#changeStatus option:contains('Rejection Note')").attr("disabled",false);
     }
     else if(status==3)
     {
      $("#changeStatus option:contains('Selected for Interview')").attr("disabled","disabled");
      $("#changeStatus option:contains('Rejection Note')").attr("disabled","disabled");
      $("#changeStatus option:contains('Begin Onboarding')").attr("disabled","disabled");
      $("#changeStatus option:contains('Reject for Role')").attr("disabled",false);
    //     return false;
     }
     else{
      $("#changeStatus option:contains('Selected for Interview')").attr("disabled",false);
      $("#changeStatus option:contains('Rejection Note')").attr("disabled",false);
      $("#changeStatus option:contains('Begin Onboarding')").attr("disabled",false);
      $("#changeStatus option:contains('Reject for Role')").attr("disabled",false);
     }
}

    /// Code to run when the document is ready.
    $(function() {
        sel.init();
    });



</script>
<style type="text/css">
    .selection-filter {
        padding: 20px;
        margin: 10px 5px;
        background: #f3f3f3;
    }
    .select2-selection__choice{
        color: #f3f3f3 !important;
    }
</style>
@stop
