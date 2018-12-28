/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue'
import vSelect from 'vue-select'
import Notifications from 'vue-notification'
import VueAxios from 'vue-axios'
import VueAuth from '@websanova/vue-auth'
import axios from 'axios'

import store from "./store";
import router from "./router";

import Game from './components/Game'
import Games from './components/Games'
import CreateGame from './components/CreateGame'
import ChatMessage from './components/ChatMessage'
import ChatMessages from './components/ChatMessages'
import GameInfo from './components/GameInfo'
import GameUsers from './components/GameUsers'
import GameField from './components/GameField'
import GameQuestion from './components/GameQuestion'
import GameMove from './components/GameMove'
import GameResults from './components/GameResults'
import App from './views/App'


require('./bootstrap');

Vue.use(Notifications);
Vue.use(VueAxios, axios);
Vue.axios.defaults.baseURL = '/api';
Vue.router = router;

Vue.use(VueAuth, {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    googleOauth2Data: {
        url: 'https://accounts.google.com/o/oauth2/auth',
        params: {
            redirect_uri: function () {
                return this.options.getUrl() + '/login';
            },
            client_id: '1080513381768-bo5sbcs0n4qm6802ge5b3qansqdcjmlv.apps.googleusercontent.com',
            scope: `openid email profile https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email`
        }
    },
});

window.Vue = Vue;
window.Store = store;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('v-select', vSelect);
Vue.component('games', Games);
Vue.component('game', Game);
Vue.component('create-game', CreateGame);
Vue.component('chat-message', ChatMessage);
Vue.component('chat-messages', ChatMessages);
Vue.component('game-info', GameInfo);
Vue.component('game-users', GameUsers);
Vue.component('game-field', GameField);
Vue.component('game-question', GameQuestion);
Vue.component('game-move', GameMove);
Vue.component('game-results', GameResults);

const app = new Vue({
    el: '#app',
    components: {App},
    store,
    router,
    watch: {
        isAuthenticated() {
            Echo.connector.pusher.config.auth.headers['Authorization'] = 'Bearer ' + this.$auth.token();
            Echo.connector.options.auth.headers['Authorization'] = 'Bearer ' + this.$auth.token();
        }
    },
    computed: {
        isAuthenticated() {
            return this.$auth.check();
        },
    }
});

