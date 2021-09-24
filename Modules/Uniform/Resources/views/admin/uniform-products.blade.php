@extends('adminlte::page')
@section('title', 'Uniform Items')
@section('content_header')
<h3>Uniform Products</h3>
@stop
@section('content')
<style>
.add-new-item {
    float: right;
    width: 175px;
    background-color: #f26222;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 10px;
    text-align: center;
    border-radius: 5px;
    padding: 5px 0px;
    margin-left: 5px;
    cursor: pointer;
}

.add-new-item:hover {
    background-color: #003A63;
}

</style>

<div id="message"></div>
<div class="add-new-item" onclick="addnew()">Add
    <span class="add-new-label">New</span>
</div>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<table class="table table-bordered" id="uniform-item-table">
    <thead>
        <tr>
            <th></th>
            <th>#</th>
            <th>Name</th>
            <th>Seller Price</th>
            <th>Vendor Price</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
@stop
@section('js')
<script>
    $(function() {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#uniform-item-table').DataTable({
            bProcessing: false,
            responsive: false,
            dom: 'lfrtBip',
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
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('uniform-products.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "DESC"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'id', name: '',visible:false},
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'name', name: 'name'},
                {data: 'selling_price', name: 'selling_price'},
                {data: 'vendor_price', name: 'vendor_price'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        for (var i = 0; i < o.images.length; i++) {
                            if(o.images[i].image_path != null && o.images[i].image_path != 0){
                                actions += '&nbsp;&nbsp;&nbsp;<a href="#" class="download" data-id="' + o.images[i].image_path + '" data-name="' + o.name + '" data-img-id="' + o.images[i].id + '"><i class="fa fa-file-image-o fa-6"></i></a>'
                            }
                        }
                        return actions;
                    },
                },
                {data: null,
                    orderable: false,
                    sortable: false,
                    render: function (o) {
                          var actions = '';
                        @can('edit_masters')
                        actions += '<a href="{{route("uniform-products.update", ["id" => ""])}}/'+ o.id +'" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

            /* videopost  download - Start*/
            $("#uniform-item-table").on("click", ".download", function (e) {
            $('#myModal').find('.modal-body').html('');
            filepath = $(this).data('id');
            fileName = $(this).data('name');
            imgId = $(this).data('imgId');
            $.ajax({
                url: "{{route('uniform-products.filedownload')}}",
                type: 'GET',
                data: "filepath=" + filepath,
                success: function (data) {
                    if (data) {
                        var html = "";
                        html+='<div style="border: 1px solid black;">'
                        html+= '<img src="'+data+'" style="display: block;margin: 0 auto;" height="600" width="400"  alt="Product image">';
                        html+='</div">'
                        $("#myModal .modal-body").html(html);
                        // $('#myModal .modal-title').text("Uniform Product : " +fileName);
                        $('#myModal .modal-footer').html("<a href='#' class='removeattachment btn btn-danger' data-file-id='" + imgId + "' data-path='" + filepath + "'>Delete</a>");
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Uniform product: " +fileName);
                    } else {
                        console.log(data);
                        swal("Oops", "Download was unsuccessful", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });

        /* Uniform image download - End*/



         /* Deleting Measurement Point Name - Start */
         $('#uniform-item-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('uniform-products.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Uniform product has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Deleting Measurement Point Name - End */

        $(document).on("click",".removeattachment",function(e){
        var self=this;
        var id = $(self).attr("data-file-id");
        var base_url = "{{ route('uniform-products.destroy-attachment',':id') }}";
        var url = base_url.replace(':id', id);
        swal({
            title: "Are you sure?",
            text: "You will not be able to undo this process",
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
            swal.close();
            $('#myModal').modal('hide');
            $.ajax({
            type: "get",
            url: url,
            success: function (response) {
                if(response.success==true){
                    location.reload();
                }

            }
        });
            } else {
            swal.close()
            }
        });
    })



    });

    function addnew() {
        window.location.href = "{{route('uniform-products.add')}}";
    }
</script>
@stop
