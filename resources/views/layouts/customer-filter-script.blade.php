<script>

$(function() {

/**Start** Dashboard Customer Filter  */
        //initialize select-2
        $('#dashboard-filter-customer').select2({
            placeholder: "Enter Customer Name or Number.",
        });

        $('.search-btn').on('click', function() {
            $("#customer-filter-container").addClass("search-handler");
        });


        $('.clear-btn').on('click', function() {
            window.location.reload();
        });


        //For Initial load clear session
        // function triggerDataTableSearch()
        // {
        //     $('.js-customer-filter').each(function(index,item){
        //         $('#'+$(item).attr('id')).DataTable().ajax.reload();
        //     });
        // }
        function triggerDataTableSearch()
        {
            $('.js-customer-filter').each(function(index,item){

                let itemId = '#'+$(item).attr('id');
                if($.fn.DataTable.isDataTable(itemId)) {
                    $(itemId).DataTable().ajax.reload();
                }

            });
        }
        function syncDashboardFilter(values)
        {
            //not using default arguments. ##babel is unavailable.
            if(!values){
                values = [];
            }
            //async request.
            $.ajax({

                url: '{{ route('sync-dashboard-filter') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    customer_ids: values
                },
                success:function(response){
                    loadShiftModules(values);// to load shift modules
                    triggerDataTableSearch();
                    reloadIframe(); // For Dashboard iframe reload

                },
                error:function(error){
                   console.log(error)
                }
            });
        }

        //Sync the filter data in server.
//        $('#dashboard-filter-customer').on('change', function() {
//           var data =  $('#dashboard-filter-customer').val();
//
//           syncDashboardFilter(data);
//        });


        //Reest the dashboard filter on click.
        $('.dashboard-filter-customer-reset').on('click',function(e){
            e.preventDefault();
            //if the select2 box is available clear it.
            if( $('#dashboard-filter-customer').length > 0){
                $('#dashboard-filter-customer').val(null).trigger('change');
            }else{
                syncDashboardFilter([]);
                // window.location.reload();
                setTimeout(function(){
                 window.location.reload(1);
                }, 1000);
            }
        });

        // iframe reload on Dashboard
        function reloadIframe() {
            var iframe = document.getElementById('iframe');
            if(iframe){
                document.getElementById('iframe').src += '';
            }
        }


/**End** Dashboard Customer Filter  */

    });

   function loadShiftModules(customer_ids){
    $('#customer-shift-modules').hide();
    var selected_customer = 0;
    if((customer_ids.length ==1)){
     selected_customer = customer_ids[0];
    }else if(window.localStorage && (localStorage.getItem('selected_customer') != 0)){
     selected_customer = localStorage.getItem('selected_customer');
    }

    if(selected_customer != 0){
     $.ajax({
        url: '{{ route('dashboard-shift-module') }}',
        type: 'GET',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
         cid: selected_customer
        },
       success:function(response){
         $('#customer-shift-modules').html(response.html);
         $(response.html).find('table').each( function( index, element ){
         var mod_id = $(element).find('thead').attr('id');
         loadEachTable(mod_id,selected_customer);
        });
        $('#customer-shift-modules').show();
         var c = 12 / Number($('#customer-shift-modules').find('.card-table').length);
         $('#customer-shift-modules').find('.card-padding').addClass('col-lg-' + c).addClass('col-lg-' + c);
        }
      });

     }
   }


   function loadEachTable(mod_id,custom_id){
        var base_url = "{{ route('shift.module',[':module_id',':customer_id']) }}";
        var base_url1 = base_url.replace(':module_id', mod_id);
        var url = base_url1.replace(':customer_id', custom_id);
        var name = $('#emp_id').val();
        var from_date = $('#fr_date').val();
        var to_date = $('#t_date').val();
         $.ajax({
            url: url,
            type: 'GET',
            data :{'name':name , 'from_date':from_date,'to_date':to_date},
            success: function(response) {},
            complete: function(complete_response) {
                var answers = [];
                var ans = [];
                var cols = [];
                var answer = [];
                answers = complete_response.responseJSON.data;

                if(answers[0].Date !== null) {
                 module_order = complete_response.responseJSON.module_order;
                }else{
                 module_order = 0;
                }

                var exampleRecord = answers[0];
                //get keys in object. This will only work if your statement remains true that all objects have identical keys
                var keys = Object.keys(exampleRecord);
                //for each key, add a column definition
                keys.forEach(function(k) {
                    cols.push({
                        title: k,
                        //optionally do some type detection here for render function
                    });

                });
              //  console.log(data);

                $.each(answers, function(key, value) {
                    inner_array = [];
                    $.each(value, function(inner_key, inner_value) {
                        inner_array.push(inner_value);

                    });
                    answer.push(inner_array);
                });

                $.fn.dataTable.ext.errMode = 'hide';

                if ($.fn.DataTable.isDataTable('#modu-'+mod_id)) {
                    $('#modu-'+mod_id).DataTable().destroy();
                    $('#modu-'+mod_id).empty();
                };
                //initialize DataTables
                var table = $('#modu-'+mod_id).DataTable({
                    destroy: true,
                     bAutoWidth: false,
                    columns: cols,

                });
                //add data and draw
                table.clear().draw();
                if(answers.length > 1) {
                    table.rows.add(answer).draw();
                }
                $('.notesspan').closest('table').find('th').eq($('.notesspan').parent().index()).css('width','20%');


            $('#modu-'+mod_id).DataTable({
                    destroy: true,
                    order: module_order,
                    dom: "l<'input-group' f <'input-group-append'>>rtip",
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            },
            columnDefs: [
                    {
                    targets: [ 0 ],
                    visible: false,
                    searchable: false
                    }],
                });

                if (inner_array.every(element => element === null)) {
                    table.clear().draw();

                }
            }
        });
   }

    </script>
