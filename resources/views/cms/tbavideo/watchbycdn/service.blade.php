<script>

var App = App || {};
App.Service = App.Service || {};
App.Service.Api = App.Service.Api || {};

App.Service.Api.TbaPlayer = (function() {
	
    function service(baseUrl) {
    	this.baseUrl = baseUrl;
    }
    
    // api
    service.prototype.getEvalEventOpts = function (tbaId) {
    	return axios.get(this.baseUrl + 'get-tba-eval-event-opts', {
            params: {
            	tbaId: tbaId,
            }
        }).then(function (data) { console.log('tba-eval-event-opts'); console.log(data.data); return data.data; });
    }
	
    // api
	service.prototype.getSectMap = function (tbaId) {
		return axios.get(this.baseUrl + 'get-tba-video-sect-map', {
            params: {
            	tbaId: tbaId,
            }
        }).then(function (data) { console.log('tba-video-sect-map'); console.log(data.data); return data.data; });
    }
	
	// api
	service.prototype.getAnalEvents = function (tbaId) {
    	return this.getTbaInfo(tbaId, 'AnalEvent');
    }
	
	// api
	service.prototype.getEvalEvents = function (tbaId, type, value) {
		var meta = null;
		switch (type) {
            case 'total':
                type = 'EvalEvent-Total';
                break;
    		case 'self':
    			type = 'EvalEvent-Self';
    			break;
			case 'user':
				type = 'EvalEvent';
				meta = {evalUserId: value};
				break;
		}
		return this.getTbaInfo(tbaId, type, meta);
    }

	// api
	service.prototype.getStatistics = function (tbaId, type) {
    	return this.getTbaInfo(tbaId, type);
    }
	
	//
	service.prototype.getTbaInfo = function (tbaId, type, meta) {
		meta = (typeof meta === 'undefined') ? null : meta
        return axios.get(this.baseUrl + 'get-tba-info', {
            params: {
            	tbaId: tbaId,
            	type : type,
				meta : meta,
            }
        }).then(function (data) { console.log('tba-info:'+type); console.log(data.data); return data.data; });
	}
	
	// api
	service.prototype.getEvalEvent = function (tbaId, eventId, type) {
		return this.getTbaEventInfo(tbaId, eventId, 'EvalEvent');
	}
	
	service.prototype.getTbaEventInfo = function (tbaId, eventId, type) {
		return axios.get(this.baseUrl + 'get-tba-event-info', {
			params: {
				tbaId  : tbaId,
				eventId: eventId,
				type   : type,
			}
		}).then(function (data) { console.log('tba-event-info:'+type); console.log(data.data); return data.data; });
	}
	
	// api
	service.prototype.createEvalEvent = function (tbaId, eventModeId, event) {
		return new Promise(function (resolve, reject) {
			resolve({status: false});
		});
	}
	
    // api
    service.prototype.getVideo = function (tbaId, video, srcType) {
		
		switch (srcType) {
			case 'local':
				return new Promise(function (resolve, reject) {
					resolve({status: true, data: video});
				});
				break;
			
			case 'reference':
				return axios.get(this.baseUrl + 'get-video-info', {
					params: {
						tbaId  : tbaId,
						videoId: video.id,
					}
				}).then(function (data) { console.log('video-info:reference'); console.log(data.data); return data.data; });
				break;
			
			default:
				return new Promise(function (resolve, reject) {
					resolve({status: false});
				});
		}

    }
    
    // api
    service.prototype.setTbaVideoMaps = function (tbaId, maps) {
        
		return axios.put(this.baseUrl + 'set-tba-video-maps', {
    		tbaId: tbaId,
    		maps : maps,
        }).then(function (data) { console.log('tba-video-maps'); console.log(data.data); return data.data; });
    	
    }

    var instance;
    function create() {
    	return new service(App.Service.Api.TbaPlayer.baseUrl);
    }
    
    return {
        getInstance: function () {
            if( !instance ) {
                instance = create();
            }
            return instance;
        },
        baseUrl: null,
    };
    
})();

</script>