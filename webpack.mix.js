/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Global
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */

/*
 * config
 * */
const config = {
	module: {
		autoload: {
			'axios'   : ['axios', 'window.axios'],
			'jquery'  : ['$', 'jQuery', 'window.$', 'window.jQuery'],
			'lodash'  : ['lodash', 'window._'],
			'video.js': ['videojs', 'window.videojs'],
			'vue'     : ['Vue', 'window.Vue'],
		},
		resrces: {
			'bootstrap-sass': [{
				type: 'fonts',
				src : 'assets/fonts/bootstrap',
				tar : 'bootstarp',
			}],
			'iview': [
				{
					type: 'locale',
					src : 'dist/locale',
					tar : '',
				},
				{
					type: 'fonts',
					src : 'dist/styles/fonts',
					tar : '',
				},
			],
			'video.js': [
				{
					type: 'font',
					src : 'dist/font',
					tar : '',
				},
				{
					type: 'lang',
					src : 'dist/lang',
					tar : '',
				},
			],
		},
	},
};

/*
 * mix
 * */
const mix = require('laravel-mix');

mix.webpackConfig({
    // ...
    module: {
        rules: [
            {
                // Rules are copied from laravel-mix@1.5.1 /src/builder/webpack-rules.js and manually merged with the ia8n-loader. Make sure to update the rules to the latest found in webpack-rules.js
                test: /\.vue$/,
                loader: 'vue-loader',
                exclude: /bower_components/,
                options: {
                    // extractCSS: Config.extractVueStyles,
                    loaders: Config.extractVueStyles ? {
                        js: {
                            loader: 'babel-loader',
                            options: Config.babel()
                        },

                        scss: vueExtractPlugin.extract({
                            use: 'css-loader!sass-loader',
                            fallback: 'vue-style-loader'
                        }),

                        sass: vueExtractPlugin.extract({
                            use: 'css-loader!sass-loader?indentedSyntax',
                            fallback: 'vue-style-loader'
                        }),

                        css: vueExtractPlugin.extract({
                            use: 'css-loader',
                            fallback: 'vue-style-loader'
                        }),

                        stylus: vueExtractPlugin.extract({
                            use: 'css-loader!stylus-loader?paths[]=node_modules',
                            fallback: 'vue-style-loader'
                        }),

                        less: vueExtractPlugin.extract({
                            use: 'css-loader!less-loader',
                            fallback: 'vue-style-loader'
                        }),

                        i18n: '@kazupon/vue-i18n-loader',
                    } : {
                        js: {
                            loader: 'babel-loader',
                            options: Config.babel()
                        },

                        i18n: '@kazupon/vue-i18n-loader',
                    },
                    postcss: Config.postCss,
                    preLoaders: Config.vue.preLoaders,
                    postLoaders: Config.vue.postLoaders,
                    esModule: Config.vue.esModule
                }
            },
            // ...
        ]
    },
    // ...
});

/*
 * Helper
 * */
const Helper = {
	Path   : {},
	Process: {},
};
Helper.Path = {
	PUBLIC: {
		lib: (path, file) => 'public/lib/' + path + '/' + (file ? file : ''),
	},
	assets: {
		src: (type, path, file) => 'resources/assets/' + path + '/' + (type ? type + '/' : '') + file,
		tar: (type, path, file) => 'public/assets/'    + path + '/' + (type ? type + '/' : '') + (file ? file : ''),
	}
};
Helper.Process = {
	copyModuleResrc: (module, resrces, path) => {
		/* ex:
		 * src = 'node_modules/bootstrap-sass/assets/fonts/bootstrap'
		 * tar = Helper.Path.assets.tar('fonts', 'app/layouts/common', 'vendor/bootstrap-sass/bootstarp')
		 * */
		for (let resrc of resrces) {
			let src = 'node_modules/' + module + '/' + resrc.src;
			let tar = Helper.Path.assets.tar(resrc.type, path, 'vendor/' + module + '/' + resrc.tar);
			mix.copyDirectory(src, tar);
		}
	}
};


