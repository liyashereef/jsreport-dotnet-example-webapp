@extends('adminlte::page')

@section('title', 'Mail Settings')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h1>Mail Settings</h1>
@stop
@section('content')
<div class="row">
    @inject('envRepository', 'Modules\Admin\Repositories\EnvRepository')
    <div class="col-md-12">
        <form method="post" action="{{ route('settings.update', ['page' => 5]) }}">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            @include('admin::partials.input', [
            'input' => [
            'title' => __('Sender Mail Address'),
            'name' => 'mail_from_address',
            'value' => old('mail_from_address', $envRepository->get('MAIL_FROM_ADDRESS')),
            'input' => 'mail',
            'required' => true,
            ],
            ])
            <div class="form-group">
                <label for="mail_driver">@lang('Driver')</label>
                <select id="mail_driver" name="mail_driver" class="form-control">
                    @foreach($drivers as $key => $value)
                    <option value="{{ $key }}" {{ old('mail_driver') ? ($key === old('mail_driver') ? 'selected' : '') : $key === $actualDriver ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div id="smtp" @if (old('mail_driver', $actualDriver) === 'mail') style="display: none" @endif>
                 @include('admin::partials.input', [
                 'input' => [
                 'title' => __('Host'),
                 'name' => 'mail_host',
                 'value' => old('mail_host', $envRepository->get('MAIL_HOST')),
                 'input' => 'text',
                 'required' => true,
                 ],
                 ])
                 @include('admin::partials.input', [
                 'input' => [
                 'title' => __('Port'),
                 'name' => 'mail_port',
                 'value' => old('mail_port', $envRepository->get('MAIL_PORT')),
                 'input' => 'text',
                 'required' => false,
                 ],
                 ])
                 @include('admin::partials.input', [
                 'input' => [
                 'title' => __('Username'),
                 'name' => 'mail_username',
                 'value' => old('mail_username', $envRepository->get('MAIL_USERNAME')),
                 'input' => 'mail',
                 'required' => false,
                 ],
                 ])
                 @include('admin::partials.input', [
                 'input' => [
                 'title' => __('Password'),
                 'name' => 'mail_password',
                 'value' => old('mail_password', $envRepository->get('MAIL_PASSWORD')),
                 'input' => 'password',
                 'required' => false,
                 ],
                 ])
                 @include('admin::partials.input', [
                 'input' => [
                 'title' => __('Encryption'),
                 'name' => 'mail_encryption',
                 'value' => old('mail_encryption', $envRepository->get('MAIL_ENCRYPTION')),
                 'input' => 'text',
                 'required' => false,
                 ],
                 ])
        </div>
        <button class="btn btn-primary blue" type="submit">@lang('Submit')</button>
    </form>
</div>
</div>
@endsection
@section('js')
<script>
    $(function () {
        $('#mail_driver').change(function () {
            if ($(this).val() == 'smtp') {
                $('#smtp').show().slow()
            } else {
                $('#smtp').hide().slow()
            }
        })
    })
</script>
@endsection
