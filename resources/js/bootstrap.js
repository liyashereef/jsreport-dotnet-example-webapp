window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

 import Echo from 'laravel-echo';

 window.Pusher = require('pusher-js');

 window.Echo = new Echo({
     broadcaster: 'pusher',
     key: 'local',
     wsHost: window.location.hostname,
     wsPort: 6001,
     forceTLS: false,
     disableStats: true,
 });

 Pusher.logToConsole = true;

require('@uppy/core/dist/style.min.css')
require('@uppy/drag-drop/dist/style.min.css')
require('@uppy/status-bar/dist/style.min.css')

import Uppy from '@uppy/core'
import DragDrop from '@uppy/drag-drop'
import StatusBar from '@uppy/status-bar'
import AwsS3Multipart from '@uppy/aws-s3-multipart'

window.Uppy = Uppy
window.DragDrop = DragDrop
window.StatusBar = StatusBar
window.AwsS3Multipart = AwsS3Multipart