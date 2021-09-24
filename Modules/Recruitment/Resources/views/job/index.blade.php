@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Summary Requisitions </h4>
</div>
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
            {{ Form::select('clientname-filter',[''=>'Select customer']+$customerList,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
            <span class="help-block"></span>
        </div>
    </div>
</div>
<br>
<table class="table table-bordered" id="jobs-table">
    <thead>
        <tr>
            @can('rec-archive-job')
            <th class="dt-body-center" width="1%">
                <input id="select_all" value="1" type="checkbox" />
            </th>
            @endcan
            <th class="sorting" width="2%">Job Id</th>
            <th class="sorting" width="10%">Requestor</th>
            <th class="sorting" title="Requestor's Phone Number" width="10%">Phone</th>
            <th class="sorting" width="15%">Email Address</th>
            <th class="sorting" width="5%" title="Project Number">Proj.No.</th>
            <th class="sorting" width="10%">Client</th>
            <th class="sorting" title="Position Requested(Number of Posts)" width="10%">Position</th>
            <!--<th class="sorting" width="1%">Posts</th>-->
            <th class="sorting" width="10%">Rationale</th>
            <th class="sorting" width="10%">Type</th>
            <th class="sorting" width="5%" title="Date Required">Date</th>
            <!--<th class="sorting" width="1%">Wage(L)</th>
            <th class="sorting" width="1%">Wage(H)</th>-->
            <th class="sorting" width="5%">Wage</th>
            <th class="sorting" width="3%">Status</th>
            @canany(['rec-edit-job','rec-job-approval','rec-hr-tracking','rec-hr-tracking-detailed-view','rec-view-allocated-candidates-geomapping'])
            <th width="1%">Actions</th>
            @endcan
        </tr>
    </thead>
</table>
@can('rec-archive-job')
{{ Form::button('Archive/Unarchive', array('class'=>'button btn archive submit','value'=>'archive','style'=>'display:none;'))}}
@endcan

@can('rec-job-approval')

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url'=>'#','id'=>'job-action-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null)}}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}" id="status">
                    <label for="status" class="col-sm-12 control-label">Choose Status</label>
                    <div class="col-sm-12">
                        {{ Form::select('status', [null=>'Please Select','approved'=>'Approve','rejected'=>'Reject','suspended'=>'Suspend'], null,array('class'=>
                        'form-control','required'=>true, 'onchange'=>'if(this.value=="approved"){ $("#hr_rep_id").show();$("#status_reason_div").hide();}
                        else { $("#hr_rep_id").hide();$("#status_reason_div").show(); }')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div style="display: none;" class="form-group" id="status_reason_div">
                    <label for="status_reason" class="col-sm-12 control-label">Reason</label>
                    <div class="col-sm-12">
                        {{ Form::textarea('status_reason',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                        <span class="help-block" id="textarea_message"></span>
                    </div>
                </div>
                <div style="display: none;" class="form-group {{ $errors->has('hr_rep_id') ? 'has-error' : '' }}" id="hr_rep_id">
                    <label for="hr_rep_id" class="col-sm-12 control-label">Assign HR Resource</label>
                    <div class="col-sm-12">
                        {{ Form::select('hr_rep_id', [null=>'Please Select']+$hr_reps, null,array('class' => 'form-control')) }}
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
@endcan



@stop
@section('scripts')
<script>
    var jobs_onboard = <?php echo json_encode($jobs_onboard); ?>;
    var selectorStatus = <?php echo json_encode(config('globals.rec_selection_status')); ?>;

    function collectFilterData() {
        return {
            client_id: $("#clientname-filter").val(),
        }
    }

    $(function() {
        $(".select2").select2()
        var table = $('#jobs-table').DataTable({
            fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url": "{{ route('recruitment-job.list') }}",
                "data": function(d) {
                    d.payperiod = $("#payperiod-filter").val();

                    //Url arguments
                    let args = globalUtils.uraQueryParamToJson(window.location.href);

                    d = $.extend(d, args);
                    d = $.extend(d, collectFilterData())
                    d.cIds = globalUtils.decodeFromCsv(args.cIds);
                    return d;
                },
            },
            "error": function(xhr, textStatus, thrownError) {
                if (xhr.status === 401) {
                    window.location = "{{ route('login') }}";
                }

            },
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-file-pdf-o',
                    @canany(['rec-edit-job', 'rec-job-approval'])
                    exportOptions: {
                        columns: ['th:not(:last-child)']
                    }
                    @endcan
                },
                {
                    extend: 'excelHtml5',
                    //text: ' ',
                    //className: 'btn btn-primary fa fa-file-excel-o',
                    @canany(['rec-edit-job', 'rec-job-approval'])
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                    @endcan
                },
                {
                    extend: 'print',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-print',
                    @canany(['rec-edit-job', 'rec-job-approval'])
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                    @endcan
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [],
            createdRow: function(row, data, dataIndex) {
                var color = '#003A63';
                if (data.active) {
                    $(row).addClass(data.status);
                    switch (data.status) {
                        case 'rejected':
                        case 'suspended':
                        case 'completed':
                            color = 'white';
                            break;
                    }
                } else {
                    $(row).addClass('archived');
                }
                $(row).find('td', 'a').css('color', color);
                $(row).find('a').css('color', color);
                //$(row).find('td').css('white-space', 'nowrap');
            },
            drawCallback: function() {
                $('#popover').popover({
                    "html": true,
                    trigger: 'manual',
                    placement: 'left',
                })
            },
            columnDefs: [
                @can('rec-archive-job') {
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    sortable: false,
                    className: 'dt-body-center',
                    render: function(data, type, full, meta) {
                        return '<input type="checkbox" id="job_id" class="archive-button-trigger select-record" name="job_id" value="' +
                            $('<div/>').text(data).html() + '">';
                    }
                },
                @endcan
            ],
            @can('rec-archive-job')
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            @endcan
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                @can('rec-archive-job') {
                    data: 'id',
                    name: 'id'
                },
                @endcan {
                    data: null,
                    name: 'unique_key',
                    render: function(o) {
                        actions = '';
                        var view_url = '{{ route("recruitment-job.view", ":id") }}';
                        view_url = view_url.replace(':id', o.id);
                        actions += '<a title="View job details" href="' + view_url + '">' + o.unique_key + '</a>';
                        return actions;
                    }
                },
                {
                    data: 'requester',
                    name: 'requester',
                    defaultContent: "--",
                },
                {
                    data: 'phone',
                    name: 'phone',
                    defaultContent: "--",
                    render: function(phone) {
                        return (null != phone) ? '<span style="white-space: nowrap;">' + phone + '</span>' : '--';
                    }
                },
                {
                    data: 'email',
                    name: 'email',
                    defaultContent: "--",
                },
                {
                    data: 'customer.project_number',
                    name: 'customer.project_number',
                    defaultContent: "--",
                },
                {
                    data: 'customer.client_name',
                    name: 'customer.client_name'
                },
                {
                    data: null,
                    name: 'position_beeing_hired.position',
                    render: function(data) {
                        return data.position_beeing_hired.position + ' (' + data.no_of_vaccancies + ')';
                    }
                },
                /*{
                    data: 'no_of_vaccancies',
                    name: 'no_of_vaccancies',
                    visible:false,
                },*/
                {
                    data: 'reason.reason',
                    name: 'reason.reason'
                },
                {
                    data: 'assignment_type.type',
                    name: 'assignment_type.type'
                },
                {
                    data: 'required_job_start_date',
                    name: 'required_job_start_date'
                },
                /*{
                    data: 'wage_low',
                    name: 'wage_low',
                    visible:false,
                    render: function (wage_low) {
                        return '$' + Number(wage_low).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                    }
                },
                {
                    data: 'wage_high',
                    name: 'wage_high',
                    visible:false,
                    render: function (wage_high) {
                        return '$' + Number(wage_high).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                    }
                },*/
                {
                    data: null,
                    name: 'wage',
                    render: function(row) {
                        wage = '$' + Number(row.wage).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                        // wage += '-$' + Number(row.wage_high).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                        return wage;
                    }
                },
                {
                    data: null,
                    name: 'status',
                    render: function(row) {
                        return '<span style="text-transform:capitalize;">' + row.status + (!row.active ? " & Archived" : "") +
                            '</span>';
                    }
                },
                @canany(['rec-edit-job','rec-job-approval','rec-hr-tracking','rec-hr-tracking-detailed-view','rec-view-allocated-candidates-geomapping'])
                {
                    data: null,
                    sortable: false,
                    render: function(o) {
                        actions = '';
                        @can('rec-edit-job')
                        var edit_url = '{{ route("recruitment-job.edit", ":id") }}';
                        edit_url = edit_url.replace(':id', o.id);
                        actions += '<a title="Edit" href="' + edit_url + '" class="fa fa-edit fa-lg link-ico" data-id=' + o.id + '></a>';
                        @endcan
                        @can('rec-job-approval')
                        actions += '<a title="Approve/Reject/Suspend" onclick="openModal(' +
                            o.id + ')" href="#" class="fa fa-podcast fa-lg link-ico"></a>';
                        @endcan
                        @canany(['rec-hr-tracking', 'rec-hr-tracking-detailed-view'])
                        if (o.active && ["approved", "completed"].includes(o.status)) {
                            var hr_tracking_url = '{{ route("recruitment-job.hr-tracking",":job_id") }}'
                            hr_tracking_url = hr_tracking_url.replace(':job_id', o.id);
                            actions += '<a class="fa fa-compress fa-lg link-ico" title="HR Tracking" href="' + hr_tracking_url + '"></a>';
                        }
                        @endcan
                        if (o.status_reason != '' && o.status_reason != null) {
                            actions += '<a class="fa fa-info-circle fa-lg link-ico" title="Reason" id="popover" data-placement="left" data-content="' + o.status_reason + '" href="#"></a>';
                        }

                        @canany(['rec-candidate-mapping','rec-view-allocated-candidates-geomapping'])      

                        var map_url = '{{ route("recruitment.candidate.plot-in-map-with-customer", ":job_id") }}';
                        map_url = map_url.replace(':job_id', o.id);
                        actions += '<a title="Map" href="' + map_url +
                            '" class="fa fa-users"></a>';
                        @endcan
                        return actions;
                    },
                },
                @endcan
            ]
        });
        table.on('draw', function() {
            refreshSideMenu();
        });
        @can('rec-job-approval')
        $('#job-action-form').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            var allowUpdate = false;
            if ($(this).find('textarea').val().length > 100) {
                $('#textarea_message').text('Maximum character length must be less than 100.');
                return false;
            }
            if ($(this).find("select[name='status']").val() != 'approved') {
                if (jobs_onboard[$('#job-action-form input[name="id"]').val()]) {
                    var a = jobs_onboard[$('#job-action-form input[name="id"]').val()]
                    var text = 'The following candidates are already assigned to this Job<br/><div class="panel"><div class="panel-body" style=" height: 200px;  overflow-y: scroll;"><table align="center" class="table">`;`<thead><tr><th>Candidate</th><th>Status</td></tr></thead> ';
                    $.each(a, function(index, value) {
                        text += '<tr> <td style="text-align: center; vertical-align: middle;">' + value.candidate.name + '</td><td style="text-align: center; vertical-align: middle;">' + selectorStatus[value.status] + '</td></tr>'

                    });
                    text += ' </table></div></div>You will not be able to undo this action. Proceed?'
                    console.log(text)
                } else {
                    text = 'You will not be able to undo this action. Proceed?';
                }
                swal({
                        title: "Are you sure?",
                        html: true,
                        text: text,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, remove",
                        showLoaderOnConfirm: true,
                        closeOnConfirm: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            var job_id = $('#job-action-form input[name="id"]').val()
                            var url = "{{ route('recruitment-job.update-status',':job_id') }}";
                            url = url.replace(':job_id', job_id);
                            var formData = new FormData($('#job-action-form')[0]);

                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: url,
                                type: 'POST',
                                data: formData,
                                success: function(data) {
                                    if (data.success) {
                                        swal("Saved", "Status of the job has been updated", "success");
                                        $("#myModal").modal('hide');
                                        table.ajax.reload();
                                    } else {
                                        alert(data.message);
                                    }
                                },
                                fail: function(response) {
                                    alert('here');
                                },
                                error: function(xhr, textStatus, thrownError) {
                                    associate_errors(xhr.responseJSON.errors, $form);
                                },
                                contentType: false,
                                processData: false,
                            });

                        } else {
                            $("#myModal").modal('hide');
                            return false;

                        }
                    });
            } else {
                var job_id = $('#job-action-form input[name="id"]').val()
                var url = "{{ route('recruitment-job.update-status',':job_id') }}";
                url = url.replace(':job_id', job_id);
                var formData = new FormData($('#job-action-form')[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        if (data.success) {
                            swal("Saved", "Status of the job has been updated", "success");
                            $("#myModal").modal('hide');
                            table.ajax.reload();
                        } else {
                            alert(data.message);
                        }
                    },
                    fail: function(response) {
                        alert('here');
                    },
                    error: function(xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form);
                    },
                    contentType: false,
                    processData: false,
                });

            }
        });
        @endcan
        @can('rec-archive-job')
        $('.dataTable').on('click', '#select_all', function() {
            var rows = table.rows({
                'search': 'applied'
            }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked).trigger('change');
        });
        $('.dataTable').on('change', '.archive-button-trigger', function() {
            ($('input.archive-button-trigger:checkbox:checked').length > 0) ? ($('.archive').show()) : ($('.archive').hide());
        });
        $('.archive').on('click', function() {
            job_ids = [];
            $("#jobs-table input[name=job_id]:checked").each(function() {
                job_ids.push($(this).val());
            });
            job_ids = (JSON.stringify(job_ids));
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('recruitment-job.archive') }}",
                type: 'POST',
                data: {
                    'job_ids': job_ids
                },
                success: function(data) {
                    if (data.success) {
                        table.ajax.reload();
                        $('.archive').hide();
                    }
                }
            });
        });
        @endcan
        table.on('click', function() {
            refreshSideMenu();
        });
    });

    $(".client-filter").change(function() {
        var table = $('#jobs-table').DataTable();
        table.ajax.reload();
    });

    $('table').on('mouseover', '#popover', function(e) {
        $(e.target).popover('show');
    });
    $('table').on('mouseout', '#popover', function(e) {
        $(e.target).popover('hide');
    });
    $("#myModal").on("hidden.bs.modal", function() {
        $("#myModal").find('form').trigger('reset');
    });
</script>
@stop