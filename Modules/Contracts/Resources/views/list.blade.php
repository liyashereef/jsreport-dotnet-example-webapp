@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Capacity Tool</h4>
</div>
<table class="table table-bordered" id="capacity-tool-table-id">
    <thead>
        <tr>
           <th class="sorting">#</th>
           <th class="sorting">Employee Name</th>
           <th class="sorting">Project Type</th>
           <th class="sorting">What Task Did you perform</th>
           <th class="sorting" style="width: 100px;">Start Date</th>
           <th class="sorting">Task Duration</th>
           <th class="sorting">Outcomes achieved</th>
           <th class="sorting">Strategic goal</th>
            <th class="sorting">Performance</th>
           <th class="sorting">Submitted</th>
           <th class="">Action</th>
       </tr>
   </thead>
</table>

@stop
@section('scripts')
<script>
    $(function () {
        var table = '';
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#capacity-tool-table-id').DataTable({
                fixedHeader: true,
                processing: false,
                responsive: true,
                serverSide: true,
                pageLength: 50,
                buttons: [{
                    extend: 'pdfHtml5',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-file-pdf-o',
                    @canany(['view_all_capacity_tool'])
                    exportOptions: {
                        columns: ['th:not(:last-child)']
                    }
                    @endcan
                },
                {
                    extend: 'excelHtml5',
                    //text: ' ',
                    //className: 'btn btn-primary fa fa-file-excel-o',
                    @canany(['view_all_capacity_tool'])
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                    @endcan
                },
                {
                    extend: 'print',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-print',
                    @canany(['view_all_capacity_tool'])
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                    @endcan
                }
            ],
                dom: 'Blfrtip',
                    ajax: {
                        "url":"{{ route('capacitytool.list') }}",

                        "error": function (xhr, textStatus, thrownError) {
                            if(xhr.status === 401){
                                window.location = "{{ route('login') }}";
                            }
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    columns: [

                    {
                        data:'DT_RowIndex',
                        name:'DT_RowIndex',
                    },
                    {
                        data:'answer_1',
                        name:'answer_1',
                    },

                    {
                        data:'answer_3',
                        name:'answer_3',
                    },
                    {
                        data:'answer_2',
                        name:'answer_2',
                    },
                    {
                        data:'answer_4',
                        name:'answer_4',
                    },
                    {
                        data:'answer_5',
                        name:'answer_5',
                    },
                    {
                        data:'answer_6',
                        name:'answer_6',
                    },
                    {
                        data:'answer_7',
                        name:'answer_7',
                    },
                    {
                        data:'answer_8',
                        name:'answer_8',
                    },


                    {
                        data:'created_at',
                        name:'created_at',
                    },
                    {
                    data: null,
                    name: 'action',
                    orderable:false,
                    render: function (o) {

                         var url = '{{ route("capacitytool.show",[":employee_id"]) }}';
                         var edit_url =  '{{ route("capacitytool.edit",[":employee_id"]) }}';
                         edit_url = edit_url.replace(':employee_id',o.capacity_tool_entry_id);
                         url = url.replace(':employee_id', o.capacity_tool_entry_id);
                         actions = '<a title="" id="view-summary" href="' +
                                url + '" ><i class="fa fa-calendar"></i> View</a>';
                        //actions += '<a title="" id="edit-summary" href="' +
                        //        edit_url + '" ><i class="fa fa-calendar"></i> Edit</a>';


                        return actions;
                    }
                }

                    ]
                });
        } catch(e){
            console.log(e.stack);
        }


    });


</script>
@stop
