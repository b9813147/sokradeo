require('./bootstrap')

import TbaPlayerHelper from './helpers/tbaplayer'
import CpntTbaPlayer   from './components/tbaplayer.vue'

window.Component = window.Component || {}
window.Component.TbaPlayer = window.Component.TbaPlayer || {}
window.Component.TbaPlayer.helper = TbaPlayerHelper
window.Component.TbaPlayer.main   = CpntTbaPlayer
