<script>
    $(function(){
 var candidate = {!! $candidate  !!};
        $(document).on("change","#gender",function(e){
            e.preventDefault();
            let genderselection = $(this).val();
            if(genderselection!="female"){
                $(".female").val("");
                $(".female").hide();
                $(".femalecontrol").prop("required",false)
            }else{
                $(".female").show();
                $(".femalecontrol").prop("required",true)

            }
        })
        $(document).ready(function () {
            setTimeout(() => {
                // let address = $('input[name="address"]').val()+","+
                // $('input[name="city"]').val()+","+
                // $('input[name="postal_code"]').val();
                 let address =  candidate['address']+","+
                 candidate['city']+","+
                 candidate['postal_code'];
                if (address != null) {
                    let shipping_address=$('textarea[name="shipping_address"]').val()
                    if (address.trim() === shipping_address.trim()) {
                        $('input[name="same_address_check"]').prop("checked", true);
                        $('textarea[name="shipping_address"]').prop("readonly",true)
                    }
                }
            }, 1000);

            $("#gender").trigger("change")
        });

        $('input[name="same_address_check"]').on("click",function(e){
            // let address = $('input[name="address"]').val()+","+
            //     $('input[name="city"]').val()+","+
            //     $('input[name="postal_code"]').val();
            let address =  candidate['address']+","+
                 candidate['city']+","+
                 candidate['postal_code'];
               

            if($(this).is(":checked")){
                $('textarea[name="shipping_address"]').val(address)
                $('textarea[name="shipping_address"]').prop("readonly",true)
            }else{

                $('textarea[name="shipping_address"]').prop("readonly",false)
            }
        })
    })

</script>
