/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import _ from 'lodash';
import Vue from 'vue'
import Vuex from 'vuex';
import vSelect from 'vue-select'
import Notifications from 'vue-notification'
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
import VueRouter from 'vue-router'

import App from './views/App'
import VueAxios from 'vue-axios'
import VueAuth from '@websanova/vue-auth'
import axios from 'axios'
import Login from './views/Login'
import Register from './views/Register'
import NotFound from './views/NotFound';
import GamesView from './views/GamesView';
import GameView from "./views/GameView";

require('./bootstrap');

Vue.use(VueAxios, axios);
Vue.axios.defaults.baseURL = '/api';
Vue.use(VueRouter);
Vue.component('v-select', vSelect);

Vue.use(Vuex);
Vue.use(Notifications);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            redirect: '/games'
        },
        {
            path: '/games',
            name: 'games',
            component: GamesView,
            meta: {
                auth: true,
                title: 'Игры'
            }
        },
        {
            path: '/games/:id',
            name: 'game',
            component: GameView,
            meta: {
                auth: true,
                title: 'Игра'
            }
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
                auth: false,
                title: 'Авторизация'
            }
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
            meta: {
                auth: false,
                title: 'Регистрация'
            }
        },
        {
            path: '/404',
            component: NotFound,
            meta: {
                title: '404 Не найдено'
            }
        },
        {path: '*', redirect: '/404'},
    ],
});
router.beforeEach((to, from, next) => {
    document.title = to.meta.title;

    next()
});
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

const store = new Vuex.Store({
    state: {
        user_colors: [],
        online_users: [],
        messages: [],
        competitive_box: null,
        game: null,
        player: null,
        winner: null,
        question: null,
        competitive_question: null,
        who_moves: null,
        field: null,
        results: null
    },
    getters: {
        competition: state => {
            if (state.game && state.competitive_box) {
                const cs = state.competitive_box.competitors;

                if (cs.length === 0) {
                    return 'Разделение территории';
                }

                let c1 = state.user_colors.find(u => u.id === cs[0]);
                let c2 = state.user_colors.find(u => u.id === cs[1]);
                if (c1 && c2) {
                    return `${c1.user.name} нападает на ${c2.user.name}`;
                }
            }
            return 'Захват территории';
        },

        is_question_exists: state => {
            return state.question || state.competitive_question
        },

        sorted_messages: state => {
            return _.groupBy(state.messages, 'date');
        }
    },
    mutations: {

        setGameMessages(state, messages) {
            state.messages = messages;
        },

        addGameMessage(state, message) {
            state.messages.push(message);
        },

        setGame(state, game) {
            state.game = game;
            state.game.count_col = 12 / state.game.count_x;
        },

        setPlayer(state, player) {
            state.player = player;
        },

        setField(state, field) {
            if (!state.game) {
                throw new Error('game is null');
            }
            state.field = field;
        },

        setCompetitiveBox(state, cb) {
            Vue.set(state, 'competitive_box', cb);
            if (cb) {
                state.field[cb.y][cb.x].color = 'LightGrey';
            }
        },

        setUserColors(state, user_colors) {
            Vue.set(state, 'user_colors', user_colors);
        },

        setBase(state, base) {
            state.field[base.y].splice(base.x, 1, base);
        },

        setQuestion(state, question) {
            if (question) {
                if (!question.answers) {
                    state.competitive_question = question;
                } else {
                    state.question = question;
                }
            }
        },

        setWhoMoves(state, who_moves) {
            state.who_moves = who_moves;
        },
        setWinner(state, winner) {
            state.winner = winner;
        },
        setOnlineUsers(state, online_users) {
            state.online_users = online_users;
        },
        addOnlineUser(state, user) {
            state.online_users.push(user);
        },
        removeOfflineUser(state, user) {
            state.online_users = state.online_users.filter(o => o.id !== user.id);
        },

        setAnswers(state, payload) {
            let user_answers = [];

            payload.results.forEach(r => {
                user_answers.push(r);
            });
            state.results = {};
            state.results.user_answers = user_answers;
            state.results.correct = payload.correct;
            payload.deleted_boxes.forEach(b => {
                state.field[b.y].splice(b.x, 1,
                    {x: b.x, y: b.y, cost: 0, base: null, color: 'white', user_color_id: null});
            });
        },

        setCompetitiveCorrectAnswers(state, payload) {
            let user_answers = [];

            payload.results.forEach(r => {
                user_answers.push(r);
            });
            state.results = {};
            state.results.user_answers = user_answers;
            state.results.correct = payload.correct;
            state.results.is_exact = false;
        },

        setCompetitiveAnswers(state, payload) {
            let user_answers = [];

            payload.results.forEach(r => {
                user_answers.push(r);
            });
            state.results = {};
            state.results.user_answers = user_answers;
            state.results.correct = payload.correct;
            state.results.is_exact = payload.is_exact;
            const cb = payload.result_box;
            state.field[cb.y].splice(cb.x, 1, cb);
            const winner = payload.winner;
            if (winner) {
                state.results.winner = winner;
                if (cb.hasOwnProperty('loss_user_color_id')) {
                    for (let i = 0; i < state.game.count_y; i++) {
                        for (let k = 0; k < state.game.count_x; k++) {
                            let box = state.field[i][k];
                            if (box.user_color_id === cb.loss_user_color_id) {
                                box.color = winner.color;
                            }
                        }
                    }
                }
            }
        },

        clearQuestion(state) {
            state.question = null;
        },

        clearCompetitiveQuestion(state) {
            state.competitive_question = null;
        },

        clearResults(state) {
            state.results = null;
        },

        clearCompetitiveBox(state) {
            Vue.set(state, 'competitive_box', null);
        },

        replaceBox(state, box) {
            if (!state.field) {
                throw new Error('boxes is null');
            }
            state.field[box.y].splice(box.x, 1, box);
        },
    }
});

window.Vue = Vue;
window.Store = store;
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
Vue.component('game-info', GameInfo);
Vue.component('game-users', GameUsers);
Vue.component('game-field', GameField);
Vue.component('game-question', GameQuestion);
Vue.component('game-move', GameMove);
Vue.component('game-results', GameResults);

Vue.component('games-view', GamesView);
Vue.component('game-view', GameView);

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

