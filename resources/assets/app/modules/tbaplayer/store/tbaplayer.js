import _ from "lodash"

export default {

	namespaced: true,

    state: {
		// app
		windowWidth		: window.innerWidth,
		windowHeight	: window.innerHeight,
		mode            : 'general',
		info            : {name: '', description: '', playlisted: 0}, // 必要預設屬性
    	apiSrv          : null,
		msgInfo         : null,
		// playlist
		playlist        : [],
		currPlay        : null, // 0 base
		// tba-video
		preTbaId        : null,
		tba             : {id: null, name: '', description: ''}, // 必要預設屬性
		videos          : [],
		videoSrcType    : null,
		// player
		flushTime       : 0,
		fragChecked     : false,
		sectMap         : null,
		eventRange      : {min: 0, max: 3600},
        analEvents      : null,
        evalEvents      : null,
		evalEventsHandle: null,
		video           : null,
		videoRange		: {min: 0, max: 0},
        tbaTime         : 0,
		track           : null, // 0 base
		seekTime        : 0,
		seekTimeLock    : false, // 暫時無用
		paused          : false,
		statusTime      : 0, // video player status time for update
		gallery         : {images: [], index: null},
		block_r			: 'commentlist',
		narrowScreen	: false,
		// Comment
		commentTagTypesList : [],
		comments			: [],
		participants		: [],
		commentRange		: {min: 0, max: 3600},
		// User Identities
		userInfo			   : null,
		privilegedUserTypeList : ["Expert", "Visitor", "Teacher"],
		userDuties			   : [],
		// File Setting
		fileSetting: {
			extList: {
				'img': ['jpg', 'jpeg', 'png'],
				'audio': ['mp3', 'wav'],
				'video': ['mp4', 'webm', 'mov', 'm4a'],
			},
			sizeLimit: 52428800, // 50MB
		},
		// Display allowed
		displayAllowed: {
			commentlist: true,
			statistics: true,
		},
    },

	getters: {

		allowedExtList: (state) => {
			let imgExtList = state.fileSetting.extList.img;
			let audioExtList = state.fileSetting.extList.audio;
			let videoExtList = state.fileSetting.extList.video;
			return [].concat(imgExtList).concat(audioExtList).concat(videoExtList);
		},

		isAdmin: (state) => {
			return _.find(state.userDuties, (duty) => {
				return duty.member_duty === 'Admin';
			}) !== undefined;
		},

		isVideoOwner: (state) => {
			return state.userInfo && state.userInfo.id === state.tba.user_id;
		},
	},

    mutations: {

		setMode (state, data) {
			switch (data) {
				case 'general':
				case 'edit':
					break
				default:
					return
			}
			state.mode = data
			state.fragChecked = false
        },

		setUserInfo (state, data) {
			state.userInfo = data
		},

		setUserDuties (state, data) {
			state.userDuties = data
		},

		setInfo (state, data) {
			state.info = data
		},

		setMsgInfo (state, data) {
			state.msgInfo = data
		},

		setPlaylist (state, data) {
			state.playlist = data
		},

		setCurrPlay (state, data) {
			if (data >= state.playlist.length) {
				return
			}
			state.currPlay = data
		},

		setTbaVideo (state, data) {
			state.preTbaId     = state.tba.id
			state.tba          = data.tba
			state.videos       = data.videos
			state.videoSrcType = data.videoSrcType
			state.fragChecked  = !_.isNil(data.tba.frag)
		},

		setFlushTime (state, data) {
			state.flushTime = data
		},

		setFragChecked (state, data) {
			state.fragChecked = data
		},

		setSectMap (state, data) {
        	state.sectMap = data
        },

		setVideoRange (state, data) {
			state.videoRange = data
		},

		setEventRange (state, data) {
        	state.eventRange = data
        },

        setAnalEvents (state, data) {
        	state.analEvents = data
        },

        setEvalEvents (state, data) {
        	state.evalEvents = data
        },

		setEvalEventsHandle (state, data) {
			state.evalEventsHandle = data
		},

        setVideo (state, data) {
        	state.video = data
        },

		setTbaTime (state, data) {
			state.tbaTime = data
		},

		setNarrowScreen (state, data) {
			state.narrowScreen = data
		},

		setTrack (state, data) {
			state.track = data
		},

		setSeekTime (state, data) {
			state.seekTime = data
		},

		setSeekTimeLock (state, data) {
			state.seekTimeLock = data
		},

		setPaused (state, data) {
			state.paused     = data
			state.statusTime = Date.now() / 1000
		},

		setAppointedTime (state, data) {
			state.appointedTime = data
		},

		setBlockR (state, data) {
			state.block_r = data
		},

		setCommentTagTypesList (state, data) {
			state.commentTagTypesList = data;
		},

		setComments (state, data) {
			state.comments = data
		},

		setParticipants (state, data) {
			state.participants = data
		},

		setCommentRange (state, data) {
			state.commentRange = data
		},

		setEvaluateOptions (state, data) {
			state.evaluateOptions = data
		},

		setDisplayAllowed (state, data) {
			state.displayAllowed[data.key] = data.val;
		},

	},

    actions: {

		info (ctx, data) {
			ctx.commit('setInfo',     data.info)
			ctx.commit('setPlaylist', data.playlist)
			ctx.commit('setAppointedTime', data.appointedTime)
			ctx.commit('setEvaluateOptions', data.evaluateOptions)
		},

		init (ctx) {
			ctx.commit('setCurrPlay', 0)
		},

        initPlayer (ctx) {
			let vue          = this._vm
        	let tbaId        = ctx.state.tba.id
            let video        = ctx.state.videos.length === 0 ? null : ctx.state.videos[0]
			let videoSrcType = ctx.state.videoSrcType
            let apiSrv       = ctx.state.apiSrv

			let promises = []
			let promise  = null

			// Get User Info
			promise = apiSrv.getInstance().getUserInfo().then((res) => {
				if(!res.status) return false;
				ctx.commit('setUserInfo', res.data)
				return true;
			});
			promises.push(promise);

			// Get user duties
			promise = apiSrv.getInstance().getUserDuties().then((res) => {
				if(!res.status) return false;
				ctx.commit('setUserDuties', res.data)
				return true;
			});
			promises.push(promise);

			promise = apiSrv.getInstance().getSectMap(tbaId).then((data) => {
				if (!data.status) return false

				// Set up sectMap
				ctx.commit('setSectMap', data.data)

				// Set up videoRange
				let sectMapRange = _.first(ctx.state.sectMap).range || null
				if (sectMapRange) ctx.commit('setVideoRange', sectMapRange)

				return true
			})
			promises.push(promise)

			promise = apiSrv.getInstance().getAnalEvents(tbaId).then((data) => {
				if(! data.status) {
					return false
				}
				ctx.commit('setEventRange', data.data.range)
				ctx.commit('setAnalEvents', data.data)
				return true
			})
			promises.push(promise)
/* 註解:因為支援片段播放 故由anal event chart決定影片初始化
			if (video !== null && videoSrcType !== null) {
				promise = apiSrv.getInstance().getVideo(tbaId, video, videoSrcType).then((data) => {
	            	if(! data.status) {
						return false
					}
					ctx.commit('setVideo', data.data)
					return true
				})
				promises.push(promise)
			}
*/
			vue.$Spin.show()
			Promise.all(promises).then((datas) => {
				vue.$Spin.hide()
				//console.log('Spinshow', datas)
            })
        },

		setUserInfo (ctx, data) {
			ctx.commit('setUserInfo', data)
		},

		setUserDuties (ctx, data) {
			ctx.commit('setUserDuties', data)
		},

		setBlockR(ctx, data) {
			ctx.commit('setBlockR', data)
		},

		setMode (ctx, data) {
			ctx.commit('setMode', data)
		},

		setMsgInfo (ctx, data) {
			ctx.commit('setMsgInfo', data)
		},

		setCurrPlay (ctx, data) {
			ctx.commit('setCurrPlay', data)
		},

		nextCurrPlay (ctx) {
			ctx.commit('setCurrPlay', ctx.state.currPlay + 1)
		},

		setTbaVideo (ctx, data) {
			ctx.commit('setTbaVideo', data)
		},

		flushPlayer (ctx) {
			ctx.commit('setFlushTime', Date.now())
		},

		updateComments(ctx, data = {}) {
			let tbaId  = ctx.state.tba.id;
			let apiSrv = ctx.state.apiSrv;
			let evaluateOptions = data.hasOwnProperty('evaluateOptions') ? data.evaluateOptions : {};
			let eventType = data.hasOwnProperty('eventType') ? data.eventType : 'tbaComment';
			
			apiSrv.getInstance().getEvalEvents(tbaId, eventType, null, null, evaluateOptions).then((evaldata) => {
				if(! evaldata.status) {
					console.log('allEventfromcommentlisterror', tbaId);
					return
				}
				// Assign comment data to state
				let _comments = [];
				let _participants = [];
				let _commentRange = ctx.state.commentRange;
				switch (eventType) {
					case 'tbaComment':
						// New comment structure
						let commentDataList = evaldata.data;
						// Get comments
						_comments = _.sortBy(
							_.map(commentDataList, (item) => {
								let userId =
									item.user && _.isObject(item.user) ? item.user.id : "";
								let name =
									item.user && _.isObject(item.user)
									? item.user.name
									: item.nick_name;
								return {
									id: item.id,
									userId: userId,
									name: name,
									time: item.time,
									type: item.type,
									tag: item.tag,
									isPositive: item.is_positive,
									text: item.text,
									attachment: item.attachment,
								};
							}),
							["time"]
						);
						// Fet participants
						_participants = _.map(commentDataList, (item) => {
							let name =
							item.user && _.isObject(item.user)
								? item.user.name
								: item.nick_name;
							return {
								name: name,
								times: 0,
							};
						});
						// Get comment range (min, max) time
						_commentRange = {
							min:
							  _comments.length > 0
								? _.minBy(_comments, "time").time
								: ctx.state.commentRange.min,
							max:
							  _comments.length > 0
								? _.maxBy(_comments, "time").time
								: ctx.state.commentRange.max,
						  };
						break;
					default:
						// Old comment structure (written by Chad)
						for(var i=0;i<evaldata.data.length;i++) {
							var counter=0;
							for(var j=0;j<evaldata.data[i].datasets[0].eventtexts.length;j++) {
								for(var k=0;k<evaldata.data[i].datasets[0].eventtexts[j].length;k++) {
									var item    = { time:'', name:'', tag:'', text:'', img:'', media:'', url:'', id:'', userid:'' };
									item.id     = evaldata.data[i].datasets[0].ids[j][k];
									item.userid = evaldata.data[i].user.userid;
									item.name   = evaldata.data[i].user.name;
									item.time   = evaldata.data[i].datasets[0].details[j][k];
									item.mode   = evaldata.data[i].datasets[0].labelsmodeid[j][k];
									item.tag    = evaldata.data[i].datasets[0].labelsmode[j][k];
									item.text   = evaldata.data[i].datasets[0].eventtexts[j][k];
									item.img    = evaldata.data[i].datasets[0].eventimgs[j][k];                        
									item.media  = evaldata.data[i].datasets[0].eventmedia[j][k];                        
									_comments.push(item);
									counter++;
								}
							}
							var pitem = {name:'', times:0};
							pitem.name=evaldata.data[i].user.name;
							pitem.times=counter;
							_participants.push(pitem)
						}
						_comments = _comments.sort(function (a, b) {
							return a.time > b.time ? 1 : -1;
						});
						_participants = _participants.sort(function (a, b) {
							return a.times < b.times ? 1 : -1;
						});
						break;
				}
				ctx.commit('setComments', _comments);
				ctx.commit('setParticipants', _participants);
				ctx.commit('setCommentRange', _commentRange);
			})
		},

		getEvalEvents (ctx, data) {
			if (data === null) {
				ctx.commit('setEvalEvents', { labels: [], range: ctx.state.eventRange, datasets: [] })
				return true
			}

			let tbaId  = ctx.state.tba.id
			let apiSrv = ctx.state.apiSrv
			//console.log('apigeteval',tbaId, data.type, data.value)
			apiSrv.getInstance().getEvalEvents(tbaId, data.type, data.value, data.mode, data.evaluateOptions).then((data) => {
            	if(! data.status) {
					return false
				}
				ctx.commit('setEvalEvents', data.data)
				return true
			})
		},

		setEvalEvents (ctx, data = null) {
			data = (data === null) ? { labels: [], range: ctx.state.eventRange, datasets: [] } : data
			ctx.commit('setEvalEvents', data)
		},

		deleteEvalEvent (ctx, eventId) {
			let tbaId  = ctx.state.tba.id
			let apiSrv = ctx.state.apiSrv
			apiSrv.getInstance().deleteEvalEvent(tbaId, eventId).then((data) => {
				let msgInfo = (data.status)
					? {type: 'success', value: 'act.update.suce'}
					: {type: 'error',   value: 'act.update.fail'}
				ctx.commit('setMsgInfo', msgInfo)

				if(! data.status) {
					return
				}

				let found = false
				let found_evalEventIdx = 0
				_.forEach(ctx.state.evalEvents, (evalEvent, evalEventIdx) => {
                    _.forEach(evalEvent.datasets, (dataset, datasetIdx) => {
                        _.forEach(dataset.ids, (row, rowIdx) => {
                            let itemIdx = _.indexOf(row, eventId)
                            if (itemIdx === -1) {
                                return true
                            }
                            dataset.ids[rowIdx].splice(itemIdx, 1)
                            dataset.details[rowIdx].splice(itemIdx, 1)
                            if (_.isArray(dataset.colors[rowIdx])) {
                                dataset.colors[rowIdx].splice(itemIdx, 1)
                            }
                            found = true
                            found_evalEventIdx = evalEventIdx
                            return false
                        })
                        return !found
                    })
                })

				if (found) {
					// ctx.commit('setEvalEvents', _.clone(ctx.state.evalEvents[found_evalEventIdx]))
					ctx.commit('setEvalEventsHandle', {
						act : 'delete-event',
						meta: {id: eventId},
					})
				}
			})
		},

		createTbaComment (ctx, commentData, fileData) {
			let apiSrv = ctx.state.apiSrv;
			apiSrv
				.getInstance()
				.createTbaComment(commentData, fileData)
				.then((res) => {
					let msgInfo = (res.status)
						? {type: 'success', value: 'act.update.suce'}
						: {type: 'error',   value: 'act.update.fail'}
					ctx.commit('setMsgInfo', msgInfo)
				});
		},

		updateTbaComment (ctx, commentData, fileData) {
			let tbaId = ctx.state.tba.id;
			let apiSrv = ctx.state.apiSrv;
			apiSrv
				.getInstance()
				.updateTbaComment(tbaId, commentData, fileData)
				.then((res) => {
					let msgInfo = (res.status)
						? {type: 'success', value: 'act.update.suce'}
						: {type: 'error',   value: 'act.update.fail'}
					ctx.commit('setMsgInfo', msgInfo)
				});
		},

		deleteTbaComment (ctx, commentId) {
			let tbaId = ctx.state.tba.id;
			let apiSrv = ctx.state.apiSrv;
			apiSrv
				.getInstance()
				.deleteTbaComment(tbaId, commentId)
				.then((res) => {
					let msgInfo = (res.status)
						? {type: 'success', value: 'act.update.suce'}
						: {type: 'error',   value: 'act.update.fail'}
					ctx.commit('setMsgInfo', msgInfo)
				});
		},

		getTbaCommentTagTypes (ctx, groupId) {
			let apiSrv = ctx.state.apiSrv;
			apiSrv
				.getInstance()
				.getTbaCommentTagTypes(groupId)
				.then((res) => {
					if (!res.status) return;
					let data = res.data;
					ctx.commit('setCommentTagTypesList', data)
				});
		},

		setTbaTime (ctx, data) {
			ctx.commit('setTbaTime', data)
		},

		setNarrowScreen (ctx, data) {
			ctx.commit('setNarrowScreen', data)
		},

		setTrackInfo (ctx, info) {

			let currPlay     = ctx.state.currPlay
			let tbaId        = ctx.state.tba.id
			let videos       = ctx.state.videos
			let videoSrcType = ctx.state.videoSrcType

			if (info === null || info.track >= videos.length) {
				ctx.commit('setCurrPlay', currPlay + 1)
				return
			}

            info.forceUpdated = _.isUndefined(info.forceUpdated) ? false : info.forceUpdated

			if (info.track === ctx.state.track && !info.forceUpdated) {
				ctx.commit('setFragChecked', false    )
				ctx.commit('setSeekTime',    info.time)
			} else {
				ctx.state.apiSrv.getInstance().getVideo(tbaId, videos[info.track], videoSrcType).then((data) => {
	            	if(! data.status) {
						return
					}
					ctx.commit('setVideo',    data.data )
					ctx.commit('setTrack',    info.track)
	            	ctx.commit('setSeekTime', info.time )
				})
			}
		},

		setPaused (ctx, data) {
			ctx.commit('setPaused', data)
		},

		shiftTbaVideoMap (ctx, info) {
			let vue     = this._vm
			let tbaId   = ctx.state.tba.id
			let apiSrv  = ctx.state.apiSrv
			let sectMap = ctx.state.sectMap
			let track   = ctx.state.track

			let maps = []
			sectMap[track].sects.forEach((v) => {
				maps.push({
					id       : v.id,
					tba_start: v.tba_start + info.offset,
					tba_end  : v.tba_end   + info.offset,
				})
			})

			vue.$Spin.show()
			apiSrv.getInstance().setTbaVideoMaps(tbaId, maps).then((data) => {
				let msgInfo = (data.status)
					? {type: 'success', value: 'act.update.suce'}
					: {type: 'error',   value: 'act.update.fail'}
				ctx.commit('setMsgInfo', msgInfo)

				// 不管正確與否均須更新時間線
				apiSrv.getInstance().getSectMap(tbaId).then((data) => {
					vue.$Spin.hide()
					if(! data.status) {
						return
					}
					ctx.commit('setSectMap', data.data)
				})
			})
		},

		reloadTbaAnalChart (ctx, callback = () => {}) {
            let tbaId   = ctx.state.tba.id
            let apiSrv  = ctx.state.apiSrv

            apiSrv.getInstance().getAnalEvents(tbaId).then((data) => {
                if(! data.status) {
                    return false
                }
                ctx.commit('setEventRange', data.data.range)
                ctx.commit('setAnalEvents', data.data)
                callback()
            })
		},

		setEvaluateOptions(ctx, data) {
			console.log(data);
			ctx.commit('setEvaluateOptions', data)
		},

		setDisplayAllowed(ctx, data) {
			if (ctx.state.displayAllowed.hasOwnProperty(data.key)) {
				ctx.commit('setDisplayAllowed', data)
			}
		},

    },

}