/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Tbaplayer
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
/*
mix
.sass(Helper.Path.assets.src('sass', 'app/modules/tbaplayer/styles', 'index.scss'          ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'tbaplayer.css'      ))
.sass(Helper.Path.assets.src('sass', 'app/modules/tbaplayer/styles', 'skins/dark-blue.scss'), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'skins/dark-blue.css'))
.js  (Helper.Path.assets.src(false,  'app/modules/tbaplayer',        'index.js'            ), Helper.Path.assets.tar('js',  'app/modules/tbaplayer', 'tbaplayer.js'       ))
.options({processCssUrls: false});

Helper.Process.copyModuleResrc('iview',    config.module.resrces['iview'],    'app/modules/tbaplayer');
Helper.Process.copyModuleResrc('video.js', config.module.resrces['video.js'], 'app/modules/tbaplayer');
mix.copyDirectory(Helper.Path.PUBLIC.lib('videojs/ezStation/font'), Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/'));

mix.copyDirectory(Helper.Path.assets.tar('fonts', 'app/modules/tbaplayer', 'vendor/iview'), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'fonts'));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/VideoJS.eot'  ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/VideoJS.eot'  ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/VideoJS.svg'  ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/VideoJS.svg'  ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/VideoJS.ttf'  ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/VideoJS.ttf'  ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/VideoJS.woff' ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/VideoJS.woff' ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/icomoon.eot'  ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/icomoon.eot'  ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/icomoon.svg'  ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/icomoon.svg'  ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/icomoon.ttf'  ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/icomoon.ttf'  ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/icomoon.woff' ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/icomoon.woff' ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/Glyphter.eot' ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/Glyphter.eot' ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/Glyphter.svg' ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/Glyphter.svg' ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/Glyphter.ttf' ), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/Glyphter.ttf' ));
mix.copy(Helper.Path.assets.tar('font', 'app/modules/tbaplayer', 'vendor/video.js/Glyphter.woff'), Helper.Path.assets.tar('css', 'app/modules/tbaplayer', 'font/Glyphter.woff'));
mix.copy(Helper.Path.PUBLIC.lib('videojs/lang/en.js'), Helper.Path.assets.tar('lang', 'app/modules/tbaplayer', 'vendor/video.js/en.js'));
mix.copy(Helper.Path.PUBLIC.lib('videojs/lang/tw.js'), Helper.Path.assets.tar('lang', 'app/modules/tbaplayer', 'vendor/video.js/tw.js'));
mix.copy(Helper.Path.PUBLIC.lib('videojs/lang/cn.js'), Helper.Path.assets.tar('lang', 'app/modules/tbaplayer', 'vendor/video.js/cn.js'));

mix.scripts([
	'public/lib/chartjs/Chart.js',
	'public/lib/chartjs/Chart.el.group.js',
	'public/lib/chartjs/Chart.linerange.js',
	'public/lib/chartjs/Chart.plugin.linerange.js',
	'public/lib/chartjs/chartjs-plugin-annotation.min.js',
	'public/lib/chartjs/chartjs-plugin-draggable.min.js',
	'public/assets/app/modules/tbaplayer/js/tbaplayer.js',
],  'public/assets/app/modules/tbaplayer/js/tbaplayer.rice.js');

mix.copyDirectory('public/assets/app/modules/tbaplayer', 'public/lib/tbaplayer');

return;
*/


/* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * Script
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */

mix
// home:main
.sass(Helper.Path.assets.src('sass', 'home/main/styles', 'app.scss'), Helper.Path.assets.tar('css', 'home/main')).version()
.js  (Helper.Path.assets.src(false,  'home/main',        'app.js'  ), Helper.Path.assets.tar('js',  'home/main')).version()

// home:about
.sass(Helper.Path.assets.src('sass', 'home/about/styles', 'app.scss'), Helper.Path.assets.tar('css', 'home/about')).version()
.js  (Helper.Path.assets.src(false,  'home/about',        'app.js'  ), Helper.Path.assets.tar('js',  'home/about')).version()

// auth:login
.sass(Helper.Path.assets.src('sass', 'auth/login/styles', 'app.scss'), Helper.Path.assets.tar('css', 'auth/login')).version()
.js  (Helper.Path.assets.src(false,  'auth/login',        'app.js'  ), Helper.Path.assets.tar('js',  'auth/login')).version()

