<script>

App.Service.Api.TbaPlayer.baseUrl = '';
Component.TbaPlayer.helper.register(App.Store.main, {module: 'tbaplayer', apiSrv: App.Service.Api.TbaPlayer});

window.onload = function () {

    var app = new Vue({
    	
    	store: App.Store.main,

    	i18n : App.Lang.main,
    	
        components: {
        	'cpnt-tbaplayer': Component.TbaPlayer.main,
        },
    	
        data: {
        	tbaPlayer: {
    			options: Globals.tbaPlayerOpts
    		},
        },
    	
        methods: {
    		
            init: function (tbaPlayerInfo) {
            	this.$store.dispatch('tbaplayer/info', tbaPlayerInfo);
            },
    		
        },
    	
        mounted: function () {
    		
        	this.$store.dispatch('tbaplayer/init');
    		
        }
    	
    });
    
    app.init(Globals.tbaPlayerInfo);
    app.$mount('#app');

}

</script>