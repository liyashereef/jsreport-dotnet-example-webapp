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
            <th class="dt-body-center">
            </th>
            <th class="sorting">Candidate Name</th>
            <th class="sorting">Image</th>
            <th class="sorting">City</th>
            <th class="sorting">Postal Code</th>
            <th class="sorting">Year of Security Experience</th>
            <th class="sorting">Last Wage</th>
            <th class="sorting">Wage Expectation</th>
            <th class="sorting">Application Date</th>
            <th class="sorting"></th>
            <th class="sorting">Cycle Time </th>
            {{-- <th class="sorting">Date Applied</th> --}}
            <th class="sorting">Email Address</th>
            <th nowrap class="sorting">Phone</th>
          {{--   <th class="sorting">Status</th> --}}
            <th class="sorting">Overall Impression</th>
            <th class="sorting">Process Step</th>
            <th class="sorting">Description</th>
            @canany(['rec-candidate-approval','rec-edit-candidate','rec-hr-tracking','rec-hr-tracking-detailed-view'])
            <th>Actions</th>
            @endcan
        </tr>
    </thead>
</table>

{{ Form::button('Compare', array('class'=>'button btn compare submit','value'=>'compare','style'=>'display:none;'))}}

{{-- @can('candidate-approval') --}}
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
{{-- @endcan --}}
@stop
@section('scripts')
<script>
    var tracking_users_arr = {{ json_encode($tracking_users_arr) }}
    $(function() {
        var table = $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            dom: 'Blfrtip',
            ajax: "{{ route('recruitment.candidate.summarylist') }}",
            buttons: [
                {
                    extend: 'pdf',
                    pageSize: 'A2',
                    exportOptions: {
                        @canany(['rec-candidate-approval','rec-edit-candidate','rec-hr-tracking','rec-hr-tracking-detailed-view','rec-track_all_candidates'])
                        columns: [ 0, 2, 3,4,5,6,7,9,10,11,12,13,14 ]
                        @endcan
                    }
                },
                 {
                text: 'Excel',
                    action: function ( e, dt, node, config ) {
                        window.location.href = '{{ route('recruitment.candidate.candidate-export') }}';
                    }
                },
                // {
                //     extend: 'excelHtml5',
                //     exportOptions: {
                //         @canany(['rec-candidate-approval','rec-edit-candidate','rec-hr-tracking','rec-hr-tracking-detailed-view','rec-track_all_candidates'])
                //         columns: 'th:not(:last-child)',
                //         @endcan
                //     }
                // },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        @canany(['rec-candidate-approval','rec-edit-candidate','rec-hr-tracking','rec-hr-tracking-detailed-view','rec-track_all_candidates'])
                        columns: [ 0,1, 2, 3,4,5,6,7,9,10,11,12,13,14 ],
                        @endcan
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "asc"]
            ],
            @can('rec-candidate-delete-job-application')
            // order: [
            //     [7, "desc"]
            // ],
            @endcan
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columnDefs: [{
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    sortable: false,
                    className: 'dt-body-center',
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox" id="candidate_id" name="candidate_id" class="compare-button-trigger" value="' +
                            $('<div/>').text(data).html() + '">';
                    }
                },
            { width: 50, targets: 4 }
            ],

            createdRow: function (row, data, dataIndex) {
                $(row).addClass(data.status);
                if(data.termination!=null)
                {
                    $(row).addClass('archived');
                }
            },
        
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                 {
                    data: null,
                    name: 'name',
                    render: function (row) {
                        actions = '';
                        var url = '{{ route("recruitment.candidate.view", [":candidate_id"]) }}';
                        url = url.replace(':candidate_id', row.id);
                        actions += '<a title="View application" href="' + url + '">' + row.name + '</a>';
                        return actions;
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
                    data: 'years_security_experience',
                    name: 'years_security_experience',
                    defaultContent:'--',

                },
                 {
                    data: null,
                    name: 'last_wage',
                    defaultContent:'--',
                    render: function (row) {
                        if(row.last_wage != null){
                        return '$' + parseFloat(row.last_wage).toFixed(2);
                        }
                    },
                },

                {
                    data: null,
                    name: 'wage_expectations',
                    defaultContent:'--',
                    render: function (row) {
                        if(row.wage_expectations != null){
                        return '$' + parseFloat(row.wage_expectations).toFixed(2);
                        }
                    },
                },
                 {
                    data: 'application_date',
                    name: 'application_date_unformatted'
                },
                {
                    data: 'application_date',
                    name: 'application_date',
                    visible:false
                },
                 {
                    data: 'cycle_time',
                    name: 'cycle_time'
                },

                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: null,
                    name: 'phone',
                    render: function (row) {
                        phone_home = row.phone;
                        phone_cellular = row.phone_cellular;
                        phone_home = (null != phone_home) ? (phone_home.split(')').join(') ')) :'';
                        phone_cellular = (null != phone_cellular) ? phone_cellular.split(')').join(') ') : '';
                        return phone_home + '\r\n<br/>' + phone_cellular;
                    }
                },
                // {
                //     data: null,
                //     name: 'candidate_status',
                //     defaultContent: "--",
                //     render: function (row) {
                //         var td_data='';
                //         if((row.termination!=null))
                //         {
                //             return td_data +="Terminated";
                //         }
                //         if (row.candidate_status == 'Proceed')
                //         {
                //             // var url = '{{ route("candidate.review", [":candidate_id",":job_id"]) }}';
                //             // url = url.replace(':candidate_id', row.id);
                //             // url = url.replace(':job_id', row.latest_job_applied.job_id);
                //             // td_data += '<a class="underline" title="Click here to review screening question/add interview notes" href="' + url + '">Proceed</a>';
                //             td_data += '<a class="underline" title="Click here to review screening question/add interview notes" href=" ">Proceed</a>';
                //         }
                //         else if(row.candidate_status!=null)
                //         {
                //             td_data +=row.candidate_status;
                //         }else if(row.candidate_status === null)
                //         {
                //             td_data +='Not Set';
                //         }

                //         return td_data;

                //     }
                // },
                {
                    data: 'feedback',
                    name: 'feedback',
                    defaultContent: "--",
                    sortable: true,
                },
                {
                    data: 'tracking_step_id',
                    name: 'tracking_step_id',
                    defaultContent: "--",

                },
                  {
                    data: 'tracking_name',
                    name: 'tracking_name',
                    defaultContent: "--",

                    },
                    

                @canany(['rec-candidate-approval','rec-edit-candidate','rec-hr-tracking','rec-hr-tracking-detailed-view','rec-track_all_candidates'])
                {
                    data: null,
                    sortable: false,
                    searchable:false,
                    render: function (row) {
                        actions = '';
                        @can('rec-candidate-screening-summary')
                            actions += '<a title="Print application" onclick="print_tab(' + row.awareness_id + ')" href="#" class="fa fa-print fa-lg link-ico"></a>'
                        @endcan
                        if(row.awareness_average_score!=0){
                        @can('rec-candidate-approval')

                            actions += '<a title="Process" onclick="openModal('+ row.awareness_id +')" href="#" class="fa fa-podcast fa-lg link-ico"></a>';
                        @endcan
                        }

                        @can('rec-edit-candidate')
                        var url ='{{ route("recruitment.candidate.edit",array(":candidate_id")) }}';
                        url = url.replace(':candidate_id', row.id);
                        actions += '<a title="Edit" href="' + url +'" class="fa fa-edit fa-lg link-ico"></a>';
                        @endcan

                        @canany(['rec-hr-tracking','rec-hr-tracking-detailed-view','rec-track_all_candidates'])

                        // if (row.latest_applied.candidate_status == 'Proceed' && $.inArray(parseInt(row.id), tracking_users_arr)!==-1) {
                        // if ( $.inArray(parseInt(row.id), tracking_users_arr)!==-1) {

                            var tracking_process_url ='{{ route("recruitment.candidate.track",array(":candidate_id")) }}';
                            tracking_process_url = tracking_process_url.replace(':candidate_id',row.id);

                            actions += '<a title="Track Candidate" href="' +tracking_process_url +'" class="fa fa-compress fa-lg link-ico"></a>';
                        // }
                        @endcan
                        return actions;
                    }
                }
                @endcan
            ]
        });

        @can('rec-candidate-approval')
        $('#candidate-action-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            url = "{{ route('recruitment.candidate.primary-screening') }}";
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



        $('.dataTable').on('change', '.compare-button-trigger', function () {
            ($('input.compare-button-trigger:checkbox:checked').length > 1) ? ($('.compare').show()) : ($('.compare').hide());
        });
        $('.compare').on('click', function () {
                    candidate_ids = [];
                    $("#candidates-table input[name=candidate_id]:checked").each(function () {
                        candidate_ids.push($(this).val());
                    });
                    candidate_ids = (JSON.stringify(candidate_ids));
                    window.location.href = "{{route("recruitment.candidate.compare",'') }}/"+candidate_ids;

                });
     });

    function print_tab(job_id) {
        var urlprint ='{{ route("recruitment.candidate-job.print-view",array(":cand_job_id")) }}';
        urlprint = urlprint.replace(':cand_job_id', job_id);
        window.open(urlprint);
    }
</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
