<div id="submit" class="container-fluid tab-pane fade candidate-screen"><br>
    <div class="row">
        <!--<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 alert alert-success"> Your Screening Form Successfully Submitted </div>-->
        <p>Please make sure to bring this completed application form should you be selected for an interview. The interview cannot proceed without this document.</p>

        <div class="col-sm-8"></div>
        <div class="col-xs-12 col-sm-4">
            <a href="#" onclick="confirmSubmission($(this).prop('href'))" id="print-pdf-application"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Submit & Print PDF</a>
        </div>
    </div>
</div>
<script>
    function confirmSubmission(url) {
        event.preventDefault();
        lastPart = url.substr(url.lastIndexOf('/') + 1);

        if (lastPart !== "#") {
            swal({
                title: "Are you sure?",
                text: "Your application will be submitted to Commissionaires Great Lakes. You cannot edit your application once submitted.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Submit",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
                    function () {
                        window.location = url;
                    });
        }
    }
</script>
