<template>
    <div class="container">
        <p>Комната: {{ game.title }}</p>
        <p>Участники:</p>
        <p v-if="!question" v-for="u in game.user_colors">
            {{ u.user.name }} -
            <span :style="{ 'background-color': u.color }">
                {{ move.user_id === u.user_id ? 'ходит' : '' }}
            </span>
        </p>
        <div v-if="question">
            <p>{{ question.title }}</p>
            <p>Варианты ответов:</p>
            <div>
                <button v-on:click="answer(0)">{{ question.a }}</button>
                <button v-on:click="answer(1)">{{ question.b }}</button>
                <button v-on:click="answer(2)">{{ question.c }}</button>
                <button v-on:click="answer(3)">{{ question.d }}</button>
            </div>
        </div>

        <div class="row main">
            <div v-for="(row, y) in rows" :key="y">
                <div class="box" :class="review(x, y)" v-for="(col, x) in cols" :key="x" @click="clickBox(x, y)">
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .main {
        border: 2px solid;
    }

    .box {
        border: 2px solid;
        height: 150px;
        background-color: white;
    }
</style>

<script>
    export default {
        props: ['gameData', 'player', 'boxes', 'whoMoves', 'initialQuestion'],

        data() {
            return {
                rows: 3,
                cols: 4,
                move: this.whoMoves,
                game: this.gameData,
                question: null
            }
        },
        mounted() {
            this.question = (this.initialQuestion === 'empty') ? null : this.initialQuestion;
            this.boxes.forEach(e => {
                $(`.box-${e.x}-${e.y}`).css('background-color', e.color);

            });
            this.listenForClicks();
            //this.listenForGameStart()
        },

        methods: {
            clickBox(x, y) {
//                console.log(x, y);
//                $(`.box`).css('background-color', 'whitesmoke');
//                $(`.box-${x}-${y}`).css('background-color', 'yellow');
                if (!(this.player === 'guest')) {
                    const userColorId = this.player.id;
                    axios.post('/games/' + this.game.id + '/box/clicked', {x, y, userColorId})
                        .then((response) => {

                        });
                }

            },

            answer(userAnswer) {
                if (!(this.player === 'guest')) {
                    const userColorId = this.player.id;
                    const questionId = this.question.id;

                    axios.post('/games/' + this.game.id + '/user/answered', {userAnswer, userColorId, questionId})
                        .then((response) => {

                        });
                }
            },

            listenForClicks() {
                Echo.private('game.' + this.game.id)
                    .listen('BoxClicked', (e) => {
                        console.log('box clicked', e);
                        $(`.box-${e.x}-${e.y}`).css('background-color', e.color);

                    })
                    .listen('WhoMoves', (e) => {
                        console.log('who moves', e);
                        this.move = e.who_moves;
                    })
                    .listen('NewQuestion', (e) => {
                        console.log('new question', e);
                        this.question = e;
                    })
                    .listen('AnswersResults', (e) => {
                        console.log('answer results', e);
                        const boxes = e.boxes;
                        boxes.forEach(e => {
                            $(`.box-${e.x}-${e.y}`).css('background-color', 'white');
                        });

                        this.question = null;

                    })
                    .listen('UserIsReady', (e) => {
                        console.log('user ready', e);
                        let index = this.game.user_colors.findIndex((uc) => uc.user_id === e.user.id);
                        this.game.user_colors[index] = e.user;
                    })
                    .listen('GameStarted', (e) => {
                        console.log(e, this.user);
                    });
            },


            ready() {
                axios.post('/games/' + this.game.id + '/user/ready')
            },
            review(x, y) {
                let n = 12 / this.cols
                return 'col-lg-' + n + ' col-xs-' + n + ' box-' + x + '-' + y
            }
        },

        computed: {}
    }
</script>
