/*
try {
	window.$ = window.jQuery = require('jquery')

	require('bootstrap-sass')
} catch (e) {

}
*/

import Echo from "laravel-echo";
/*使用 socket.io-client*/
window.io = require('socket.io-client');
try {
  // window.Echo = new Echo({
  //   broadcaster: 'socket.io',
  //   host       : window.location.hostname + ':6001'
  // });

  // lodash
  window._ = require('lodash')

  // axios
  window.axios = require('axios')
  window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
  let token = document.head.querySelector('meta[name="csrf-token"]')
  if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
  } else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
  }

} catch (e) {

}