// cms:video:index
.sass(Helper.Path.assets.src('sass', 'cms/video/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'cms/video/index')).version()
.js  (Helper.Path.assets.src(false,  'cms/video/index',        'app.js'  ), Helper.Path.assets.tar('js',  'cms/video/index')).version()
// cms:video:watch
.sass(Helper.Path.assets.src('sass', 'cms/video/watch/styles', 'app.scss'), Helper.Path.assets.tar('css', 'cms/video/watch')).version()
.js  (Helper.Path.assets.src(false,  'cms/video/watch',        'app.js'  ), Helper.Path.assets.tar('js',  'cms/video/watch')).version()

// cms:tba:index
.sass(Helper.Path.assets.src('sass', 'cms/tba/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'cms/tba/index')).version()
.js  (Helper.Path.assets.src(false,  'cms/tba/index',        'app.js'  ), Helper.Path.assets.tar('js',  'cms/tba/index')).version()
// cms:tba:watch
.sass(Helper.Path.assets.src('sass', 'cms/tba/watch/styles', 'app.scss'), Helper.Path.assets.tar('css', 'cms/tba/watch')).version()
.js  (Helper.Path.assets.src(false,  'cms/tba/watch',        'app.js'  ), Helper.Path.assets.tar('js',  'cms/tba/watch')).version()

// cms:tbavideo:index
.sass(Helper.Path.assets.src('sass', 'cms/tbavideo/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'cms/tbavideo/index')).version()
.js  (Helper.Path.assets.src(false,  'cms/tbavideo/index',        'app.js'  ), Helper.Path.assets.tar('js',  'cms/tbavideo/index')).version()
// cms:tbavideo:watch
.sass(Helper.Path.assets.src('sass', 'cms/tbavideo/watch/styles', 'app.scss'), Helper.Path.assets.tar('css', 'cms/tbavideo/watch')).version()
.js  (Helper.Path.assets.src(false,  'cms/tbavideo/watch',        'app.js'  ), Helper.Path.assets.tar('js',  'cms/tbavideo/watch')).version()

// exhibition:tbavideo
.sass(Helper.Path.assets.src('sass', 'exhibition/tbavideo/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'exhibition/tbavideo/index')).version()
.js  (Helper.Path.assets.src(false,  'exhibition/tbavideo/index',        'app.js'  ), Helper.Path.assets.tar('js',  'exhibition/tbavideo/index')).version()

// group:list
.sass(Helper.Path.assets.src('sass', 'group/list/styles', 'app.scss'), Helper.Path.assets.tar('css', 'group/list')).version()
.js  (Helper.Path.assets.src(false,  'group/list',        'app.js'  ), Helper.Path.assets.tar('js',  'group/list')).version()

// group:main
.sass(Helper.Path.assets.src('sass', 'group/main/styles', 'app.scss'), Helper.Path.assets.tar('css', 'group/main')).version()
.js  (Helper.Path.assets.src(false,  'group/main',        'app.js'  ), Helper.Path.assets.tar('js',  'group/main')).version()

// group:manage
.sass(Helper.Path.assets.src('sass', 'group/manage/styles', 'app.scss'), Helper.Path.assets.tar('css', 'group/manage')).version()
.js  (Helper.Path.assets.src(false,  'group/manage',        'app.js'  ), Helper.Path.assets.tar('js',  'group/manage')).version()

// group:watch:tbavideo
.sass(Helper.Path.assets.src('sass', 'group/watch/tbavideo/styles', 'app.scss'), Helper.Path.assets.tar('css', 'group/watch/tbavideo')).version()
.js  (Helper.Path.assets.src(false,  'group/watch/tbavideo',        'app.js'  ), Helper.Path.assets.tar('js',  'group/watch/tbavideo')).version()

// management:group:index
.sass(Helper.Path.assets.src('sass', 'management/group/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/group/index')).version()
.js  (Helper.Path.assets.src(false,  'management/group/index',        'app.js'  ), Helper.Path.assets.tar('js',  'management/group/index')).version()
// management:group:info
.sass(Helper.Path.assets.src('sass', 'management/group/info/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/group/info')).version()
.js  (Helper.Path.assets.src(false,  'management/group/info',        'app.js'  ), Helper.Path.assets.tar('js',  'management/group/info')).version()
// management:group:manage
.sass(Helper.Path.assets.src('sass', 'management/group/manage/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/group/manage')).version()
.js  (Helper.Path.assets.src(false,  'management/group/manage',        'app.js'  ), Helper.Path.assets.tar('js',  'management/group/manage')).version()

// management:module:index
.sass(Helper.Path.assets.src('sass', 'management/module/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/module/index')).version()
.js  (Helper.Path.assets.src(false,  'management/module/index',        'app.js'  ), Helper.Path.assets.tar('js',  'management/module/index')).version()
// management:module:info
.sass(Helper.Path.assets.src('sass', 'management/module/info/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/module/info')).version()
.js  (Helper.Path.assets.src(false,  'management/module/info',        'app.js'  ), Helper.Path.assets.tar('js',  'management/module/info')).version()
// management:module:manage
.sass(Helper.Path.assets.src('sass', 'management/module/manage/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/module/manage')).version()
.js  (Helper.Path.assets.src(false,  'management/module/manage',        'app.js'  ), Helper.Path.assets.tar('js',  'management/module/manage')).version()

// management:role:index
.sass(Helper.Path.assets.src('sass', 'management/role/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/role/index')).version()
.js  (Helper.Path.assets.src(false,  'management/role/index',        'app.js'  ), Helper.Path.assets.tar('js',  'management/role/index')).version()
// management:role:info
.sass(Helper.Path.assets.src('sass', 'management/role/info/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/role/info')).version()
.js  (Helper.Path.assets.src(false,  'management/role/info',        'app.js'  ), Helper.Path.assets.tar('js',  'management/role/info')).version()
// management:role:manage
.sass(Helper.Path.assets.src('sass', 'management/role/manage/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/role/manage')).version()
.js  (Helper.Path.assets.src(false,  'management/role/manage',        'app.js'  ), Helper.Path.assets.tar('js',  'management/role/manage')).version()

// management:user:index
.sass(Helper.Path.assets.src('sass', 'management/user/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/user/index')).version()
.js  (Helper.Path.assets.src(false,  'management/user/index',        'app.js'  ), Helper.Path.assets.tar('js',  'management/user/index')).version()
// management:user:info
.sass(Helper.Path.assets.src('sass', 'management/user/info/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/user/info')).version()
.js  (Helper.Path.assets.src(false,  'management/user/info',        'app.js'  ), Helper.Path.assets.tar('js',  'management/user/info')).version()
// management:user:manage
.sass(Helper.Path.assets.src('sass', 'management/user/manage/styles', 'app.scss'), Helper.Path.assets.tar('css', 'management/user/manage')).version()
.js  (Helper.Path.assets.src(false,  'management/user/manage',        'app.js'  ), Helper.Path.assets.tar('js',  'management/user/manage')).version()

// district:tbavideo
.sass(Helper.Path.assets.src('sass', 'district/tbavideo/index/styles', 'app.scss'), Helper.Path.assets.tar('css', 'district/tbavideo/index')).version()
.js  (Helper.Path.assets.src(false,  'district/tbavideo/index',        'app.js'  ), Helper.Path.assets.tar('js',  'district/tbavideo/index')).version()

// app
.sass(Helper.Path.assets.src('sass', 'app/styles', 'common.scss'), Helper.Path.assets.tar('css', 'app')).version()

// base
.sass(Helper.Path.assets.src('sass', 'base', 'base.scss'), 'public/css').version()
.js  (Helper.Path.assets.src('js',   'base', 'base.js'  ), 'public/js' ).version()
.extract(['axios', 'iview', 'lodash', 'vue', 'vue-router', 'vue-select', 'vuex'])
.autoload(config.module.autoload);

/*
 * Layouts
 * */

// common
/*
mix
.sass(Helper.Path.assets.src('sass', 'app/layouts/common', 'main.scss'), Helper.Path.assets.tar('css', 'app/layouts/common'))
.options({processCssUrls: false})
.js  (Helper.Path.assets.src('js',   'app/layouts/common', 'main.js'),   Helper.Path.assets.tar('js',  'app/layouts/common'))
.extract(['vue'])
.autoload(config.module.autoload);
Helper.Process.copyModuleResrc('bootstrap-sass', config.module.resrces['bootstrap-sass'], 'app/layouts/common');
*/
/*
 * Modules
 * */

/*
 * Lib
 * */
mix.scripts([
	'public/lib/chartjs/Chart.js',
	'public/lib/chartjs/Chart.el.group.js',
	'public/lib/chartjs/Chart.linerange.js',
	'public/lib/chartjs/Chart.plugin.linerange.js',
	'public/lib/chartjs/chartjs-plugin-annotation.min.js',
	'public/lib/chartjs/chartjs-plugin-draggable.min.js',
], 'public/lib/chartjs/chart-modified-by-rice.js');
