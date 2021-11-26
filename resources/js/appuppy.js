/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('alpinejs');
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