import Vue     from 'vue'
import VueI18n from 'vue-i18n'
import en      from 'iview/dist/locale/en-US'
import tw      from 'iview/dist/locale/zh-TW'
import cn      from 'iview/dist/locale/zh-CN'

Vue.use(VueI18n)
Vue.locale = () => {}

const messages = {

	en: _.assign({}, en),
	tw: _.assign({}, tw),
	cn: _.assign({}, cn),

}

export default new VueI18n({

	locale: document.documentElement.lang,

	messages,

})
