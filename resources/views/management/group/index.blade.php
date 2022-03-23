@extends('layouts.management.home')

@section('content')
<row>
    <i-col span="8" offset="8">
        <a href="{{route('management.group.info')  }}" class="ivu-btn ivu-btn-long">資訊統計</a>
        <a href="{{route('management.group.manage')}}" class="ivu-btn ivu-btn-long">群組管理</a>
    </i-col>
</row>
@endsection
