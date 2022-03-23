@extends('layouts.group.group')

@section('app-sider')
<i-menu mode="vertical" width="auto">
    <submenu name="group">
        <template slot="title">
            <icon type="ios-americanfootball"></icon>群組
        </template>
        <menu-item name="group-home"><router-link v-bind:to="{name: 'home'}" tag="div">首頁</router-link></menu-item>
    </submenu>
    <submenu name="member">
        <template slot="title">
            <icon type="ios-americanfootball"></icon>成員
        </template>
        <menu-item name="member-valid"  ><router-link v-bind:to="{name: 'members', params: {fun: 'valid'  }}" tag="div">群組成員</router-link></menu-item>
        <menu-item name="member-applied"><router-link v-bind:to="{name: 'members', params: {fun: 'applied'}}" tag="div">加入申請</router-link></menu-item>
    </submenu>
    <submenu name="channels">
        <template slot="title">
            <icon type="ios-americanfootball"></icon>頻道
        </template>
        <menu-item name="channels-channels"><router-link v-bind:to="{name: 'channels'}" tag="div">群組頻道</router-link></menu-item>
        <menu-group title="頻道">
            <menu-item v-for="v in channels" v-bind:key="v.id" v-bind:name="'channels-'+v.id">
            	<router-link v-bind:to="{name: 'channel', params: {id: v.id, cmsType: v.cms_type}}" tag="div">@{{v.name}}</router-link>
            </menu-item>
        </menu-group>
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
