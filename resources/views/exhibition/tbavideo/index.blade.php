@extends('layouts.exhibition.main')

@section('app-content')
    <router-view
        v-on:check-logined="checkLogined"
        v-on:display-default-channel-prompt=displayDefaultChannelPrompt
    ></router-view>
@endsection

@section('app-sider')
    <cpnt-user-info-sidebar v-if="logined" area="exhibition"></cpnt-user-info-sidebar>
@endsection
