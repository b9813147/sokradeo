<i-footer class="footer">
    <div>
        <a href="{{Config::get('srvs.teammodel.institute.url')}}">
            {{ __('app/footer.links.teammodel-research-institute')}}
        </a> |
        <a href="http://sokrates.teammodel.org/district/TTTA">
            {{ __('app/footer.links.teacher-training-ai-innovation-academy')}}
        </a> |
        <a href="http://sokrates.teammodel.cn/district/TTTA">
            {{ __('app/footer.links.teacher-training-ai-innovation-academy-cn')}}
        </a> |
        <a href="{{route('home.about')}}">
            {{ __('app/footer.links.about') }}
        </a> |
        <a href="{{route('welcome')}}">
            {{ __('app/footer.links.home') }}
        </a>
        {{--		|--}}
        {{--    	<a>{{ __('app/footer.links.habook-explanation') }}</a> |--}}
        {{--    	<a>{{ __('app/footer.links.how-to-upload')      }}</a> |--}}
        {{--    	<a>{{ __('app/footer.links.contact')            }}</a>--}}
    </div>
    <div class="copyright">
        <p>{!!  __('app/footer.copyright') !!}</p>
    </div>
</i-footer>
