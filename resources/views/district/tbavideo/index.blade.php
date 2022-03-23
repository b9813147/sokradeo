@extends('layouts.district.main')

@section('app-content')
    <router-view v-on:check-logined="checkLogined"></router-view>
@endsection

@section('app-sider')
    <cpnt-user-info-sidebar v-if="logined" area="district"></cpnt-user-info-sidebar>
@endsection
