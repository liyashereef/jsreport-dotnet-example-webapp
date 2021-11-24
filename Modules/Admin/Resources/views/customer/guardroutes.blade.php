{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')


<div class="row">
    <div class="col-md-2" style="font-size:18px;padding-top:7px">Guard Routes</div>
    
    <div  class="col-md-3">
       
    </div>
    <div class="col-sm-2"><button type="button" id="fencebuttonaddnew" class="btn btn-primary">Add Routes</button></div>
</div>

@stop

@section('content')
<div id="myModal" class="modal fade" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add/Edit Fence</h4>
            </div>

            <div class="modal-body">

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-3">
                        <label> Route Name </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="routename">
                    </div>
                </div>

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-3">
                        <label> Description</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" id="routedesc" class="form-control" />
                    </div>
                </div>

               

                <div class="row plotarea" class="form-control" style="padding:5px">
                    <div class="col-md-5"></div>
                    <div class="col-md-1">
                        <button type="button" id="fencebutton" class="btn btn-primary">Save</button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="fencebuttoncancel" class="btn btn-primary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <input type="hidden" name="fencecount" id="fencecount" value="0" />
            <input type="hidden" name="booleanedit" id="booleanedit" value="0" />
            <input type="hidden" name="whichfence" id="whichfence" value="0" />
            <input type="hidden" name="addnewfence" id="addnewfence" value="-1" />
            <input type="hidden" name="customer_id" id="customer_id" value="" />
            <input type="hidden" name="renderedfence" id="renderedfence" value="0" />
            <input type="hidden" name="datatablepage" id="datatablepage" value="0" />
        </div>
    </div>
</div>
<div  class="container-fluid">
    
</div>
<div  class="container-fluid" style="height: 400px;width: 100%;overflow-y:auto;overflow-x:none"  id="fencedetailsTab">
    
    <table class="table table-bordered" id="route-table" style="width:100%">
        <thead>
                        <tr>
                            <th>#</th>
                            <th>Route Name</th>
                            <th>Description</th>
                            <th>Active</th>
                            <th></th>
                            
                        </tr>
        </thead>
       
        </table>   
    
</div>

@stop

@section('js')
<style>
    ul li {
        list-style: none;
    }
    hr {
        border: none;
        height: 10px;
        /* Set the hr color */
        color: #333;
        /* old IE */
        background-color: #333;
        /* Modern Browsers */
    }
</style>

<script>
    var bindevents = function (event, customer_id) {
        var customer_id = $("#customer_id").val();

    }
    $(document).ready(function () {
        var latitiude = 43.93667009577818;
        var longitude = -79.65423151957401;
        var radius = 1000;

        var jqdata = [];
       
    });
    
    

    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#route-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o'
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('guardroutesdata') }}",
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
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                },
                {
                    data: 'routename',
                    name: 'routename'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {data: 'status', name: 'status'},
               
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }

        /* Posting data to PositionLookupController - Start*/
     


        $("#route-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("routes.details",":id") }}';
            var url = url.replace(':id', id);
            
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $("#myModal").modal("show");
                    } else {
                        alert(data);
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
    $("#fencebutton").on("click",function(e){
        $("#booleanedit").val("0");
        var editflag = $("#booleanedit").val();
        var routename = $("#routename").val();
        var routedesc = $("#routedesc").val();
        var status = true;
        if(routename==""){
            swal("Warning", "Route name cannot be null", "warning");
                return false;
        }
        else{
            swal({
                                title: "Are you sure?",
                                text: "You will be disabling an active fence",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: '#DD6B55',
                                confirmButtonText: 'Yes, I am sure',
                                cancelButtonText: "No, cancel it",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $.ajax({
                                        type: "post",
                                        url: "{{route('postguardroutes')}}",
                                        data: {
                                            "editflag":editflag,"routename":routename,"routedesc":routedesc,"status":status
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        success: function(response) {
                                            swal("Updated", "Successfully updated", "success");
                                            $("#myModal").modal("hide");
                                            
                                            table.ajax.reload();

                                        }
                                    });


                                } else {
                                    swal("Cancelled", "Not saved", "error");
                                    e.preventDefault();
                                }
                            });
           
        }
        
    });

    $("#fencebuttoncancel").on("click", function (e) {
        $("#myModal").modal("hide");
    })
    
    $("#fencebuttonaddnew").on("click", function (ev) {
        ev.preventDefault();
        $("#title").val("");
        $("#addressfence").val("");
        $("#contractual_visit").val("");
        $("#latfence").val("");
        $("#longfence").val("");
        $("#radiusfence").val(1000);
        $("#visitsfence").val("");
        $("#booleanedit").val("0");
        $("#addnewfence").val("-1");
        $("#whichfence").val("0");
        $("#savemap").css("display", "none");
        

            $("#myModal").modal("show");
        
    });

    $("#savemap").on("click", function (eve) {
        $("#fencebutton").trigger("click");
    })

    $('#myModal').on('pagehide', function (event) {
    });

</script>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
</style>
<script>
    

    $(".close").on('click', function (event) {
        $("#title").val("");
        $("#addressfence").val("");
        $("#contractual_visit").val("");
        $("#latfence").val("");
        $("#longfence").val("");
        $("#radiusfence").val(1000);
        $("#visitsfence").val("");
        $("#booleanedit").val("0");
        $("#addnewfence").val("-1");
        $("#whichfence").val("0");
    });

    

    

    $(document).keyup(function (e) {
        jQuery
        if (e.key === "Escape") {
            $(".close").trigger("click");
        }
    });

  
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="{{ asset('js/timepicki.js') }}"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel='stylesheet' type='text/css' href="{{ asset('css/timepicki.css') }}" />
@stop