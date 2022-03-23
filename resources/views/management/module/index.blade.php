@extends('layouts.management.home')

@section('content')
<row>
    <i-col span="8" offset="8">
        <a href="{{route('management.module.info')  }}" class="ivu-btn ivu-btn-long">資訊統計</a>
        <a href="{{route('management.module.manage')}}" class="ivu-btn ivu-btn-long">模組管理</a>
    </i-col>
</row>
@endsection
