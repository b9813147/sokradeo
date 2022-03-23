@extends('layouts.management.home')

@section('content')
<row>
    <i-col span="8" offset="8">
        <a href="{{route('management.user.info')  }}" class="ivu-btn ivu-btn-long">資訊統計</a>
        <a href="{{route('management.user.manage')}}" class="ivu-btn ivu-btn-long">成員管理</a>
    </i-col>
</row>
@endsection
