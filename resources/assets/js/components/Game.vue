<template>
    <div class="container">
        <p>Комната: {{ game.title }}</p>
        <p>Участники:</p>
        <div class="row no-gutters" v-for="(u, i) in game.user_colors" :key="i.id">
            <p class="col-10">{{ u.user.name }}</p>
            <p class="col-2 player-square" :style="{ 'background-color': u.color }">Счет: {{ u.score }}</p>
        </div>
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
            <p>Ждем пока сходит {{ move.name }}</p>
        </div>

        <div class="main">
            <div class="row no-gutters" v-for="(col, y) in game.count_y" :key="y">
                <div class="box" :class="compute(x, y)" v-for="(row, x) in game.count_x" :key="x"
                     @click="clickBox(x, y)">
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .main {
        border: 2px solid;
    }

    .player-square {
        height: 3px;
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
                move: {
                    id: '',
                    name: ''
                },
                game: {
                    count_x: 0,
                    count_y: 0,
                    user_colors: []
                },
                count_col: 0,
                squares: this.boxes,
                question: Array.isArray(this.initialQuestion) ? null : this.initialQuestion,
                gamer: Array.isArray(this.player) ? null : this.player
            }
        },

        updated() {
            this.squares.forEach(e => {
                //$(`.box-${e.x}-${e.y}`).css('background-color', e.color);
                const x = this.$el.querySelector(`.b-${e.x}-${e.y}`);
                console.log(x);
                x.style.backgroundColor = e.color;
            });
        },

        mounted() {
            this.move = this.whoMoves;
            this.game = this.gameData;
            this.count_col = 12 / this.game.count_y;
            this.listenForEvents();

            if (!this.gamer) {
                this.$notify({
                    text: 'Вы зашли как гость'
                });
            }
        },

        methods: {

            compute(x, y) {
                return 'col-' + this.count_col + ' b-' + x + '-' + y;
            },

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
                        //$(`.box-${e.x}-${e.y}`).css('background-color', e.color);
                        const x = this.$el.querySelector(`.b-${e.x}-${e.y}`);
                        console.log(x);
                        x.style.backgroundColor = e.color;

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
                            //$(`.box-${e.x}-${e.y}`).css('background-color', 'white');
                            const x = this.$el.querySelector(`.b-${e.x}-${e.y}`);
                            console.log(x);
                            x.style.backgroundColor = 'white';
                        });
                        this.question = null;
                    });
            }


        }
    }
</script>
