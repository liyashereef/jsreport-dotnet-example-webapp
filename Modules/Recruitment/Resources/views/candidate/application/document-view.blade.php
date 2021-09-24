<style>
    .candidate-pic-div img {
        transition: transform .5s, filter 1.5s ease-in-out;
    }

    /* [3] Finally, transforming the image when container gets hovered */
    .candidate-pic-div:hover img {
        z-index: 9999999;
        transform:scale(1.5);
        -ms-transform:scale(1.5); /* IE 9 */
        -moz-transform:scale(1.5); /* Firefox */
        -webkit-transform:scale(1.5); /* Safari and Chrome */
        -o-transform:scale(1.5); /* Opera */
        /* position: relative; */
    }
</style>


<div style="float: left;width: 50% !important;height:100% !important;">
    
    <ul class="list-unstyled" id="attachments">
        @foreach($document as $each_document)
        @if($each_document['submitted_file']!="")
        <li class="col-form-label"><a target="_blank" href="{{Storage::disk('s3-recruitment')->temporaryUrl($each_document['submitted_file'],Carbon::now()->addMinutes(60)) }}"><b>{{  $each_document['document_name'] }}</b></a></li>
        @endif
        @endforeach
    </ul>
</div>

