@extends('layouts.app')
@section('content')
<style>
    .profileImage {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
    }

    .candidate-image-div img {
        transition: transform .5s, filter 1.5s ease-in-out;
    }

    /* [3] Finally, transforming the image when container gets hovered */
    .candidate-image-div:hover img {
        z-index: 9999999;
        transform:scale(5);
        -ms-transform:scale(5); /* IE 9 */
        -moz-transform:scale(5); /* Firefox */
        -webkit-transform:scale(5); /* Safari and Chrome */
        -o-transform:scale(5); /* Opera */
        position: relative;
    }
</style>
<div class="table_title">
    <h4> Candidate Summary
    <?php
$selected_customer_ids = (new \App\Services\HelperService())->getCustomerIds();
if (!empty($selected_customer_ids)) {
    echo '<button type="button" class="dashboard-filter-customer-reset btn btn-primary float-right"> Reset Filter</button>';
}
?>
    </h4>
</div>

<table class="table table-bordered" id="candidates-table">

    <thead>
        <tr>
            @can('candidate-delete-job-application')
            <th class="dt-body-center">
                <input id="select_all" value="1" type="checkbox" />
            </th>
            @endcan
            <th class="sorting">Client Name</th>
            <th class="sorting">Candidate Name</th>
            <th class="sorting">Current Employer</th>
            <th class="sorting">Current Wage (hourly)</th>
            <th class="sorting">Image</th>
            <th class="sorting">City</th>
            <th class="sorting">Postal Code</th>
            <th class="sorting">YOE</th>
            <th class="sorting">Wage Expectation</th>
            <th class="sorting">Date Applied</th>
            <th class="sorting">Email Address</th>
            <th nowrap class="sorting">Phone</th>
            <th class="sorting">Status</th>
            <th class="sorting">Overall Impression</th>
            @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view'])
            <th>Actions</th>
            @endcan
        </tr>
    </thead>
