import Vue     from 'vue'
import VueI18n from 'vue-i18n'
import en      from 'iview/dist/locale/en-US';
import tw      from 'iview/dist/locale/zh-TW';
import cn      from 'iview/dist/locale/zh-CN';
import list    from '../../../app/lang/list'

Vue.use(VueI18n)
Vue.locale = () => {}

list.en = _.assign(list.en, en)
list.tw = _.assign(list.tw, tw)
list.cn = _.assign(list.cn, cn)

const messages = list

export default new VueI18n({

	locale: document.documentElement.lang,

	messages,

})
