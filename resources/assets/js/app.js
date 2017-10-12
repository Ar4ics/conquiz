
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue           from 'vue'
import Notifications from 'vue-notification'

Vue.use(Notifications);
//window.Bus = new Vue();
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('games', require('./components/Games.vue'));
Vue.component('game', require('./components/Game.vue'));
Vue.component('create-game', require('./components/CreateGame.vue'));

new Vue({
    el: '#app'
});

