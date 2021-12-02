@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
<style>
    #table-id .fa {
        margin-left: 11px;
    }
</style>
@stop
@section('content')
<div class="table_title">
    <h4>Chat History </h4>
</div>
{{-- <div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$project_list,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div> --}}
<br>

<table class="table table-bordered" id="chat-table">
     <thead>
         <tr>
             <th width="5%"></th>
             <th class="sorting" width="10%">Employee Name</th>
             <th class="sorting" width="15%">Date</th>
             <th class="sorting" width="15%">Time</th>

         </tr>
     </tbody>
     </thead>
 </table> 


@stop
@section('scripts')
<script>
   
    
   const pm = {
        ref: {
            chatTable: null,
            chatExpandTable: {},
            expandedPanels: [],
        },
        init() {
            //Initialize project managemet table
            this.initChatTable();
            //Event listeners
            this.registerEventListeners();
        },
        registerEventListeners() {
            //Global scope inside closure.
            let root = this;
           // Add event listener for opening and closing details project
            $('#chat-table').on('click', '.details-control', function(e, arg) {
                let tr = $(this).closest('tr');
                let row = root.ref.chatTable.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    root.toggleDetails(this, false);
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    root.toggleDetails(this, true);
                    row.child(root.renderExpansionTable(row.data())).show();
                    root.afterRenderExpansionTable(row.data());
                    //init it as data table
                    tr.addClass('shown');
                    $($(tr).next('tr').find('td')[0]).addClass('zero-padding');
                }
                if (arg === undefined) {

                    root.handleExpantionPanels($(this).data("panel-id"));
                }
                // $('.pmt-lv1').find('thead').css('display','none');
            });
        },
        initChatTable(){

         this.ref.chatTable  = $('#chat-table').DataTable({
            bProcessing: false,
            responsive: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('chat.view-history.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                        data: null,
                        render: function(o) {

                            return '<button  class="btn details-control fa fa-plus-square pro-' + o.from_id + '" data-panel-id="pro-' + o.from_id + '"></button>';
                        },
                        orderable: false,
                    },
                {
                    data: 'from',
                    name: 'from'
                },
                {data: 'date', name: 'date'},
                {data: 'time', name: 'time'}

            ]
        });
       
   
    },
            handleExpantionPanels(panelId) {
            if (this.ref.expandedPanels.includes(panelId)) {
                this.ref.expandedPanels = this.ref.expandedPanels.filter(f => f !== panelId)
            } else {

                this.ref.expandedPanels.push(panelId);
            }
        },
        toggleDetails(el, expand) {
            if (expand) {
                $(el).removeClass('fa-plus-square').addClass('fa-minus-square');
            } else {
           
                $(el).removeClass('fa-minus-square').addClass('fa-plus-square');
            }
        },
         reloadTable(table) {

            table.ajax.reload(null, false);
        },
        drawCallback(settings) {
            if (settings.sTableId === 'pm-project-table') {
                pm.triggerExpand('pro');
            }
            if (settings.sTableId.startsWith('pm-group-table')) {
                pm.triggerExpand('gro');
            }
        },
        triggerExpand(prefix) {
            this.ref.expandedPanels.forEach(function(el) {
                if (el.startsWith(prefix)) {
                    let node = $('body').find('.' + el);
                    if (node.length > 0) {
                        node.trigger('click', ['re-render']);
                    }
                }
            });
        },

    renderExpansionTable(d) {
            return `
                <table class="pm-sub-table pmt-lv1 table table-bordered"  id="pm-chat-expansion-table-${d.id}">
                    <thead>
                        <tr>
                           <th></th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                </table>
                `;
        },
         afterRenderExpansionTable(d) {
            let url = '{{ route("chat.show-message",":id") }}';
            url = url.replace(':id', d.from_id);
            this.ref.chatExpandTable[d.id] = $('#pm-chat-expansion-table-' + d.id).DataTable({
                "drawCallback": this.drawCallback,
                paging: false,
                bFilter: false,
                bInfo: false,
                "ajax": {
                    "url": url,
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                "columnDefs": [{
                        "width": "10%",
                        "targets": 0
                    },
                    {
                        "width": "20%",
                        "targets": 1
                    },
                    {
                        "width": "25%",
                        "targets": 2
                    },
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                    {
                        data: 'text',
                        name: 'text',
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'date',
                        name: 'date',
                        
                    },
                    {
                        data: 'time',
                        name: 'time',
                        
                    },

                   
                ],
            });
        },
}


$(function() {
        pm.init();
    });

     

           
</script>


@stop
