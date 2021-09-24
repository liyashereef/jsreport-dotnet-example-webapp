<!-- Response KPIs -->
<style>
    .titledata{
        width:100px !important;
    }

    .monthdata{
        width:100px !important;
        text-align: center;
        vertical-align: middle !important;
        white-space: nowrap;
    }
    .bluebg{
        background: #003b63;
        color: #fff;
    }

    .orangebg{
        background: #f23c22;
        color:#fff;
    }
    .incgreenbg{
        background:green;
        color: #fff;
    }
    .incyellowbg{
        background:yellow;
        color: #000;
    }
    .incredbg{
        background:red;
        color: #fff;
    }
    .table-nonfluid {
   width: auto !important;
}
</style>
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetResponseKpis',function(payload) {
        let wc; //widget container

        function generateContent() {
            let datatable = ``
            let data = payload.data;
            let months =  Object.values(data["kpi"]["headermonths"]).reverse();
            let rowdata =  Object.values(data["kpi"])
            if(rowdata.length>1)
                {
            datatable+=`<table class="table table-bordered table-nonfluid staticblock tbl-line-height-1">
            <tr class="bluebg staticblock">`;
            datatable+=`<td  class="titledata" style="white-space:nowrap;padding-left:18px !important;padding-right:50px !important">
            Incident Subject&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`
            months.forEach(element => {
                datatable+=`<td class="monthdata" style="">${element.replace("-"," ")}</td>`

            });
            datatable+=`</tr>`;
            datatable+=`<tr>`;

            rowdata.forEach(function(element) {
                let tdrowdata = element
                if(element["name"]!=undefined)
                {
                    let subname = element["name"];
                    let popsubname = element["name"];
                    if(subname.length>25)
                    {
                        subname = subname.substr(0,25)+"...";
                    }
                    datatable+=`<tr><td title="${popsubname}"  data-toggle="popover"
                    style="cursor:pointer;padding-left:18px !important;padding-right:50px !important;white-space:nowrap"
                      class="titledata titlename">${subname}</td>`
                    let monthlydata = Object.values(element["months"]).reverse()

                    monthlydata.forEach(function(elm)  {
                        let actualValue =elm;
                        elm=Math.floor(elm);
                        let bclass = "incgreenbg"
                        if(element["response_time"]>elm){
                            bclass = "incgreenbg"
                        }else if(elm > element["response_time"] && elm <= element["response_time"]*2){
                            bclass = "incyellowbg"
                        }
                        else if(elm > element["response_time"]*2){
                            bclass = "incredbg"
                        }
                        if(elm>0){
                            datatable+=`<td class="monthdata ${bclass}">${elm}</td>`
                        }else if(actualValue>0 ){
                            datatable+=`<td class="monthdata incgreenbg"><1</td>`
                            //datatable+=`<td class="monthdata ">&nbsp;</td>`
                            }else{
                            datatable+=`<td class="monthdata ">&nbsp;</td>`
                        }

                    });
                    datatable+=`<tr>`

                }


            });
            datatable+=`</tr>`;
                }else{
                    datatable+=`<table class="table ">
                    <tr><td style="text-align:center;width:100%">No record found</td></tr>`
                }
            datatable+= `</table>`
            let content = datatable;
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            $(".titlename").tooltip()
        }

        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();

        $(document).on("mouseover",".titlename",function(){
            $(this).tooltip();
        })
    });
</script>
