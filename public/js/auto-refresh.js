/**START** Autoload Reload */

         function triggerDataTableReload()
        {  
            $('.auto-refresh').each(function(index,item){ 
                
                let itemId = '#'+$(item).attr('id');
                if($.fn.DataTable.isDataTable(itemId)) {  
                    // console.log($(itemId).DataTable());
                    //Hiding ajax message. [ Message: PLEASE WAIT... ]
                    if(itemId != '#shift-journal-table'){ // For to avoid in 
                        $(itemId).DataTable().context[0].ajax.global = false;
                    }
                    //DataTable reloading   
                    $(itemId).DataTable().ajax.reload();
                   
                }
                
            });
            
            //For MSP Tracking Dashboard. Reloading map and Datatable.
            if ($("#map").hasClass("msp-tracking-dashboard-auto-refresh")) {
                 $(".filterbutton").trigger( "click" );
                $('body').loading({
                    start: false,
                });
              }
            
            //   //For FM Dashboard.widgets reloading.
            //   if($('#fcm-dashboard-widgets').hasClass("fcm-dashboard-auto-refresh")){
            //     fcmc.filterTriggered();
            //   }
            
        }
    //----START---- Auto Refresh -----------------
            setInterval(function(){  
                triggerDataTableReload(); 
            }, 300000);
    //----END---- Auto Refresh -----------------

/**End** Autoload Reload */