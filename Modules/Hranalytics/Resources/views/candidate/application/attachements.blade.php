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
        @foreach($candidateJob->candidate->attachements as $eachAttachment)
        <li class="col-form-label"><a target="_blank" href="{{ asset('attachments/'.$eachAttachment->attachment_file_name) }}"><b>{!!is_object($eachAttachment->attachment) ? $eachAttachment->attachment->attachment_name : $eachAttachment->attachment_file_name !!} </b></a></li>
        @endforeach
    </ul>
</div>
<div class="candidate-pic-div" style="float: right;width: 50% !important;justify-content: center;height:100% !important;">
    <img src="{{asset('images/uploads/') }}/{{ $candidateJob->candidate->profile_image ?? config('globals.noAvatarImg') }}" height="250px" width="250px" style="border-radius: 50%;display:inline-block; vertical-align:middle;"/>
</div>
