@extends('layouts.app')
@section('css')
    <style>
        .gj-modal .col-md-4{
    color: #38393a !important;
}

    </style>
@endsection
@section('content')
<div class="container-fluid" style="margin-top:-7px;padding: 3px !important">

    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-10  table_title"><h4 style="margin:0px !important">Facility Signout</h4></div>
        <div class="col-md-2 table_title" style="text-align: right">


        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-10  table_title"></div>
        <div class="col-md-2 table_title" style="text-align: right">
            @canany(['manage_all_customer_facility','manage_allocated_customer_facility'])
                <button id="addnewfac"  class="btn btn-primary" style="">Add New Facility Signout</button>
            @endcanany

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="amenitytable" class="table table-bordered">
                <thead>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    @foreach ($facilities as $facility)
                        <tr><td>{{$facility->facility}}</td>
                        <td>{{$facility->description}}</td>
                        <td>{{$facility->customer->client_name}} ({{$facility->customer->project_number}})</td>
                        <td>
                            @if ($facility->active==1)
                            Active
                            @else
                            Inactive

                            @endif
                        </td>
                        <td>
                            @if ($facility->single_service_facility!=1)
                                <a style="padding-right:7px " title="Manage services" href="manageservice/{{$facility->id}}" class="edit fa fa-cog" data-id="{{$facility->id}}"></a>
                            @endif
                            @canany(['manage_all_customer_facility','manage_allocated_customer_facility'])
                                @if (!\Auth::user()->can('manage_all_customer_facility'))
                                    @if (in_array($facility->customer_id,$customers))
                                        <a style="padding-right:7px " href="editfacilities/{{$facility->id}}"
                                        class="edit fa fa-edit" data-id="{{$facility->id}}"></a>

                                    @endif

                                @else
                                    <a style="padding-right:7px" href="editfacilities/{{$facility->id}}" class="edit fa fa-edit" data-id="{{$facility->id}}"></a>

                                @endif

                            @endcanany
                            @canany(['remove_customer_facility'])
                                <a class="removefacility fa fa-trash" data-id="{{$facility->id}}" style="cursor: pointer;"></a>
                            @endcanany



                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            $("#amenitytable").DataTable({
                autoWidth: false,
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                "columnDefs":[
                    {"width":"20%", "targets": 0},
                    {"width":"40%", "targets": 1},
                    {"width":"20%", "targets": 2}]
            });
        });

        $(document).on("click","#addnewfac",function(e){
            location.href="{{route('cbs.addfacility')}}"
        })

        $(document).on("click",".removefacility",function(e){
            e.preventDefault();
            var facilityid = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action. Proceed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes',
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){

                if (isConfirm){
                    $.ajax({
                    type: "post",
                    url: '{{route("cbs.removefacility")}}',
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {id:facilityid},

                    success: function (response) {
                        var data = jQuery.parseJSON(response);
                        $(function(e){
                            if(data.code==200){
                                swal({
                                    title: "Deleted",
                                    text: data.message,
                                    type: "success"
                                    }, function() {
                                        location.reload();
                                    });
                            }else{
                                swal("Warning",data.message,"warning");
                            }
                        })

                    }
                });


                } else {
                    swal.close()
                    e.preventDefault();
                }
            });


        })
    </script>
@endsection
