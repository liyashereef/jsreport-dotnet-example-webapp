@extends('layouts.app')
@section('content')
<?php $error_block = '<span class="help-block text-danger align-middle font-12"></span>';?>
<div class="table_title">
    <h4> Operational Dashboard
    </h4>
</div>
<div class="">
    <div class="" role="tabpanel">
        <ul class="nav nav-tabs operational_dashboard" role="tablist">
            @php
                $i =1;
            @endphp
            @foreach ($template_categories as $category)
                <li class="op_dashboard_li" role="presentation" >
                    {{-- <a class={{($i == 1) ? "nav-link" : "nav-link active" }} role="tab" data-toggle="tab" href="#profile"> --}}
                        <a class="nav-link operational_dashboard"  role="tab" data-toggle="tab" href="#{{ $category['id'] }}">
                            <span>{{$i++}}. {{ $category['description'] }}
                        </span>
                    </a>
                </li>
            @endforeach

        </ul>
        <div class="tab-content">
            @foreach ($template_categories as $category)
                <div id="{{ $category['id'] }}" class="tab-pane">

                    <section class="full-width">
                            <table class="table table-bordered operational_dashboard" id="tbsl{{ $category['id'] }}">
                                {{-- <table class="table table-bordered" id="example"> --}}
                                </table>
                    </section>
                </div>
            @endforeach


            <div class="candidate-screen display-inline print-view-btn" style="float:right;">
                {{-- <a title="Print application" href="{{route('candidate-job.print-view',$candidateJob->id)}}">
                    <i class="fa fa-print" aria-hidden="true"></i>
                </a> --}}
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script>
    $(document).ready(function () {
       var table, columns, answer, template_category_id;
        //Default Selecting first tab on page load
        var template_category_id = {!! $template_categories[0]['id'] !!};
        $('#'+template_category_id).addClass('active');
        loadDatatable(template_category_id);
        $('.operational_dashboard  a[href="' + ('#'+template_category_id) + '"]').addClass('active');
        //Datatable loading on clicking each tab
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            template_category_id = target.replace('#','');
            e.preventDefault();
            $('div#filters').remove();
             loadDatatable(template_category_id)
           });
        //Function to load datatable
        function loadDatatable(template_category_id){
            var url = '{{ route("operational-dashboard.parent_answers",[":temp_category_id"]) }}';
            url = url.replace(':temp_category_id',template_category_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response){
                    },
                complete: function(complete_response){
                    answers = complete_response.responseJSON.data.answers;
                    questions = complete_response.responseJSON.data.questions;
                    filter_dropdown = complete_response.responseJSON.data.filter_dropdown;
                    payperiod = complete_response.responseJSON.data.payperiod;
                    columns = []; filters = ''; options = ''; payperiod_options = ''; answer = [];

                    columns.push(
                                    { title: "Client Number" , width : "10%" },
                                    { title: "Client Name" , width : "15%" },

                                    { title: "Area Manager" , width : "10%"},
                                    { title: "Pay Period" ,'visible' :false},
                                      { title: "Customer id" ,'visible' :false},

                                );
                    $('#'+template_category_id).prepend('<div class="operational_dashboard " id="filters"><div class="form-group row"><div class="col-md-4 ">Filter By:</div></div></div>');
                    //Options for dynamic filters
                    options += '<option value="0">Select</option>';
                    $.each(filter_dropdown,function(key,value){
                        options += value;
                    });
                    //Payperiod dropdown
                    payperiod_options += '<option value="0">Select</option>';
                    $.each(payperiod,function(key,value){
                        payperiod_options += value;
                    });
                    filters += '<div class="form-group row"><div class="col-md-4">Pay Period</div>';
                    filters += '<div class="col-md-4"><select data-id="4" class="form-control filter">'+payperiod_options+'</select></div></div>';
                    //Dynamic filter questions
                    if(questions != null){
                        $.each(questions.template_form,function(key,value){
                            columns.push({'title' : value.question_text , 'width' : "15%"});
                            filters += '<div class="form-group row"><div class="col-md-4">'+value.question_text+'</div>';
                            filters += '<div class="col-md-4"><select data-id="'+(key+5)+'" class="form-control filter">'+options+'</select></div></div>';
                        });
                        $('#'+template_category_id).find('div#filters').append(filters);
                    }
                    //Answers for dynamic columns
                    $.each(answers,function(key,value){
                        inner_array = [];
                        $.each(value,function(inner_key,inner_value){
                            var customer_id=value.customer_id
                            var payperiod=value.payperiod
                           if(inner_key=='client_name')
                           {
                            var url = '{{ route("customer.details", [":id",":payperiod_id",":analytics"]) }}';
                            url = url.replace(':id', customer_id);
                            url = url.replace(':payperiod_id', payperiod);
                             url = url.replace(':analytics', 'analytics');
                            inner_value='<a href="'+url+'">'+inner_value+'</a>'
                           }
                            inner_array.push(inner_value);

                        });
                        answer.push(inner_array);
                    });
                    console.log(answer);

                     table = $('#tbsl'+template_category_id).DataTable({
                         destroy: true,
                         processing: false,
                         responsive: true,
                         bFilter: true,
                         pageLength: 50,
                         data : answer,
                         lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                         columns: columns,
                         columnDefs: [
                                        { "width": "20%" }
                                    ]

                    });
                }
            });
       }

    //Dynamic column filtering
    $(document).on('change','select.filter', function () {
        search_value = ( this.value != 0 ) ? this.value : '';
        table.column($(this).attr('data-id')).search(search_value).draw();
    } );
    });
</script>
@stop
<style>
.subTabs{
    margin-left: 0px !important;
    margin-top: -5px !important;
}
</style>