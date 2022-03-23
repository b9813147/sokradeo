export default (() => {

	class Service {

		constructor(baseUrl) {
			this.baseUrl = baseUrl;
			this.hostUrl = window.location.origin + "/";
		}

		getUserInfo() {
			return axios
			  .get(this.hostUrl + "user/tbavideo/get-user-info")
			  .then((data) => data.data);
		}

		getUserDuties() {
			return axios
			  .get(this.hostUrl + "user/tbavideo/get-user-duties")
			  .then((data) => data.data);
		}

		// api
		getEvalEventOpts (tbaId, evaluateOptions = {}) {
			return axios.get(this.baseUrl + 'get-tba-eval-event-opts', {
	            params: {
	            	tbaId          : tbaId,
					evaluateOptions: evaluateOptions
	            }
	        }).then((data) => data.data)
		}

		// api
		getSectMap (tbaId) {
			return axios.get(this.baseUrl + 'get-tba-video-sect-map', {
	            params: {
	            	tbaId: tbaId,
	            }
	        }).then((data) => data.data)
	    }

		// api
		getAnalEvents (tbaId) {
	    	return this.getTbaInfo(tbaId, 'AnalEvent')
	    }

		// api
		getEvalEvents (tbaId, type, value, mode, evaluateOptions = {}) {
			let meta = null
			switch (type) {
                case 'total':
                    type = 'EvalEvent-Total';
                    break;
				case 'self':
					type = 'EvalEvent-Self'
					break
				case 'tbaComment':
					type = 'TbaComment'
					break
				case 'user':
					type = 'EvalEvent'
					meta = {evalUserIds: value, evalModes: mode}
					break
			}
			return this.getTbaInfo(tbaId, type, meta, evaluateOptions)
	    }

		// api
		getStatistics (tbaId, type) {
			return this.getTbaInfo(tbaId, type)
		}

		getTbaInfo (tbaId, type, meta = null, evaluateOptions = {}) {
	        return axios.get(this.baseUrl + 'get-tba-info', {
	            params: {
	            	tbaId          : tbaId,
	            	type           : type,
					meta           : meta,
					evaluateOptions: evaluateOptions
	            }
	        }).then((data) => data.data)
		}

		// api
		getEvalEvent (tbaId, eventId, type) {
			return this.getTbaEventInfo(tbaId, eventId, 'EvalEvent')
		}

		getTbaEventInfo (tbaId, eventId, type) {
			return axios.get(this.baseUrl + 'get-tba-event-info', {
				params: {
					tbaId  : tbaId,
					eventId: eventId,
					type   : type,
				}
			}).then((data) => data.data)
		}

		// api
		createEvalEvent (tbaId, eventModeId, event, file) {
			// return axios.post(this.baseUrl + 'create-tba-eval-event', {
			// 	tbaId      : tbaId,
			// 	eventModeId: eventModeId,
			// 	event      : event,
			// }).then((data) => {
			// 	data = data.data
			// 	if (! data.status) {
			// 		return data
			// 	}
			// 	// 轉換資料
			// 	data.data.time = data.data.time_point
			// 	return data
			// })
			let formData = new FormData();
			formData.append('image', file);
			formData.append('tbaId', tbaId);
			formData.append('eventModeId', eventModeId);
			formData.append('event', JSON.stringify(event));
			return axios({
				method: 'POST',
				url: this.baseUrl + 'create-tba-eval-event',
				data: formData,
				mimeType: 'multipart/form-data'
			}).then((data) => {
				data = data.data;
				if (! data.status) {
					return data
				}
				// 轉換資料
				data.data.time = data.data.time_point;
				return data
			})
		}

		// api
		updateEvalEvent (tbaId, eventId, event, file) {
			let formData = new FormData();
			formData.append('image', file);
			formData.append('tbaId', tbaId);
			formData.append('eventId', eventId);
			formData.append('event', JSON.stringify(event));
			return axios({
				method: 'POST',
				url: this.baseUrl + 'update-tba-eval-event',
				data: formData,
				mimeType: 'multipart/form-data'
			}).then((data) => {
				data = data.data;
				if (! data.status) {
					return data
				}
				// 轉換資料
				data.data.time = data.data.time_point;
				return data
			})
		}

		// api
		deleteEvalEvent (tbaId, eventId) {
			return axios.delete(this.baseUrl + 'delete-tba-eval-event', {
				data: {
					tbaId  : tbaId,
					eventId: eventId,
				}
			}).then((data) => data.data)
		}
		
		// Create comment
		createTbaComment (commentData, fileData) {
			let formData = new FormData();
			formData.append('commentData', JSON.stringify(commentData));
			formData.append('fileData', fileData);
			
			return axios({
				method: 'POST',
				url: this.hostUrl + 'comments',
				data: formData,
				mimeType: 'multipart/form-data'
			}).then((data) => data.data);
		}

		// Update comment
		updateTbaComment (tbaId, commentData, fileData) {
			let formData = new FormData();
			formData.append('_method', 'PUT');
			formData.append('commentData', JSON.stringify(commentData));
			formData.append('fileData', fileData);
			
			return axios({
				method: 'POST',
				url: this.hostUrl + 'comments/' + tbaId,
				data: formData,
				mimeType: 'multipart/form-data'
			}).then((data) => data.data);
		}

		// delete comments
		deleteTbaComment (tbaId, commentId) {
			return axios.delete(this.hostUrl + 'comments/' + tbaId, {
				data: {
					commentId: commentId,
				}
			}).then((data) => data.data)
		}

		// Get comment tag types for selection
		getTbaCommentTagTypes(groupId) {
			let url = groupId
				? this.hostUrl + 'comment-tag-types/' + groupId
				: this.hostUrl + 'comment-tag-types';
			return axios.get(url).then((data) => data.data);
		}

	    // api
	    getVideo (tbaId, video, srcType) {

			switch (srcType) {
				case 'local':
					return new Promise((resolve, reject) => {
						resolve({status: true, data: video})
					})
					break

				case 'reference':
					return axios.get(this.baseUrl + 'get-video-info', {
						params: {
							tbaId  : tbaId,
							videoId: video.id,
						}
					}).then((data) => data.data)
					break

				default:
					return new Promise((resolve, reject) => {
						resolve({status: false})
					})
			}

	    }

		// api
		setTbaVideoMaps (tbaId, maps) {

			return axios.put(this.baseUrl + 'set-tba-video-maps', {
				tbaId: tbaId,
				maps : maps,
            }).then((data) => data.data)

		}

	}

	return {

		baseUrl: null,

		getInstance() {

			if (this.baseUrl === null) {
				console.log('error')
				return
			}

			if (!Service.instance) {
				Service.instance = new Service(this.baseUrl)
			}
			return Service.instance
		}

	}

})()
