@extends('layouts.app')
@section('content')
    {{--{{dd($dispatchRequestShows->dispatchRequestType->name)}}--}}
    {{--{{dd($dispatchRequestShows->customer->client_name)}}--}}
    <div class="table_title">
        <h4>Dispatch Request</h4>
    </div>
    <div class="d-flex justify-content-center align-items-center" id="close-div">
    @if(isset($dispatchRequestShows) && ($dispatchRequestShows->dispatch_request_status_id != 4))
    <a href="#" id="add-new-button" data-id="{{$id}}"><div class="add-new" data-title="Add New Customer">Close<span class="add-new-label"> Status</span></div></a>
    @endif
    </div>


    <div class="container">
        <div class="row">
            <div class="col-md-6">

                <p> Subject : <span
                            style="color: black;font-weight: bold">{{ ($dispatchRequestShows->subject) ? $dispatchRequestShows->subject : '' }}</span>
                </p>


                <p> Issue Type : <span
                            style="color: black;font-weight: bold">{{ ($dispatchRequestShows->dispatchRequestType) ? $dispatchRequestShows->dispatchRequestType->name : '' }}</span>
                </p>


                <p> Customer Name : <span
                            style="color: black;font-weight: bold">{{ ($dispatchRequestShows->customer) ? $dispatchRequestShows->customer->client_name : '' }}</span>
                </p>


                <p> Site Address : <span
                            style="color: black;font-weight: bold">{{ ($dispatchRequestShows->site_address) ? $dispatchRequestShows->site_address : '' }}</span>
                </p>

                <p> Postal Code : <span
                            style="color: black;font-weight: bold">{{ ($dispatchRequestShows->site_postalcode) ? $dispatchRequestShows->site_postalcode : '' }}</span>
                </p>
                <p> Rate : <span
                            style="color: black;font-weight: bold">{{ ($dispatchRequestShows->rate) ? $dispatchRequestShows->rate : '' }}</span>
                </p>
            </div>

            <div class="col-md-6">
                <p> Status : <span
                            style="color: black;font-weight: bold">
                        @if($dispatchRequestShows->dispatch_request_status_id == 1)
                            Open
                        @elseif($dispatchRequestShows->dispatch_request_status_id == 2)
                            In-progress
                        @elseif($dispatchRequestShows->dispatch_request_status_id == 3)
                            Arrived and started investigation
                        @elseif($dispatchRequestShows->dispatch_request_status_id == 4)
                            Closed
                        @endif

                            </span>
                </p>
                @if($dispatchRequestShows->description !='')
                    <p> Description : <span
                                style="color: black;font-weight: bold">{{ ($dispatchRequestShows->description) ? $dispatchRequestShows->description : '' }}</span>
                    </p>
                @endif
                @if($dispatchRequestShows->respond_by !='')
                    <p> Respond By : <span
                                style="color: black;font-weight: bold">{{ ($dispatchRequestShows->respondby) ? $dispatchRequestShows->respondby->first_name.' '.$dispatchRequestShows->respondby->last_name : '' }}</span>
                    </p>
                @endif
                @if($dispatchRequestShows->estimated_time !='')
                    <p> Estimated Time : <span
                                style="color: black;font-weight: bold">{{ ($dispatchRequestShows->estimated_time) ? $dispatchRequestShows->estimated_time : '' }}</span>
                    </p>
                @endif
                @if($dispatchRequestShows->actual_time !='')
                    <p> Actual Time : <span
                                style="color: black;font-weight: bold">{{ ($dispatchRequestShows->actual_time) ? $dispatchRequestShows->actual_time : '' }}</span>
                    </p>
                @endif
                @if($dispatchRequestShows->delta !='')
                    <p> Delta : <span
                                style="color: black;font-weight: bold">{{ ($dispatchRequestShows->delta) ? $dispatchRequestShows->delta : '' }}</span>
                    </p>
                @endif

            </div>
        </div>

    </div>
    <br>
    <br>
    <div class="table_title">
        <h4>Dispatch Request Declined List</h4> <br>
    </div>
    <table class="table table-bordered" id="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Reason</th>
            <th>Date</th>
        </tr>
        </thead>
    </table>

@stop

@section('scripts')
    <script>

        // for request decline
        $(function () {
            $.fn.dataTable.ext.errMode = 'throw';

            try {
                var base_url = "{{ route('dispatch_request.decline_list',':id') }}";
                var url = base_url.replace(':id', {{$id}});
                console.log('data');
                var table = $('#table').DataTable({
                    dom: 'lfrtBip',
                    bprocessing: false,
                    buttons: [
                        {
                            extend: 'pdfHtml5',
                            text: ' ',
                            className: 'btn btn-primary fa fa-file-pdf-o',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: ' ',
                            className: 'btn btn-primary fa fa-file-excel-o',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            extend: 'print',
                            text: ' ',
                            className: 'btn btn-primary fa fa-print',
                            exportOptions: {
                                columns: [0, 1, 2, 3]
                            }
                        },
                        {
                            text: ' ',
                            className: 'btn btn-primary fa fa-envelope-o',
                            action: function (e, dt, node, conf) {
                                emailContent(table, 'Course');
                            }
                        }
                    ],
                    processing: false,
                    serverSide: true,
                    responsive: true,
                    ajax: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    order: [
                        [1, "asc"]
                    ],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    columns: [{
                        data: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },

                        {
                            data: 'user.username',
                            name: 'user.username'

                        },
                        {
                            data: 'comment',
                            name: 'comment'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        }

                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }
     /* Status change - Start */

        $('#add-new-button').on('click', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('dispatchrequest.statusclose',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Security clearance has been deleted successfully';
            deleteRecord(url, table, message);
        });

    /* Status change  - End */

    /*Close Status- Start */

    function deleteRecord(url, table, message) {
        var url = url;
        var table = table;
        swal({
            title: "Are you sure?",
            text: "You will not be able to undo this action. Proceed?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, close",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },
        function () {
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                        // swal("Closed", message, "success");
                        // if (table) {
                            location.reload();
                        // }
                    }else if(data.success == false){
                        if(Object.prototype.hasOwnProperty.call(data,'message') && data.message){
                            swal("Warning", data.message, "warning");
                        }else{
                            swal("Warning", 'Data exists', "warning");
                        }                       
                    } else {
                        console.log(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });
}
/* Close Status - End */


        });
       
    </script>
    /* style start */
    <style>
    #close-div{
        margin-left: 1000px;
    }
    </style>
@stop

