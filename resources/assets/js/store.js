import Vue from 'vue'
import Vuex from 'vuex';
Vue.use(Vuex);

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
        is_answered: false,
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
        }
    },
    mutations: {

        setGameMessages(state, messages) {
            Vue.set(state, 'messages', messages);
        },

        addGameMessage(state, message) {
            const group = state.messages.find(m => m.date === message.date);
            if (group) {
                group.messages.push(message);
            } else {
                state.messages.push({date: message.date, messages: [message]});
            }
        },

        setOnlineUsers(state, online_users) {
            Vue.set(state, 'online_users', online_users);
        },

        addOnlineUser(state, user) {
            state.online_users.push(user);
        },

        removeOfflineUser(state, user) {
            state.online_users = state.online_users.filter(o => o.id !== user.id);
        },

        setUserColors(state, user_colors) {
            Vue.set(state, 'user_colors', user_colors);
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

        replaceBox(state, box) {
            if (!state.field) {
                throw new Error('boxes is null');
            }
            state.field[box.y].splice(box.x, 1, box);
        },

        setBase(state, base) {
            state.field[base.y].splice(base.x, 1, base);
        },

        setQuestion(state, question) {
            if (question) {
                if (!question.answers) {
                    state.competitive_question = question;
                    state.question = null;
                } else {
                    state.question = question;
                    state.competitive_question = null;
                }
            } else {
                state.question = null;
                state.competitive_question = null;
            }
        },

        setWhoMoves(state, who_moves) {
            state.who_moves = who_moves;
        },
        setWinner(state, winner) {
            state.winner = winner;
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

        setAnswered(state, is_answered) {
            state.is_answered = is_answered;
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

    }
});

export default store;