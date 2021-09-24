
@extends('layouts.app')
@section('content')
<style>
    .delete{
        margin-left: 10px;
    }
    .media-contain .img-hold img {
    border: solid 1px rgba(204, 204, 204, 0.22);
    padding: 8px;
    max-width: 100%;
}
img {
    vertical-align: middle;
}
</style>

    <div class="table_title">
        <h4> {{$content->content_title}} </h4>
    </div>

    @if($content->content_type_id == 1)
        <div class="row media-contain">
               <div class="col-md-12 d-flex justify-content-center p-5 img-hold">
                  <img src="https://s3.{{config('filesystems.disks.s3.region')}}.amazonaws.com/{{config('filesystems.disks.s3.bucket')}}/images/{{$content->value}}" alt=""/>
               </div>
           </div>
    @elseif($content->content_type_id == 2)
    <div class="row media-contain">
               <div class="col-md-12 d-flex justify-content-center pt-2 p-0 pb-4 img-hold">

                      <iframe src="https://s3.{{config('filesystems.disks.s3.region')}}.amazonaws.com/{{config('filesystems.disks.s3.bucket')}}/pdf/{{$content->value}}" width="90%" height="670"></iframe>
               </div>
           </div>
            <!-- <div class="course-read d-flex justify-content-center align-items-center">

                    <input class="button pdf-read-btn btn mb-0 ml-2" id="mdl_save_change" type="button" value="I have read and understood the document">

        </div> -->
    @elseif($content->content_type_id == 3)
        <div class="row media-contain">
                <div class="col-md-12 d-flex justify-content-center p-5 img-hold">

                    <video id='my-video' class='video-js' controls preload='auto' width='1150' height='500'
                        data-setup='{}'>

                        <source src="https://s3.{{config('filesystems.disks.s3.region')}}.amazonaws.com/{{config('filesystems.disks.s3.bucket')}}/video/{{$content->value}}" type='video/mp4'>
                        <p class='vjs-no-js'>
                            To view this video please enable JavaScript, and consider upgrading to a web browser thats
                            <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                        </p>
                    </video>
                </div>
            </div>

        @endif





@stop @section('scripts')
<script>

</script>
@stop
