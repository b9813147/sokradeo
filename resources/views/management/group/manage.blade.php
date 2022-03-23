@extends('layouts.management.manage')

@section('app-content')
<section class="tools">
    <span class="title">群組管理</span>
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

<!-- create -->
<modal v-model="modal.create" title="群組新增" v-on:on-visible-change="getGroup" v-on:on-ok="createGroup">
	<i-form ref="createForm" v-bind:model="groupInfo" v-bind:label-width="80">
		<form-item label="學校碼">
            <i-input type="text" v-model="groupInfo.school_code" placeholder="請輸入..."></i-input>
        </form-item>
		<form-item label="名稱">
            <i-input type="text" v-model="groupInfo.name" placeholder="請輸入..."></i-input>
        </form-item>
        <form-item label="描述">
            <i-input type="text" v-model="groupInfo.description" placeholder="請輸入..."></i-input>
        </form-item>
        <form-item label="狀態">
            <i-select v-model="groupInfo.status">
                <i-option v-bind:value="0">關閉</i-option>
                <i-option v-bind:value="1">開啟</i-option>
            </i-select>
        </form-item>
        <form-item label="公開">
            <i-select v-model="groupInfo.public">
                <i-option v-bind:value="0">否</i-option>
                <i-option v-bind:value="1">是</i-option>
            </i-select>
        </form-item>
        <form-item label="管理員">
            <cpnt-select multiple label="name" placeholder="請輸入..."
                v-bind:debounce="250"
                v-bind:on-search="searchUsers"
                v-bind:options="userSelect.list"
                v-bind:value.sync="groupInfo.admins">
            </cpnt-select>
        </form-item>
	</i-form>
</modal>

<!-- edit -->
<modal v-model="modal.edit" title="群組編輯" v-on:on-visible-change="getGroup" v-on:on-ok="setGroup">
	<i-form ref="editForm" v-bind:model="groupInfo" v-bind:label-width="80">
		<form-item label="學校碼">
			<p>@{{ groupInfo.school_code }}</p>
        </form-item>
		<form-item label="名稱">
			<p>@{{ groupInfo.name }}</p>
        </form-item>
        <form-item label="描述">
            <i-input type="text" v-model="groupInfo.description" placeholder="請輸入..."></i-input>
        </form-item>
        <form-item label="狀態">
            <i-select v-model="groupInfo.status">
                <i-option v-bind:value="0">關閉</i-option>
                <i-option v-bind:value="1">開啟</i-option>
            </i-select>
        </form-item>
        <form-item label="公開">
            <i-select v-model="groupInfo.public">
                <i-option v-bind:value="0">否</i-option>
                <i-option v-bind:value="1">是</i-option>
            </i-select>
        </form-item>
        <form-item label="管理員">
            <cpnt-select multiple label="name" placeholder="請輸入..."
                v-bind:debounce="250"
                v-bind:on-search="searchUsers"
                v-bind:options="userSelect.list"
                v-bind:value.sync="groupInfo.admins">
            </cpnt-select>
        </form-item>
	</i-form>
</modal>
@endsection
