@extends('layouts.app') @section('content')
<div class="table_title">
    <h4>Schedule Details</h4>
</div>
{{ Form::open(array('url'=>'#','id'=>'event-log-form','class'=>'form-horizontal', 'method'=> 'POST')) }}

<section class="row content-block">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 title-header-align form-panel">
        {{--
        <div class="formpanel-header">
            <h4 class="color-white">Customer Details</h4>
        </div> --}}
        <div class="row" id="customer-details">
            <div class="col-md-12 col-sm-12 xs-12 col-lg-12">
                <div class="row">
                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Project Number</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" placeholder="Project Number" name="project_number" value="{{$stcprojectdetails->customer->project_number or '--'}}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Site Address</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" value="{{$stcprojectdetails->customer->address or '--'}}" placeholder="Site Address" name="site_address"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Client Name</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" placeholder="Client Name" value="{{$stcprojectdetails->customer->client_name or '--'}}" name="client_name"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Site Postal Code</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" value="{{$stcprojectdetails->customer->postal_code or '--'}}" placeholder="Site Postal Code"
                                    name="postal_code" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-126">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Inquiry Date</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" value="{{$stcprojectdetails->inquiry_date or '--'}}" placeholder="Inquiry Date" name="inquiry_date"
                                    readonly>
                            </div>
                        </div>
                    </div>



                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Time Stamp</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" class="form-control" value="{{$stcprojectdetails->inquiry_time or '--'}}" placeholder="Time Stamp" name="time_stamp"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Duty Officer</label>
                            <div class="col-md-8 col-xs-9">
                                <input type="text" value="{{$stcprojectdetails->trashed_user->full_name }}" class="form-control" placeholder="Duty Officer" name="duty_officer"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <div class="form-group row styled-form">
                            <label class="col-md-3 label-adjust col-form-label control-label col-xs-3">Site Description</label>
                            <div class="col-md-8 col-xs-9">
                                <textarea class="form-control form-rounded textarea-adjust" rows="5" name="site_description" readonly>{{$stcprojectdetails->customer->description or '--'}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <table class="table table-bordered" id="stc-table1">
            <thead>
                <tr>
                    <th>Id</th>
                    <th class="sorting">Employee Name</th>
                    <th class="sorting">Call Log</th>
                    <th class="sorting">Date</th>
                    <th class="sorting">Time</th>
                    <th class="sorting">Status</th>
                </tr>
            </thead>
        </table>
    </div>
    @stop
    @section('scripts')
    <script type="text/javascript">
        $(function () {
            var project_id = window.location.pathname.split(/\//)[2];
            var requirement_id = window.location.pathname.split(/\//)[3];
            var url = '{{ route("stcdetails.list", [$project_id,$requirement_id]) }}';
            var table = $('#stc-table1').DataTable({
                processing: false,
                fixedHeader: true,
                responsive: true,
                ajax: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                fnRowCallback: function (nRow, aData, iDisplayIndex) {
                    var info = table.page.info();
                    $('td', nRow).eq(0).html(iDisplayIndex + 1 + info.page * info.length);
                    if (aData.status_log.status == "Called And Accepted Shift") {
                        $(nRow).find('td:eq(5)').css('background-color', 'green');
                         $(nRow).find('td:eq(5)').addClass('font-color-green');
                    } else {
                        $(nRow).find('td:eq(5)').css('background-color', 'red');
                         $(nRow).find('td:eq(5)').addClass('font-color-red');
                    }
                },
                columns: [{
                        data: null,
                        name: 'id',
                        sortable: false
                    },
                    {
                        data: null,
                        name: 'user.name',
                        defaultContent: "--",
                        render: function (row) {
                            actions = '';
                            var url ='#';
                            var title ='Not Available';
                             if(row.user.candidate_transition != null){
                             title ='View';
                             url = '{{ route("candidate.view", [":candidate_id",":job_id"]) }}';
                        url = url.replace(':candidate_id', row.user.candidate_transition.candidate.id);
                        url=url.replace(':job_id', row.user.candidate_transition.candidate.latest_job_applied.job_id);
                          }
                        @can('candidate-screening-summary')
                          actions += '<a title="' + title + '" href="' + url + '">' +
                                row.user.name + '</a>';
                        @else
                          actions += row.user.name;
                        @endcan
                            return actions;
                        }
                    },
                    {
                        data: 'status_log.status',
                        name: 'status_log.status',
                        defaultContent: "--"
                    },
                    {
                        data: 'call_date',
                        name: 'call_date',
                        defaultContent: "--",
                    },
                    {
                        data: 'call_time',
                        name: 'call_time',
                        defaultContent: "--",
                    },
                    {
                        data: null,
                        name: 'status_log.status',
                        defaultContent: "--",
                        render: function (o) {
                            if (o.status_log.status == "Called And Accepted Shift") {
                                return 'Closed';
                            } else {
                                return 'Open';
                            }
                        }
                    }

                ]
            });

        });
    </script>
    @stop