</table>
@can('candidate-delete-job-application')
{{ Form::button('Remove', array('class'=>'button btn archive submit','value'=>'archive','style'=>'display:none;'))}}
@endcan
@can('candidate-approval')
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url'=>'#','id'=>'candidate-action-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('candidate_status') ? 'has-error' : '' }}" id="candidate_status">
                    <label for="candidate_status" class="col-sm-12 control-label">Choose Status</label>
                    <div class="col-sm-12">
                        {{ Form::select('candidate_status', [null=>'Please Select','Proceed'=>'Proceed','Reject'=>'Reject'], null,array('class' =>'form-control','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('feedback_id') ? 'has-error' : '' }}" id="feedback_id">
                    <label for="feedback_id" class="col-sm-12 control-label">Overall Impression</label>
                    <div class="col-sm-12">
                        {{ Form::select('feedback_id', [null=>'Please Select']+$feedbackLookups, null,array('class' => 'form-control','required'=>true))}}
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
@stop @section('scripts')
<script>
     var tracking_users_arr = {{ json_encode($tracking_users_arr) }}
    $(function () {
        var table = $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('candidate.screening-summary-list') }}",
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                        columns: [0, 1, 2,3, 4, 6, 7, 8, 9, 10, 11,12,13,14],
                        @endcan
                    }
                },
                 {
                text: 'Excel',
                    action: function ( e, dt, node, config ) {
                        window.location.href = '{{ route('candidate.candidate-export') }}';
                    }
                },
                /*{
                    extend: 'excelHtml5',
                    exportOptions: {
                        @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                        columns: 'th:not(:last-child)',
                        @endcan
                    }
                },*/
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                        columns: 'th:not(:last-child)',
                        @endcan
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [7, "desc"]
            ],
            @can('candidate-delete-job-application')
            order: [
                [10, "desc"]
            ],
            @endcan

            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).addClass(data.status);
                if(data.termination!=null)
                {
                    $(row).addClass('archived');
                }
            },
            @can('candidate-delete-job-application')
            columnDefs: [{
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    sortable: false,
                    className: 'dt-body-center',
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox" id="candidate_id" name="candidate_id" class="archive-button-trigger" value="' +
                            $('<div/>').text(data).html() + '">';
                    }
                },
                {
                    targets: 9,
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).css('white-space', 'nowrap');
                    }
                }
            ],
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
                @can('candidate-delete-job-application')
                {
                    data: 'id',
                    name: 'id'
                },
                @endcan
                {
                    data: null,
                    name: 'latest_job_applied.job.customer.client_name',
                    render: function (row) {
                        actions = '';
                        var view_url = '{{ route("job.view", ":id") }}';
                        view_url = view_url.replace(':id', row.latest_job_applied.job_id);
                        actions += '<a title="View" href="' + view_url + '">' + row.latest_job_applied.job.customer.client_name + '</a>';
                        return actions;
                    }
                },
                {
                    data: null,
                    name: 'name',
                    render: function (row) {
                        actions = '';
                        var url = '{{ route("candidate.view", [":candidate_id",":job_id"]) }}';
                        url = url.replace(':candidate_id', row.id);
                        url = url.replace(':job_id', row.latest_job_applied.job_id);
                        actions += '<a title="View application" href="' + url + '">' + row.name + '</a>';
                        return actions;
                    }
                },
                {
                    data:null,
                    name:'employment_history_latest.employer',
                    render:function(row){
                        if(row.employment_history_latest != null && row.employment_history_latest != "") {
                            return row.employment_history_latest.employer;
                        }else{
                            return "--";
                        }
                    }
                },
                {
                    data:null,
                    name:'wage_expectation.wage_last_hourly',
                    render:function(row){
                        if(row.wage_expectation != null && row.wage_expectation != "") {
                            console.log(row)
                            // debugger
                            if((row.wage_expectation).wage_last_hourly>0){
                                return "$"+parseFloat((row.wage_expectation).wage_last_hourly).toFixed(2);
                            }else{
                                return "--";
                            }
                        }
                    }
                },
                {
                    data: null,
                    name:'profile_image',
                    render: function (row) {
                        if(row.profile_image != null && row.profile_image != "") {
                            let image = "{{asset('images/uploads/') }}/" + row.profile_image;
                            return '<div id="candidate-image-div" class="candidate-image-div" style="width:10% !important;"><img name="image" src="'+image+'"  class="profileImage"></div>';
                        }else{
                            let image = "{{asset('images/uploads/') }}/{{ config('globals.noAvatarImg') }}";
                            return '<div id="candidate-image-div" class="candidate-image-div" style="width:10% !important;"><img name="image" src="'+image+'"  class="profileImage"></div>';
                        }

                    }
                },
                {
                    data: 'city',
                    name: 'city'
                },
                {
                    data: 'postal_code',
                    name: 'postal_code'
                },

                {
                    data: 'guarding_experience.years_security_experience',
                    name: 'guarding_experience.years_security_experience'
                },
                {
                    data: null,
                    name: 'wage_expectation.wage_expectations_from',
                    render: function (row) {
                        return 'Low: $' + parseFloat(row.wage_expectation.wage_expectations_from).toFixed(2) + '\r\n<br/>High: $' + parseFloat(row.wage_expectation.wage_expectations_to).toFixed(2);
                    },
                },
                {
                    data: 'latest_job_applied.submitted_date',
                    name: 'latest_job_applied.submitted_date'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: null,
                    name: 'phone_home',
                    render: function (row) {
                        phone_home = row.phone_home;
                        phone_cellular = row.phone_cellular;
                        phone_home = (null != phone_home) ? (phone_home.split(')').join(') ')) :'';
                        phone_cellular = (null != phone_cellular) ? phone_cellular.split(')').join(') ') : '';
                        return phone_home + '\r\n<br/>' + phone_cellular;
                    }
                },
                {
                    data: null,
                    name: 'latest_job_applied.candidate_status',
                    defaultContent: "--",
                    render: function (row) {
                        var td_data='';
                        if((row.termination!=null))
                        {
                            return td_data +="Terminated";
                        }
                        if (row.latest_job_applied.candidate_status == 'Proceed')
                        {
                            var url = '{{ route("candidate.review", [":candidate_id",":job_id"]) }}';
                            url = url.replace(':candidate_id', row.id);
                            url = url.replace(':job_id', row.latest_job_applied.job_id);
                            td_data += '<a class="underline" title="Click here to review screening question/add interview notes" href="' + url + '">Proceed</a>';
                        }
                        else if(row.latest_job_applied.candidate_status!=null)
                        {
                            td_data +=row.latest_job_applied.candidate_status;
                        }else if(row.latest_job_applied.candidate_status === null)
                        {
                            td_data +='Not Set';
                        }

                        return td_data;

                    }
                },
                {
                    data: 'latest_job_applied.feedback.feedback',
                    name: 'created_at',
                    defaultContent: "--",
                    sortable: true,
                },
                /*{
                    data: null,
                    name: null,
                    render: function(row){
                        return ((row.termination==null)?'Active':'Terminated');
                    },
                    defaultContent: '--',
                    visible: false
                },*/
                @canany(['candidate-screening-summary','candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                {
                    data: null,
                    sortable: false,
                    render: function (row) {
                        actions = '';
                        @can('candidate-screening-summary')
                            actions += '<a title="Print application" onclick="print_tab(' + row.latest_job_applied.id + ')" href="#" class="fa fa-print fa-lg link-ico"></a>'
                        @endcan
                        @can('candidate-approval')
                            actions += '<a title="Process" onclick="openModal(' + row.latest_job_applied.id +')" href="#" class="fa fa-podcast fa-lg link-ico"></a>';
                        @endcan
                        @can('edit-candidate')
                        var url ='{{ route("candidate.edit",array(":candidate_id",":job_id")) }}';
                        url = url.replace(':candidate_id', row.id);
                        url = url.replace(':job_id', row.latest_job_applied.job_id);
                        actions += '<a title="Edit" href="' + url +'" class="fa fa-edit fa-lg link-ico"></a>';
                        @endcan
                        @canany(['hr-tracking','hr-tracking-detailed-view','track_all_candidates'])

                        if (row.latest_job_applied.candidate_status == 'Proceed' && $.inArray(parseInt(row.id), tracking_users_arr)!==-1) {

                            var tracking_process_url ='{{ route("candidate.track",array(":candidate_id",":job_id")) }}';
                            tracking_process_url = tracking_process_url.replace(':candidate_id',row.id);
                            tracking_process_url = tracking_process_url.replace(':job_id', row.latest_job_applied.job_id);
                            actions += '<a title="Track Candidate" href="' +tracking_process_url +'" class="fa fa-compress fa-lg link-ico"></a>';
                        }
                        @endcan
                        return actions;
                    }
                }
                @endcan
            ]
        });

        @can('candidate-approval')
        $('#candidate-action-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('candidate.primary-screening') }}";
            var formData = new FormData($('#candidate-action-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal("Saved", "Status of this job application has been updated",
                            "success");
                        $("#myModal").modal('hide');
                        table.ajax.reload();
                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        });
        @endcan

        @can('candidate-delete-job-application')
        $('.dataTable').on('click', '#select_all', function () {
            var rows = table.rows({'search': 'applied'}).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked).trigger('change');
        });
        $('.dataTable').on('change', '.archive-button-trigger', function () {
            ($('input.archive-button-trigger:checkbox:checked').length > 0) ? ($('.archive').show()) : ($('.archive').hide());
        });
        $('.archive').on('click', function () {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: true
                },
                function () {
                    candidate_ids = [];
                    $("#candidates-table input[name=candidate_id]:checked").each(function () {
                        candidate_ids.push($(this).val());
                    });
                    candidate_ids = (JSON.stringify(candidate_ids));
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('candidate.archive') }}",
                        type: 'POST',
                        data: {
                            'candidate_ids': candidate_ids
                        },
                        success: function (data) {
                            if (data.success) {
                                table.ajax.reload();
                                $('.archive').hide();
                            }
                        }
                    });
                });
            });
        @endcan
    });

    function print_tab(job_id) {
        var urlprint ='{{ route("candidate-job.print-view",array(":cand_job_id")) }}';
        urlprint = urlprint.replace(':cand_job_id', job_id);
        window.open(urlprint);
    }
</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
