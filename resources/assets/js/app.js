
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue           from 'vue'
import Notifications from 'vue-notification'

import Game from './components/Game'
import Games from './components/Games'
import CreateGame from './components/CreateGame'
import ChatMessage from './components/ChatMessage'
import ChatMessages from './components/ChatMessages'

window.Vue = Vue;
Vue.use(Notifications);
window.Bus = new Vue();
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('games', Games);
Vue.component('game', Game);
Vue.component('create-game', CreateGame);
Vue.component('chat-message', ChatMessage);
Vue.component('chat-messages', ChatMessages);

const app = new Vue({
    el: '#app'
});

