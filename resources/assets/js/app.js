/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue'
import Vuex from 'vuex';
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
import GameExactQuestion from './components/GameExactQuestion'
import GameMove from './components/GameMove'
import GameResults from './components/GameResults'

Vue.use(Vuex);
Vue.use(Notifications);

const store = new Vuex.Store({
    state: {
        user_colors: [],
        competitors: [],
        online_users: [],
        game: null,
        player: null,
        winner: null,
        question: null,
        exact_question: null,
        who_moves: null,
        field: null,
        results: null
    },
    getters: {
        competition: state => {
            if (state.game && state.competitors && state.competitors.length > 0) {
                const cs = state.competitors;
                let c1 = state.user_colors.find(u => u.id === cs[0]);
                let c2 = state.user_colors.find(u => u.id === cs[1]);
                if (c1 && c2) {
                    return `${c1.user.name} нападает на ${c2.user.name}`;
                }
            }
            return null;
        },
        question_asked: state => {
            return state.question || state.exact_question;
        }
    },
    mutations: {
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

        setCompetitors(state, competitors) {
            Vue.set(state, 'competitors', competitors);
        },

        setUserColors(state, user_colors) {
            Vue.set(state, 'user_colors', user_colors);
        },

        setCompetitiveBoxColor(state, payload) {
            const cb = payload.competitive_box;
            state.field[cb.y][cb.x].color = 'grey';
        },

        setBase(state, base) {
            state.field[base.y].splice(base.x, 1, base);
        },
        setQuestion(state, question) {
            state.question = question;
        },
        setExactQuestion(state, exact_question) {
            state.exact_question = exact_question;
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
                    {x: b.x, y: b.y, cost: 0, base_guards_count: 0, color: 'white', user_color_id: 0});
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
        },

        setCompetitiveAnswers(state, payload) {
            let user_answers = [];

            payload.results.forEach(r => {
                user_answers.push(r);
            });
            state.results = {};
            state.results.user_answers = user_answers;
            state.results.correct = payload.correct;
            Vue.set(state, 'competitors', []);
            const cb = payload.result_box;
            state.field[cb.y].splice(cb.x, 1, cb);
            const winner = payload.winner;
            if (winner) {
                state.results.winner = winner;
            }
        },

        clearQuestion(state) {
            state.question = null;
        },

        clearExactQuestion(state) {
            state.exact_question = null;
        },

        clearResults(state) {
            state.results = null;
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
Vue.component('game-info', GameInfo);
Vue.component('game-users', GameUsers);
Vue.component('game-field', GameField);
Vue.component('game-question', GameQuestion);
Vue.component('game-exact-question', GameExactQuestion);
Vue.component('game-move', GameMove);
Vue.component('game-results', GameResults);


const app = new Vue({
    el: '#app',
    store
});

