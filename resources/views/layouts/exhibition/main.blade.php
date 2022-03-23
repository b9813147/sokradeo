@extends('app.layouts.common')

@inject('publicUrlPres', 'App\Presenters\App\Url\PublicPresenter')

@section('content')
    <layout>
        <i-header class="header" style="background: #000000;">
            <i-menu mode="horizontal" theme="dark" v-on:on-select="navigate">
                {{-- Desktop --}}
                <Row v-if="!isMobileBrowser">
                    {{-- Logo and Web App Name --}}
                    <i-col :lg="4" :md="4" :sm="0" :xs="0">
                        <menu-item name="home" class="title" style="height: 56px">
                            <img src="{{ $publicUrlPres->image('app', 'teammodel/sokrates-logo.png', true) }}"
                                style="height: 56px; padding: 8px;">
{{--                            <span style="vertical-align: text-bottom;">{{ __('app/base.sokradeo') }}</span>--}}
                        </menu-item>
                    </i-col>
                    {{-- Searchbar --}}
                    <i-col :lg="2" :md="2" :sm="0" :xs="0" push="4" style="z-index: 1000">
                        <i-input icon="ios-search" placeholder="{{ __('app/base.inputs.enter') }}..."
                            v-model.trim="keywordSelect.selected" v-on:on-change="searchKeywords"
                            v-on:on-click="searchKeyword" v-on:keyup.native.enter="searchKeyword" style="cursor: pointer">
                        </i-input>
                        <dropdown-menu slot="list">
                            <dropdown-item v-for="(v, i) in keywordSelect.list" :key="i" :name="v.name">
                                @{{ v.name }}
                            </dropdown-item>
                        </dropdown-menu>
                    </i-col>
                    {{-- Navbar End Area --}}
                    <i-col :lg="18" :md="18" :sm="24" :xs="24">
                        <Row style="float: right">
                            {{-- My Sokrates --}}
                            <i-col span="8">
                                <menu-item name="my-sokrates">
                                    <router-link to="/myMovie">
                                        <i-button class="my-sokrates-menu">
                                            <span>
                                                {{ __('app/base.my-sokradeo') }}
                                            </span>
                                        </i-button>
                                    </router-link>
                                </menu-item>
                            </i-col>
                            {{-- Notifications --}}
                            <i-col span="3">
                                <Row v-if="logined">
                                    <menu-item name="notifications">
                                        <router-link to="/myMovie?display=notifications">
                                            <Badge :count="unreadNotificationCount" :overflow-count="notificationCountLimit"
                                                class-name="notif-badge">
                                                <Icon type="ios-bell-outline" size="22" style="color: #ffffffb3;">
                                                </Icon>
                                            </Badge>
                                        </router-link>
                                    </menu-item>
                                </Row>
                            </i-col>
                            {{-- Lang --}}
                            <i-col span="6">
                                <menu-item name="lang">
                                    <Icon type="ios-world-outline" size="20"></Icon>
                                    <select v-model="selectedLangUrl" v-on:change="switchLang"
                                        style="background-color: #080808; color: #FFFFFF; border: 1px;">
                                        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}
                                            style="color: white;">
                                            {{ __('app/base.langs.en') }}
                                        </option>
                                        <option value="tw" {{ app()->getLocale() === 'tw' ? 'selected' : '' }}
                                            style="color: white;">
                                            {{ __('app/base.langs.tw') }}
                                        </option>
                                        <option value="cn" {{ app()->getLocale() === 'cn' ? 'selected' : '' }}
                                            style="color: white;">
                                            {{ __('app/base.langs.cn') }}
                                        </option>
                                    </select>
                                </menu-item>
                            </i-col>
                            {{-- UserInfo --}}
                            <i-col span="4">
                                <Row v-if="logined">
                                    <menu-item name="user-info-sidebar">
                                        <Avatar class="user-info-avatar" :src="urlAvatar"></Avatar>
                                    </menu-item>
                                </Row>
                                <Row v-else>
                                    <menu-item name="login">
                                        <icon type="person" size="20"></icon>
                                        <span>{{ __('app/auth.login') }}</span>
                                    </menu-item>
                                </Row>
                            </i-col>
                        </Row>
                    </i-col>
                </Row>
                {{-- Mobile --}}
                <Row v-else>
                    {{-- Search Icon --}}
                    <i-col span="4">
                        <menu-item name="search" class="mobile-menu">
                            <Icon type="ios-search" size="22" @click="toggleMobileSearchbar"></Icon>
                        </menu-item>
                    </i-col>
                    {{-- My Sokrates --}}
                    <i-col span="4">
                        <menu-item name="my-sokrates" class="mobile-menu">
                            <router-link to="/myMovie">
                                <i-button class="my-sokrates-menu">
                                    <span>
                                        {{ __('app/base.my-sokradeo') }}
                                    </span>
                                </i-button>
                            </router-link>
                        </menu-item>
                    </i-col>
                    {{-- User --}}
                    <i-col span="16">
                        <Row v-if="logined" style="float: right">
                            <i-col span="8">
                                <menu-item name="notifications" class="mobile-menu">
                                    <router-link to="/myMovie?display=notifications">
                                        <Badge :count="unreadNotificationCount" :overflow-count="notificationCountLimit"
                                            class-name="notif-badge">
                                            <Icon type="ios-bell-outline" size="20" style="color: #ffffffb3;">
                                            </Icon>
                                        </Badge>
                                    </router-link>
                                </menu-item>
                            </i-col>
                            <i-col span="12">
                                <menu-item name="user-info-sidebar" class="mobile-menu">
                                    <Avatar class="user-info-avatar" :src="urlAvatar"></Avatar>
                                </menu-item>
                            </i-col>
                        </Row>
                        <Row v-else style="float: right">
                            <i-col span="24">
                                <menu-item name="login" class="mobile-menu">
                                    <icon type="person" size="22"></icon>
                                    <span>{{ __('app/auth.login') }}</span>
                                </menu-item>
                            </i-col>
                        </Row>
                    </i-col>
                </Row>
                {{-- Mobile Searchbar --}}
                <Row v-show="mobileSearchbar.show" justify="center" align="middle" class="searchbar-container-mobile">
                    <i-col span="24">
                        <i-input icon="ios-search" placeholder="{{ __('app/base.inputs.enter') }}..."
                            v-model.trim="keywordSelect.selected" v-on:on-change="searchKeywords"
                            v-on:on-click="searchKeyword" v-on:keyup.native.enter="searchKeyword" style="cursor: pointer">
                        </i-input>
                    </i-col>
                </Row>
            </i-menu>
        </i-header>
        <layout>
            <layout>
                <router-view name="navtools" style="padding: 0 20px;"></router-view>
                <i-content style="overflow-y: hidden">
                    @yield('app-content')
                </i-content>
            </layout>
            <layout class="sider right">
                <transition name="sider-r">
                    <div class="container" v-show="!this.$store.state.sider.right.collapsed"
                        v-on-clickaway="closeVisibleUserSideBar">
                        @yield('app-sider')
                    </div>
                </transition>
            </layout>
        </layout>

        @include('app.footers.common')
    </layout>

    <!-- modal -->
    <modal class="modal flat" class-name="ivu-modal-vertical-center" v-model="mixin.modal.login.value"
        v-bind:closable="mixin.modal.login.closable" v-bind:mask-closable="mixin.modal.login.maskClosable"
        title="{{ __('app/auth.login') }}">
        <div slot="close"></div>
        <div slot="header"></div>
        <div slot="footer"></div>
        <cpnt-login v-bind:to="mixin.modal.login.to"></cpnt-login>
    </modal>

    <!-- Default Channel Prompt Modal -->
    <modal class="modal flat" class-name="ivu-modal-vertical-center" v-model="mixin.modal.defaultChannelPrompt.value"
        v-bind:closable="mixin.modal.defaultChannelPrompt.closable"
        v-bind:mask-closable="mixin.modal.defaultChannelPrompt.maskClosable" title="Default Channel Prompt">
        <div slot="close"></div>
        <div slot="header"></div>
        <div slot="footer"></div>
        <cpnt-default-channel-prompt v-bind:on-cancel="closeDefaultChannelPrompt"></cpnt-default-channel-prompt>
    </modal>
@endsection
