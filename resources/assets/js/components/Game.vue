<template>
    <div class="container">
        <h5 class="text-center">Комната: {{ game.title }}</h5>
        <div class="card bg-light">
            <div class="card-header">Участники</div>
            <div class="card-body">

                <div class="row no-gutters" v-for="(u, i) in game.user_colors" :key="i.id">
                    <div class="col-10">{{ u.user.name }}</div>
                    <div class="col-2 player-square" :style="{ 'background-color': u.color }">Счет: {{ u.score }}</div>
                </div>
            </div>
        </div>
        <div v-if="question">
            <div class="card text-center">
                <h6 class="card-header">
                    Вопрос
                </h6>
                <div class="card-body">
                    <p>{{ question.title }}</p>
                    <div class="a">
                        <button id="a-0" class="btn btn-info" v-on:click="answer(0)">{{ question.a }}</button>
                        <button id="a-1" class="btn btn-info" v-on:click="answer(1)">{{ question.b }}</button>
                        <button id="a-2" class="btn btn-info" v-on:click="answer(2)">{{ question.c }}</button>
                        <button id="a-3" class="btn btn-info" v-on:click="answer(3)">{{ question.d }}</button>
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
            <p class="alert alert-primary">Ждем пока сходит {{ move.name }}</p>
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
                answers: [],
                count_col: 0,
                question: Array.isArray(this.initialQuestion) ? null : this.initialQuestion,
                gamer: Array.isArray(this.player) ? null : this.player
            }
        },

        created() {
            this.move = this.whoMoves;
            this.game = this.gameData;
            this.count_col = 12 / this.game.count_y;
        },

        mounted() {

            this.listenForEvents();
            if (!this.gamer) {
                this.$notify({
                    text: 'Вы зашли как гость'
                });
            }
            this.boxes.forEach(e => {
                $(`.b-${e.x}-${e.y}`).css('background-color', e.color);
            });

        },

        methods: {

            review(x, y) {
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
                        $(`.b-${e.x}-${e.y}`).css('background-color', e.color);

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
                            $(`.a > .btn`).attr('class', 'btn btn-info');
                        }, 5000);
                    });
            }


        }
    }
</script>
