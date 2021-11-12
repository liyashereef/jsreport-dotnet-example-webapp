@extends('adminlte::page')
@section('title', 'Uniform Items')
@section('content_header')
<h3>Uniform Items</h3>
@stop
@section('content')
<div
    class="add-new"
    onclick="addnew()"
    data-title="Add New Customer">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="uniform-item-table">
    <thead>
        <tr>
             <th></th>
            <th>#</th>
            <th>Item Name</th>
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
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.uniform-items.list') }}",
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
            columns: [
                {data: 'id', name: '',visible:false},
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'item_name', name: 'item_name'},
                {data: null,
                    orderable: false,
                    sortable: false,
                    render: function (o) {
                          var actions = '';
                        @can('edit_masters')
                        actions += '<a href="{{route("recruitment.uniform-items.update", ["id" => ""])}}/'+ o.id +'" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
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
    });

    function addnew() {
        window.location.href = "{{route('recruitment.uniform-items.add')}}";
    }
</script>
@stop
