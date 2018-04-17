import "babel-polyfill";

require('./bootstrap');

window.Vue = require('vue');

require('./components');

const app = new Vue({
    el: '#app'
});
