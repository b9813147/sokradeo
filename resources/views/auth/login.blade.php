@extends('layouts.auth')

@inject('publicUrlPres', 'App\Presenters\App\Url\PublicPresenter')

@section('app-content')
    <card class="login">
        <div style="text-align: center">
            <img src="{{$publicUrlPres->image('app', 'teammodel/original-black-small.png', true)}}" style="width: 200px;">
            <br/><br/>
            <i-button v-on:click="loginAsHabook" long>{{ __('app/auth.login') }}</i-button>
        </div>
    </card>
@endsection
