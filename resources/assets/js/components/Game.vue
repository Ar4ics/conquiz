<template>
    <div class="container">
        <div class="card bg-light">
            <h5 class="card-header text-center">{{ game.title }}</h5>
            <div class="card-body">

                <div class="row no-gutters" v-for="(u, i) in game.user_colors" :key="i.id">
                    <div class="col-8 col-md-10">{{ u.user.name }}</div>
                    <div class="col-4 col-md-2 player-square" :style="{ 'background-color': u.color }">{{ u.score }}
                        баллов
                    </div>
                </div>
            </div>
        </div>
        <div class="card bg-white">
            <h5 class="card-header text-center">Кто здесь</h5>
            <div class="card-body">
                <span class="row no-gutters" v-for="(u, i) in online_users" :key="i.id">
                    {{ u.name }}
                </span>
            </div>
        </div>
        <chat-messages :game_id="game.id" :title="'Чат'"/>

        <div v-if="question">
            <div class="card text-center">
                <h6 class="card-header">
                    Вопрос
                </h6>
                <div class="card-body">
                    <p>{{ question.title }}</p>
                    <div class="a">
                        <button v-bind:id="'a-' + i"
                                class="btn btn-light"
                                v-for="(a, i) in question.answers"
                                v-bind:key="i"
                                v-on:click="answer(i)"
                        >
                            {{ a }}
                        </button>
                    </div>
                    <div v-if="answers.length > 0">
                        <div v-for="(a, i) in answers" :key="i">
                            {{ a.name }} дал {{ a.is_correct ? 'правильный' : 'неправильный' }} {{ a.ans }} ответ
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div v-if="winner" class="text-center">
                <p class="alert alert-success">Игра завершена. Победитель {{ winner.name }}</p>
            </div>
            <div v-else>
                <div v-if="move" class="text-center">
                    <p class="alert alert-primary">Ждем хода игрока {{ move.name }}</p>
                </div>
            </div>
        </div>

        <div class="main">
            <div class="row no-gutters" v-for="(col, y) in game.count_y" :key="y">
                <div class="box" :class="review(x, y)" v-for="(row, x) in game.count_x" :key="x"
                     @click="clickBox(x, y)">
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>
    .main {
        border: 0.05rem solid;
    }

    .player-square {
        height: 0.05rem;
    }

    .box {
        border: 0.05rem solid;
        height: 10rem;
        background-color: white;
    }
</style>

<script>
    export default {
        props: ['gameData', 'player', 'boxes', 'whoMoves', 'initialQuestion', 'competitiveBox'],

        data() {
            return {
                move: {
                    id: '',
                    name: ''
                },
                game: {
                    count_x: 0,
                    count_y: 0,
                    winner_user_color_id: 0,
                    user_colors: []
                },
                answers: [],
                count_col: 0,
                question: this.initialQuestion,
                common_box: this.competitiveBox,
                winner: null,
                online_users: []
            }
        },

        created() {
            this.game = this.gameData;
            this.move = this.whoMoves;
            if (this.game.winner_user_color_id) {
                let userColor = this.game.user_colors.filter(u => u.id === this.game.winner_user_color_id)[0];
                this.winner = userColor.user;
            }
            this.count_col = 12 / this.game.count_x;
        },

        mounted() {
            this.listenForEvents();
            if (!this.player) {
                this.$notify({
                    text: 'Вы зашли как гость'
                });
            }
            this.boxes.forEach(e => {
                $(`.b-${e.x}-${e.y}`).css('background-color', e.color);
            });
            if (this.common_box) {
                $(`.b-${this.common_box.x}-${this.common_box.y}`).css('background-color', 'grey');
            }

            Echo.join('game_users.' + this.game.id)
                .here((users) => {
                    console.log('users', users);
                    this.online_users = users;
                    this.$notify({
                        text: `В комнате ${users.length} человек`
                    });
                })
                .joining((user) => {
                    console.log('joining', user);
                    this.online_users.push(user);
                    this.$notify({
                        text: `В комнату зашел ${user.name}`
                    });
                })
                .leaving((user) => {
                    console.log('leaving', user);
                    this.online_users = this.online_users.filter(o => o.id !== user.id);
                    this.$notify({
                        text: `Из комнаты вышел ${user.name}`
                    });
                });

        },
        methods: {

            review(x, y) {
                return 'col-' + this.count_col + ' b-' + x + '-' + y;
            },

            clickBox(x, y) {
                if (!this.player) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                const userColorId = this.player.id;
                axios.post('/games/' + this.game.id + '/box/clicked', {x, y, userColorId})
                    .then((response) => {
                        //console.log(response);
                        if (response.data.error) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });
            },


            answer(userAnswer) {
                console.log('answer', userAnswer);
                if (!this.player) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                const userColorId = this.player.id;
                const questionId = this.question.id;

                axios.post('/games/' + this.game.id + '/user/answered', {userAnswer, userColorId, questionId})
                    .then((response) => {
                        if (response.data.error) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });
            },
            listenForEvents() {
                Echo.private('game.' + this.game.id)
                    .listen('BoxClicked', (e) => {
                        console.log('box clicked', e);
                        $(`.b-${e.x}-${e.y}`).css('background-color', e.color);
                    })
                    .listen('ShowCompetitiveBox', (e) => {
                        console.log('box clicked', e);
                        $(`.b-${e.x}-${e.y}`).css('background-color', 'grey');
                    })
                    .listen('WhoMoves', (e) => {
                        console.log('who moves', e);
                        this.move = e;
                    })
                    .listen('WinnerFound', (e) => {
                        console.log('winner', e);
                        let userColor = this.game.user_colors.filter(u => u.id === e.winner.id)[0];
                        this.winner = userColor.user;
                    })
                    .listen('NewQuestion', (e) => {
                        console.log('new question', e);
                        this.question = e;
                    })
                    .listen('AnswersResults', (e) => {
                        console.log('answer results', e);
                        e.results.forEach(r => {
                            let userColor = this.game.user_colors.filter(u => u.id === r.user_color_id)[0];
                            userColor.score = r.score;
                            this.answers.push({name: userColor.user.name, ans: r.answer + 1, is_correct: r.is_correct});
                        });
                        $(`#a-${e.correct}`).attr('class', 'btn btn-success');
                        e.boxes.forEach(e => {
                            $(`.b-${e.x}-${e.y}`).css('background-color', 'white');
                        });
                        setTimeout(() => {
                            this.answers = [];
                            this.question = null;
                            //$(`.a > .btn`).attr('class', 'btn btn-info');
                        }, 5000);
                    });
            }


        }
    }
</script>
