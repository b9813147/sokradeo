import Vue      from 'vue'
import VueI18n  from 'vue-i18n'
import en       from 'iview/dist/locale/en-US';
import tw       from 'iview/dist/locale/zh-TW';
import cn       from 'iview/dist/locale/zh-CN';
import customTw from './custom/tw.json';
import customEn from './custom/en.json';
import customCn from './custom/cn.json';
import base     from '../../../app/lang/base'

Vue.use(VueI18n)
Vue.locale = () => {
}

const messages = {
    en: {},
    tw: {},
    cn: {},
}

messages.en = _.assign(en, base.en, messages.en, customEn)
messages.tw = _.assign(tw, base.tw, messages.tw, customTw)
messages.cn = _.assign(cn, base.cn, messages.cn, customCn)

export default new VueI18n({

    locale: document.documentElement.lang,

    messages,

})
