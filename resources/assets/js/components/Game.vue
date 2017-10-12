<template>
    <div class="container">
        <p>Комната: {{ game.title }}</p>

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
        <div v-else>
            <p>Участники:</p>
            <p v-for="(u, i) in game.user_colors" v-bind:key="i.id">
                {{ u.user.name }} -
                <span :style="{ 'background-color': u.color }">
                    {{ move.id === u.id ? 'ходит' : '' }}
                </span>
            </p>
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
                move: {
                    id: '',
                    name: ''
                },
                game: {
                    user_colors: []
                },
                squares: this.boxes,
                question: Array.isArray(this.initialQuestion) ? null : this.initialQuestion,
                gamer: Array.isArray(this.player) ? null : this.player
            }
        },
        mounted() {
            this.move = this.whoMoves;
            this.game = this.gameData;
            this.squares.forEach(e => {
                $(`.box-${e.x}-${e.y}`).css('background-color', e.color);

            });
            this.listenForEvents();

            if (!this.gamer) {
                this.$notify({
                    text: 'Вы зашли как гость'
                });
            }
        },

        methods: {
            clickBox(x, y) {
                if (!this.gamer) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                if (this.move.id !== this.gamer.id) {
                    this.$notify({
                        text: 'Сейчас ходит ' + this.move.name
                    });
                    return;
                }
                const userColorId = this.gamer.id;
                axios.post('/games/' + this.game.id + '/box/clicked', {x, y, userColorId})
                    .then((response) => {
                        console.log(response);
                        if (response.data.error && (response.data.code === 0)) {
                            this.$notify({
                                text: 'Это поле занято'
                            });
                        }

                    });

            },

            answer(userAnswer) {
                if (!this.gamer) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                const userColorId = this.gamer.id;
                const questionId = this.question.id;

                axios.post('/games/' + this.game.id + '/user/answered', {userAnswer, userColorId, questionId})
                    .then((response) => {

                    });
            },
            listenForEvents() {
                Echo.private('game.' + this.game.id)
                    .listen('BoxClicked', (e) => {
                        console.log('box clicked', e);
                        $(`.box-${e.x}-${e.y}`).css('background-color', e.color);

                    })
                    .listen('WhoMoves', (e) => {
                        console.log('who moves', e);
                        this.move = e;
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
                    });
            },

            review(x, y) {
                let n = 12 / this.cols;
                return 'col-lg-' + n + ' col-xs-' + n + ' box-' + x + '-' + y
            }
        }
    }
</script>
