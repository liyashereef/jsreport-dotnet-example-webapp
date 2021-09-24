<div id="footer">
    <footer class="text-center margin-top-1">
        <a class="btn submit pull-left" herf="#" title="Help" onclick="showHelpAlert();">Help</a>
        <span>&COPY; Copyright {{ date('Y') }}
            CGL 360.</span>
    </footer>
</div>
<script>
    function showHelpAlert() {
        swal({
            html:true,
            title: "Please contact",
            text: "For assistance, please contact CGL360 Admin at:<br>  E: <a href='mailto:support@cgl360.ca'><u>support@cgl360.ca</u></a><br>P: 1 866 364 4496 / 416 364 4496 x 607 ",
            showCancelButton: false,
            confirmButtonText: "OK",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        });
    }

   </script>
