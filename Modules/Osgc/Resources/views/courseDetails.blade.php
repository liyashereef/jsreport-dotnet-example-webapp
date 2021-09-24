@extends('layouts.cgl360_osgc_scheduling_layout')

@section('css')

<style>
  .main{
    
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    
  }
 .tabs {
    margin: 0 auto;
    padding: 0 20px;
   
  }
  #tab-button {
    display: table;
    table-layout: fixed;
    width: 100%;
    margin: 0;
    padding: 0;
    list-style: none;
  }
  #tab-button li {
    display: table-cell;
    width: 20%;
  }
 
  #tab-button li a {
    display: block;
    padding: .5em;
    /*background: #1a182b;*/
    border: 1px solid #ddd;
    text-align: center;
    color: #fff;
    text-decoration: none;
  }
  #tab-button li:not(:first-child) a {
    border-left: none;
  }
  #tab-button li a:hover,
  #tab-button .is-active a {
    border-bottom-color: transparent;
    background: #3c393a;
  }
  .tab-contents {
    /* padding: .5em 2em 1em; */
    border: 1px solid #ddd;
    
  }
  .first-tab {
    background: #379a2b;
  }
  .second-tab {
    background: #f36424;
  }
  .other-tab {
    background: #1a182b;
  }
  
  .vertical-menu {
      display: block;
      /* position: relative; */
  }
  .vertical-menu a {
   
    color: #fff !important;
    display: block;
    padding: 2px;
    background-color:#379a2b;
    font-size: small;
    text-align: justify;
    padding-left: 15px;
    border :1px solid #318427;
  }
  .err-validation
  {
    color: #c00;
  }
  .err {
    border: 1px solid #c00;
}
  .vertical-menu a:hover {
    background-color: #ccc;
  }
  
  .vertical-menu a.active {
    background-color: #379a2b;
    color: white;
  }
  
  .tab-button-outer {
    display: none;
  }
  .tab-contents {
    margin-top: 20px;
  }
.part1 {background-color:#f36424;
     
    
     
   
  padding-top: 50px;
  padding-bottom: 45px;
    }




  @media screen and (min-width: 768px) {
    .tab-button-outer {
      position: relative;
      z-index: 2;
      display: block;
    }
    .tab-select-outer {
      display: none;
    }
    .tab-contents {
      position: relative;
      top: -1px;
      margin-top: 0;
    }
    
  }
</style>
@stop
@section('content')
<section class="container main">

    <div class="row justify-content-center main">
       
        @if(!empty($result->CourseHeaders))
        <!-- start -->
        <div class="tabs">
              <div class="tab-button-outer">
                <ul id="tab-button">
                    @foreach($result->CourseHeaders as $key=> $header)
                      <li><a href="#tab{{$key}}" class="first-tab">{{$header->name}}</a></li>
                    @endforeach
                </ul>
              </div>
      
              <div class="tab-select-outer">
                <select id="tab-select">
                    @foreach($result->CourseHeaders as $key=> $header)
                      <option value="#tab{{$key}}">{{$header->name}}</option>
                    @endforeach
                </select>
              </div>
              @foreach($result->CourseHeaders as $key1=> $header)
              <div id="tab{{$key1}}" class="tab-contents">
              <div class="input-group">
                    <div class="part1 col-md-2 col-sm-3">
                        <div>
                            <div class="vertical-menu">
                            @foreach($result->CourseSections as $section)
                                @if($header->id == $section->header_id)
                                  <a>{{$section->name}}</a>
                                @endif
                            @endforeach
                            </div>
                          </div>
                        </div>
                    <div class="part2 col-md-10 col-sm-9">

                    <p>Some text to enable scrolling.. Lorem ipsum dolor sit amet, illum definitiones no quo, maluisset concludaturque et eum, altera fabulas ut quo. Atqui causae gloriatur ius te, id agam omnis evertitur eum. Affert laboramus repudiandae nec et. Inciderint efficiantur his ad. Eum no molestiae voluptatibus.</p>
                    <p>Some text to enable scrolling.. Lorem ipsum dolor sit amet, illum definitiones no quo, maluisset concludaturque et eum, altera fabulas ut quo. Atqui causae gloriatur ius te, id agam omnis evertitur eum. Affert laboramus repudiandae nec et. Inciderint efficiantur his ad. Eum no molestiae voluptatibus.</p>
                    <p>Some text to enable scrolling.. Lorem ipsum dolor sit amet, illum definitiones no quo, maluisset concludaturque et eum, altera fabulas ut quo. Atqui causae gloriatur ius te, id agam omnis evertitur eum. Affert laboramus repudiandae nec et. Inciderint efficiantur his ad. Eum no molestiae voluptatibus.</p>
                    <p>Some text to enable scrolling.. Lorem ipsum dolor sit amet, illum definitiones no quo, maluisset concludaturque et eum, altera fabulas ut quo. Atqui causae gloriatur ius te, id agam omnis evertitur eum. Affert laboramus repudiandae nec et. Inciderint efficiantur his ad. Eum no molestiae voluptatibus.</p>
                                     
                    </div>
              </div>
              </div>
              @endforeach
        </div>
        @endif
                <!-- End -->
            
</div>
</section>
          
  



@stop
@section('scripts')
<script>
    $(function() {
  var $tabButtonItem = $('#tab-button li'),
      $tabSelect = $('#tab-select'),
      $tabContents = $('.tab-contents'),
      activeClass = 'is-active';

  $tabButtonItem.first().addClass(activeClass);
  $tabContents.not(':first').hide();

  $tabButtonItem.find('a').on('click', function(e) {
    var target = $(this).attr('href');

    $tabButtonItem.removeClass(activeClass);
    $(this).parent().addClass(activeClass);
    $tabSelect.val(target);
    $tabContents.hide();
    $(target).show();
    e.preventDefault();
  });

  $tabSelect.on('change', function() {
    var target = $(this).val(),
        targetSelectNum = $(this).prop('selectedIndex');

    $tabButtonItem.removeClass(activeClass);
    $tabButtonItem.eq(targetSelectNum).addClass(activeClass);
    $tabContents.hide();
    $(target).show();
  });
});
</script>
@stop



