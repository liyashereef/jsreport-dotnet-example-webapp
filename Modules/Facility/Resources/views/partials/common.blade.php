<script type="text/javascript">
$(document).on("keypress",".notdecimal",function(evt){
    var $txtBox = $(this);
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if(charCode==46){
        return false;
    }
})
$(document).on("keypress",".number",function(evt){
    var $txtBox = $(this);
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;
        else {
            var len = $txtBox.val().length;
            var index = $txtBox.val().indexOf('.');
            if (index > 0 && charCode == 46) {
              return false;
            }
            if (index > 0) {
                var charAfterdot = (len + 1) - index;
                if (charAfterdot > 3) {
                    return false;
                }
            }
        }
        return $txtBox; //for chaining
})
</script>
