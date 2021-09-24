@extends('layouts.cgl360_osgc_scheduling_layout')

@section('css')

<style>
   html, body {
  height: 100%;
  margin: 0;
  font-family: 'Montserrat' !important;
    
}

.wide-block {
    width: calc(100% + 16px);
    margin-left: -8px;
    margin-top: -20px;
    background-color: #19202b!important;
    border-radius: 0;
    padding: 15px 5px;
    height: 252px;
}
.color-light {
    color: #d6d6d6!important;
    font-size: 16px;
}
.content-area{
  color: white;
  text-align: justify;
  padding: 30px 15px;
}
.banner-intro {
   
    height: 200px;
    padding: 5px;
}
.price-area{
  color:white;
  font-size:20px;
  font-weight:500
}
.viewCourse
{
  margin-top: 30px;
}
.btn{
   
    border-radius: 5px;
    box-shadow: none;
    font-weight: 500;
    cursor: pointer;
    border: 0;
    font-size: 17px;
    background: #ea660f;
    margin-bottom: 5px;
    color: #ffffff;  

}
.price-div {
    margin-top: 64px;
}
.cart {
    background: #ea660f;
    margin-bottom: 5px;
    color: #ffffff;  
}
.course-title
{
  font-size: 20px;
  margin-bottom: 14px;
  font-weight: bold;
}

.swal-text{
  max-height: 15em;  /* To be adjusted as you like */
  overflow-y: scroll;
  text-align: justify;
  line-height: 1.6;
  color: black !important;
  }
.swal-modal
{
  font-family: 'Montserrat' !important;
  width: 50%;
}
.swal-title {
  font-size: 23px;
}
.swal-button{
  font-size: 15px;
}
.imgDiv
{
  width: 88%;
  height: 202px;
    border: solid 1px rgb(255, 255, 255);
    align-items: center;
    position: absolute;
    
    vertical-align: middle;
    text-align: center;
    
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
@stop
@section('content')

    <!-- <div class="row justify-content-center">
      {{"No Course Found"}}
    </div>   -->
<section class="container">

    <!-- <div class="row">
        <div class="col-md-12 dleft-column">
        <div class="row mt-4" id="course_list" style="margin-left:0px !important;">
          @for($i=0;$i<=2;$i++)
         
                <div class="d-flex col-md-3" >
                <div class="container_fluid" style="padding-left:30px !important;padding-right:30px !important;border:solid 0.5px #d3d3d3">
                    <div class="row">
                        <div class="col-md-12 mt-2">
                        <h3 class=" " style="font-weight:bold;font-size:18px ;line-height: 1.6;white-space:normal">Security Guard Course</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                          <img style="height:231px !important" src="{{ asset('images/courses_noimage.png') }}" alt="" class="card-intro w-100">
                        </div>
                    </div>
                
                    <div class="row mt-2 mb-3">
                      <a class=" mt-2 ml-3 p-2  rounded shadow  readmore" style="border:solid 0.5px #d3d3d3;background:white;color:#F1502B;font-weight:bold" title="" href="">Buy Now
                      <i class="fa fa-angle-double-right ml-2" aria-hidden="true"></i>
                      </a>
                    </div>
              </div>
            </div>
        
  @endfor
  </div>
          </div>
        </div> -->
<div class="row">    
{!!$finalStr!!}
 </div>   



  
</section>         
   



@stop
@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">

  
      var key="{{config('globals.stripe_key')}}";
      var stripe = Stripe(key);
      var checkoutButton = document.getElementById('checkout-button');

      checkoutButton.addEventListener('click', function() {
        $('body').loading({
            stoppable: false,
            message: 'Please wait...'
        });
        var courseId=$(this).data('id');
        const data = { course_id: courseId };
        fetch("{{route('osgc.payCourse')}}", {
        method: 'POST',
          headers: {
            'X-CSRF-TOKEN':'{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data),
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(session) {
          return stripe.redirectToCheckout({ sessionId: session.id });
        })
        
        .then(function(result) {
          // If `redirectToCheckout` fails due to a browser or network
          // error, you should display the localized error message to your
          // customer using `error.message`.
          if (result.error) {
            alert(result.error.message);
          }
        })
        .catch(function(error) {
          console.log('Error:', error);
          //alert('Please try again');

        });

        
      });
   
      
      function readMore(id)
      {
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
                url: '{{ route("osgc.showCourse") }}',
                type: 'POST',
                data: "course_id=" + id,
                success: function (data) {
                  if(data)
                  {
                    swal(data.title, data.description)
                 
                 
                  }
                  
                    
                }
            });
        
      }
    </script>
@stop



