import TbaPlayerStore from '../store/tbaplayer'

export default {
	
	register (store, options) {
		
		TbaPlayerStore.state.apiSrv = options.apiSrv
		
		store.registerModule(options.module, TbaPlayerStore)
		
	}
	
}
