@extends('layouts.management.manage')

@section('app-content')
<section class="tools">
    <span class="title">成員管理</span>
    <div  class="items">
		<i-button v-on:click="modal.create=true"><icon type="plus"></icon></i-button>
    </div>
</section>
<section class="list">
	<i-table stripe border v-bind:columns="list.fields" v-bind:data="list.items"></i-table>
</section>
<section class="pager">
	<page 
    	v-bind:total="list.total"
    	v-bind:page-size="pager.per"
    	v-bind:current="pager.current"
    	v-on:on-change="getPagination">
    </page>
</section>

<!-- edit -->
<modal v-model="modal.edit" title="成員編輯" v-on:on-visible-change="getUser" v-on:on-ok="setUser">
	<i-form ref="editForm" v-bind:model="userInfo" v-bind:label-width="80">
		<form-item label="電子信箱">
			<p>@{{ userInfo.email }}</p>
        </form-item>
        <form-item label="姓名">
			<p>@{{ userInfo.name }}</p>
        </form-item>
        <form-item label="新增日期">
			<p>@{{ userInfo.created_at }}</p>
        </form-item>
        <form-item label="修改日期">
			<p>@{{ userInfo.updated_at }}</p>
        </form-item>
        <form-item label="角色">
        	<cpnt-select multiple label="type" placeholder="請輸入..."
                v-bind:options="{{$roles}}"
                v-bind:value.sync="userInfo.roles">
        	</cpnt-select>
        </form-item>
	</i-form>
</modal>
@endsection
