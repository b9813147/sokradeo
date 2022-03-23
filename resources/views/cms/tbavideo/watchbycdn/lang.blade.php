<script>

Vue.locale = function () {};

var App = App || {};
App.Lang = App.Lang || {};

App.Lang.main = new VueI18n.default({

	locale: document.documentElement.lang,

	messages: {

		en: _.assign({}, iview.langs['en-US']),
		tw: _.assign({}, iview.langs['zh-TW']),
		cn: _.assign({}, iview.langs['zh-CN']),

	},

});

</script>
