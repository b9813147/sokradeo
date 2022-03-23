@extends('layouts.group.group')

@section('app-sider')
<i-menu mode="vertical" width="auto">
    <submenu name="group">
        <template slot="title">
            <icon type="ios-americanfootball"></icon>群組
        </template>
        <menu-item name="group-home"  ><router-link v-bind:to="{name: 'home'  }" tag="div">首頁</router-link></menu-item>
        <menu-item name="group-about" ><router-link v-bind:to="{name: 'about' }" tag="div">關於</router-link></menu-item>
        <menu-item name="group-member"><router-link v-bind:to="{name: 'member'}" tag="div">成員</router-link></menu-item>
        @if ($managed)
        	<menu-item name="group-manage"><a href="{{route('group.manage', [$groupId])}}">管理</a></menu-item>
        @endif
    </submenu>
    <submenu name="channels">
        <template slot="title">
            <icon type="ios-americanfootball"></icon>頻道
        </template>
        <menu-item v-for="v in channels" v-bind:key="v.id" v-bind:name="'channels-'+v.id">
        	<router-link v-bind:to="{name: 'channel', params: {id: v.id, cmsType: v.cms_type}}" tag="div">@{{v.name}}</router-link>
        </menu-item>
    </submenu>
</i-menu>
@endsection

@section('app-content')
<section class="tools">
    <span class="title">group name</span>
    <div  class="items">
    	
    </div>
</section>
<section class="context">
	<router-view></router-view>
</section>
@endsection
