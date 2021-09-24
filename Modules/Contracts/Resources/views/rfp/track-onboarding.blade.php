@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('css')
    <style>
        .section-header {
            background-color: #f36424;
            border: 1px solid #e9ecef;
            border-bottom: none;
        }

        .section-data {
            color: white;
            padding: 10px 18px;
            font-weight: bold;
        }

        .section-track {
            padding: 15px;
        }

        .add-remove-btn {
            margin-top: 10px;
        }

        td.action-data {
            width: 40px;
            padding: 18px!important;
        }

        .table thead th,
        table thead td {
            color: #ffffff;
            border-bottom: 1px solid #003A63;
        }

        .table thead th,
        table tfoot th {
            font-weight: 600;
            font-size: 15px;
        }

        .table-bordered td,
        .table-bordered td a,
        .table-bordered th {
            font-size: 14px;
            color: #003A63;
        }

        .table-bordered th {
            background: #003A63;
        }

        table.no-footer {
            border-bottom: none;
        }

        .fa {
            color: #f48452 !important;
        }

        .table-bordered td a:hover {
            color: #003A63;
        }

        .dataTables_length select {
            width: 100px;
            padding: 5px;
            color: #f48452;
            border: 1px solid #DDE9ED;
        }

        .table tbody tr {
            background-color: #dde9ed;
        }

        .table tbody tr td{
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
<div class="table_title">
    <h4>Onboarding Stages</h4>
</div>

{{ Form::open(array('url'=>'#','id'=>'client-onboarding-tracking-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{csrf_field()}}
{{ Form::hidden('id', null) }}
 <div id="form-container">

     <div id="onboarding-div-id">
         <div class="section-task-container el_fields container-fluid" id="_row">
             {{ Form::hidden('rfpDetailsId',$rfpDetails->id) }}
             {{ Form::hidden('client-onboarding-id',$onBoardingDetails->id) }}
             @foreach ($onBoardingDetails->section  as $sectionKey => $eachSection)
                 {{ Form::hidden('client-section-id[]',$eachSection->id) }}
                 <div class="row section-header">
                 <div class="col-sm-12 section-data"> {{$eachSection->section}} </div>
             </div>
             <div class="row">
                 <table class="table table-bordered">
                     <thead>
                     <th style="width: 50%">Process Step</th>
                     <th style="width: 15%">Percentage Completed</th>
                     <th style="width: 15%">Target Date</th>
                     <th style="width: 20%">Assigned To</th>
                     </thead>
                     <tbody id="step_row">
                     @forelse ($eachSection->step as $stepKey => $eachStep)
                     <tr class="client-step"  >
                         {{ Form::hidden("client-step-id[$sectionKey][]",$eachStep->id) }}
                         <td>
                             {{$eachStep->step}}
                         </td>
                         <td>
                             @can('update_client_onboarding_step_status')
                             {{ Form::select("client-step-percentage[$sectionKey][]",$percentArray,$eachStep->percentage_completed,
                                array('class'=>'form-control','placeholder'=>'Please Select','required'=>'required')) }}
                             @elsecanany(['view_assigned_client_onboarding_steps','view_all_client_onboarding_steps'])
                                 {{$eachStep->percentage_completed}}
                             @endcan
                         </td>
                         <td>
                             {{\Carbon\Carbon::createFromFormat('Y-m-d',$eachStep->target_date)->format('l, F d Y')}}
                         </td>
                         <td>
                             {{$eachStep->assignedTo->first_name." ".$eachStep->assignedTo->last_name." (".$eachStep->assignedTo->employee->employee_no.")"}}
                         </td>
                     </tr>
                     @empty
                         <td colspan="4">No data assigned</td>
                     @endforelse
                     </tbody>
                 </table>
             </div>
             @endforeach
         </div>
     </div>


</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">

    {{ Form::submit('Save', array('class' => 'btn submit')) }}

    {{ Form::button('Cancel', array('class' => 'btn cancel','onclick'=>'window.history.back();')) }}
</div>
{{ Form::close() }}


@stop
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>

<script>
$(function () {
$('#client-onboarding-tracking-form').submit(function (e) {
    e.preventDefault();
    var $form = $(this);
    $form.find('td').removeClass('has-error').find('.help-block').text('');
    var formData = new FormData($('#client-onboarding-tracking-form')[0]);

    var url =
        "{{ route('rfp.store-client-onboarding',[$rfpDetails->id, $onBoardingDetails->id]) }}";
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
                    title: 'Success',
                    text: 'Tracking step has been successfully updated',
                    icon: "success",
                    button: "Ok",

                }, function () {
                    window.location = "{{ route('rfp.summary') }}";
                });
            } else {
                alert(data.message);
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


   $('#rfp-status-form').on('click', '.delete', function (e) {
            lookup_id = $(this).data('id');
            rfp_id = $(this).data('rfp-id');
            var url = '{{route("remove-rfp-tracking-step",[":lookup_id",":rfp_id"])}}',
            url = url.replace(':lookup_id', lookup_id);
            url = url.replace(':rfp_id', rfp_id);
            swal({
                    title: "Are you sure?",
                    text: "You will not be able undo this action!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove!",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal({
                                    title: 'Success',
                                    text: 'The RFP Tracking step has been successfully removed',
                                    icon: "success",
                                    button: "Ok",
                                }, function () {
                                    location.reload();
                                });
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
        });
});
</script>
@endsection
