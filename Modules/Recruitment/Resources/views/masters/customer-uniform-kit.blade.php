{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')
@section('title', 'Uniform Kits')
@section('content_header')
<h1>Uniform Kits</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new"  onclick="addnew()" data-title="Add New Dropdown">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="dropdown-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Kit Name</th>
            <th>Customer Name</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>


@stop
@section('js')
<script>

    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#dropdown-table').DataTable({
                dom: 'lfrtBip',
                bProcessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1],
                    },
                    customize: function (xlsx) {
                      var sheet = xlsx.xl.worksheets['sheet1.xml'];
                      var col = $('col', sheet);
                      $(col[1]).attr('width', 40);
                  }

              },
              {
                extend: 'print',
                text: ' ',
                className: 'btn btn-primary fa fa-print',
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                text: ' ',
                className: 'btn btn-primary fa fa-envelope-o',
                action: function (e, dt, node, conf) {
                    emailContent(table, 'Work Types');
                }
            }
            ],
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax: {
                "url":'{{ route('recruitment.customer-uniform-kits.list') }}',
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
            {data: 'DT_RowIndex', name: '',sortable:false},
            {data: 'kit_name', name: 'kit_name'},
            {data: 'project_details', name: 'project_details'},
            {data: null,
                orderable:false,
                render: function (o) {
                    var actions = '';
                    @can('edit_masters')
                        actions = '<a href="{{route("recruitment.customer-uniform-kits.update", ["id" => ""])}}/'+ o.id +'" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
                          @endcan
                            @can('lookup-remove-entries')
                                actions +='<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                                 @endcan
                        return actions;
                },
            }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }


        /* WorkType Delete  - Start */
        $('#dropdown-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url ="{{ route('recruitment.customer-uniform-kits.destroy',':id') }}";
            var url = base_url.replace(':id',id);
            var message = 'Uniform kit name has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* WorkType Delete  - End */

    });

 function addnew() {
        window.location.href = "{{route('recruitment.customer-uniform-kits.add')}}";
    }

</script>
@stop
