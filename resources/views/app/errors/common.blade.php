@extends('app.layouts.general')
@section('content')
    <layout>
        <i-header>

        </i-header>
        <i-content>
            <card style="width : 640px; margin: 16px auto;">
                <div style="text-align: center;">
                    <img src="{{ url('/images/app/tw/teammodel/logo.png') }}" style="width: 200px;">
                    @if(app()->isDownForMaintenance())
                        <p style="word-wrap: break-word;">{{ __('app/auth.maintain') }}</p>
                    @else
                        <br/><br/>
                        <p>{{date('Y-m-d/H:i:s')}}</p>
                        <p style="word-wrap: break-word;">{{__('app/auth.description')}}</p>
                        <p style="word-wrap: break-word;">{{__('app/auth.description_2')}}</p>
                        <p style="word-wrap: break-word;">{{__('app/auth.description_3')}}</p>
                        <i-button>
                            <a href="{{ route('auth.login.loginashabook') }}" class="btn-primary">{{ __('app/auth.login') }}</a>
                        </i-button>
                    @endif


                    @if (env('APP_ENV') === 'local')
                        <p style="word-wrap: break-word;">{{$message}}</p>
                    @endif

                </div>
            </card>
        </i-content>

        <section style="text-align: center;">
            @include('app.footers.common')
        </section>
    </layout>
@endsection

@section('supplement')
    <script>
      new Vue({
        el: '#app',

        data: {},

        methods: {}
      })
    </script>
@endsection
