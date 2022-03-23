@extends('app.layouts.common')

@inject('publicUrlPres', 'App\Presenters\App\Url\PublicPresenter')

@section('content')
    <layout>
        <i-header class="header" style="background: #000000;">
            <i-menu mode="horizontal" theme="dark" v-on:on-select="navigate">
                <Row>
                    {{-- Logo and Web App Name --}}
                    <i-col :lg="4" :md="4" :sm="0" :xs="0">
                        <a href="{{ url('/district') . '/' . $globals['abbr'] }}">
                            <menu-item name="home" class="title" style="height: 56px">
                                @if ($globals['thumbnail'])
                                    <img src="{{ $globals['thumbnail'] }}" style="height: 56px; padding: 8px;">
                                @else
                                    <img src="{{ $publicUrlPres->image('app', 'teammodel/default-channel-logo.png', true) }}"
                                        style="height: 56px; padding: 8px;">
                                @endif
                                <span style="vertical-align: text-bottom;">{{ $globals['name'] }}</span>
                            </menu-item>
                        </a>
                    </i-col>
                    {{-- Navbar End Area --}}
                    <i-col :lg="20" :md="20" :sm="24" :xs="24">
                        <Row style="float: right">
                            {{-- My Sokrates --}}
                            <i-col span="8">
                                <menu-item name="my-sokrates">
                                    <a href="{{ url(env('APP_URL')) }}/exhibition/tbavideo#/myMovie">
                                        <i-button class="my-sokrates-menu">
                                            <span>
                                                {{ __('app/base.my-sokradeo') }}
                                            </span>
                                        </i-button>
                                    </a>
                                </menu-item>
                            </i-col>
                            {{-- Notifications --}}
                            <i-col span="3">
                                <Row v-if="logined">
                                    <menu-item name="notifications">
                                        <a style="text-decoration-line: none;color:white"
                                            href="{{ url(env('APP_URL')) }}/exhibition/tbavideo#/myMovie?display=notifications">
                                            <Badge :count="unreadNotificationCount" :overflow-count="notificationCountLimit"
                                                class-name="notif-badge">
                                                <Icon type="ios-bell-outline" size="22" style="color: #ffffffb3;">
                                                </Icon>
                                            </Badge>
                                        </a>
                                    </menu-item>
                                </Row>
                            </i-col>
                            {{-- Lang --}}
                            <i-col span="6">
                                <menu-item name="lang">
                                    <Icon type="ios-world-outline"></Icon>
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
                            <i-col span="6">
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
@endsection
