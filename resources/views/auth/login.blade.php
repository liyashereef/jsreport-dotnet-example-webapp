@extends('layouts.app')
@section('css')
<style>
    .logomargin{
        margin-top:17%;
    }
    .logomargin img{
        max-width: 100%;
    }
    body{
        background: white !important
    }
    @media only screen and (max-width: 780px) {
    .logomargin{
        display: none;
    }
}
</style>
    
@endsection
@section('content')
<section class="container">
    <div class="row">
        <div class="col-md-6 logomargin">
            <img class="content-landing-img"   src="{{asset('images/CGL-LOGO-600px-152px.png')}}" />

        </div>
        <div class="login-page col-md-6">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h2 class="orange login-header">@lang('Login')</h2>
                    <hr>
                </div>
            </div>
            <!--  <h3>@lang('Login')</h3> -->
            <form role="form" class="form-horizontal" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}            
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">  
                        <div class="form-group">   
                            <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-user"></i></div>  
                                <input id="log" type="text" placeholder="@lang('Username or Email')" class="form-control" name="log" value="{{ old('log') }}" required autofocus>
    
                            </div>
                            
                        </div>
                    </div>
    
                </div>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-key"></i></div>
                                <input id="password" type="password" placeholder="@lang('Password')" class="form-control" name="password" required>
                            </div>
                            {!! $errors->first('log', '<span class="text-danger align-middle font-12"><i class="fa fa-close"></i>:message</span>') !!}                           
                        </div>
                    </div>
                </div>   
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6" style="padding-top: .35rem">
                        <div class="form-check mb-2 mr-sm-2 mb-sm-0">
                            <label class="form-check-label">
                                <input class="form-check-input checkbox-alignment" name="remember"
                                       type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                       <span style="padding-bottom: .15rem">@lang('Remember me')</span>
                            </label>
                        </div>
                    </div>
                </div>  
                <div class="row text-xs-right text-sm-right text-md-right" style="padding-bottom: 1rem">
                    <div class="col-md-5"></div>
                    <div class="col-md-4">
                        <button type="submit" class="btn submit" value="@lang('Login')"><i class="fa fa-sign-in"></i> Login</button>
                    </div>
                </div>       
                <!-- <label class="add-bottom">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> 
                           <span class="label-text">@lang('Remember me')</span>
                </label>
                <input class="button-primary full-width-on-mobile" type="submit" value="@lang('Login')"> -->                   
            </form>
        </div>
    </div>
    
</section>
@endsection
