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
            function triggerDataTableSearch()
            {
                $('.js-customer-filter').each(function(index,item){
                    $('#'+$(item).attr('id')).DataTable().ajax.reload();
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
                    url: '/sync-dashboard-filter',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "customer_ids": values
                    },
                    success:function(response){
                        triggerDataTableSearch();
                        reloadIframe(); // For Dashboard iframe reload
                    },
                    error:function(error){
    
                    }
                });
            }
    
            //Sync the filter data in server.
//            $('#dashboard-filter-customer').on('change', function() { 
//               var data =  $('#dashboard-filter-customer').val(); 
//               syncDashboardFilter(data);
//            });
    
           
            //Reest the dashboard filter on click.
            $('.dashboard-filter-customer-reset').on('click',function(e){ 
                e.preventDefault();
                //if the select2 box is available clear it.
                if( $('#dashboard-filter-customer').length > 0){
                    $('#dashboard-filter-customer').val(null).trigger('change');
                }else{ 
                    syncDashboardFilter([]);
                    window.location.reload();  
                }
            });
    
            // iframe reload on Dashboard
            function reloadIframe() {
                document.getElementById('iframe').src += '';
            }
    
    
    /**End** Dashboard Customer Filter  */ 
    
        });