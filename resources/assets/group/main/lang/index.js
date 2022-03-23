import Vue     from 'vue'
import VueI18n from 'vue-i18n'
import en      from 'iview/dist/locale/en-US';
import tw      from 'iview/dist/locale/zh-TW';
import cn      from 'iview/dist/locale/zh-CN';
import base    from '../../app/lang/base'
import group   from '../../app/lang/group'

Vue.use(VueI18n)
Vue.locale = () => {}

const messages = {
	en: {},
	tw: {},
	cn: {},
}

messages.en = _.assign(en, base.en, group.en, messages.en)
messages.tw = _.assign(tw, base.tw, group.tw, messages.tw)
messages.cn = _.assign(cn, base.cn, group.cn, messages.cn)

export default new VueI18n({

	locale: document.documentElement.lang,

	messages,

})
