<template>
    <div class="container">

        <div class="row">

            <div class="col-12 order-1 col-md-6 order-md-0">
                <chat-messages :initial-messages="messages" :game_id="game.id"/>
            </div>

            <div class="col-12 order-0 col-md-6 order-md-1">
                <game-field/>

                <game-info/>

                <game-move/>

                <game-question/>
            </div>
        </div>

    </div>

</template>

<style scoped>


</style>

<script>
import {mapMutations, mapState} from 'vuex';

    export default {
        props: ['game', 'player', 'field', 'whoMoves', 'winner', 'question', 'competitiveBox', 'userColors', 'messages'],

        created() {
            this.setGame(this.game);
            this.setPlayer(this.player);
            this.setField(this.field);
            this.setQuestion(this.question);
            this.setWhoMoves(this.whoMoves);
            this.setCompetitiveBox(this.competitiveBox);
            this.setUserColors(this.userColors);
            this.setWinner(this.winner);
        },

        mounted() {
            if (!this.player) {
                this.$notify({
                    text: 'Вы зашли как гость'
                });
            }
            this.listenForEvents();
            this.logQuestion();
        },

        beforeDestroy() {
            Echo.leave('game.' + this.game.id);
        },

        computed: {
          ...mapState({
              questionState: 'question',
              competitiveQuestionState: 'competitive_question'
          })
        },

        methods: {
            ...mapMutations([
                'setGame',
                'setPlayer',
                'setField',
                'setUserColors',
                'setWinner',
                'setCompetitiveBox',
                'clearCompetitiveBox',
                'setQuestion',
                'setWhoMoves',
                'changeBoxColor',
                'clearQuestion',
                'clearCompetitiveQuestion',
                'clearResults',
                'setBase',
                'setAnswers',
                'setCompetitiveCorrectAnswers',
                'setCompetitiveAnswers',
                'replaceBox',
                'setAnswered'
            ]),

            logQuestion() {
                console.log('questionState', this.questionState);
                console.log('competitiveQuestionState', this.competitiveQuestionState);
            },

            getCurrentQuestionId() {
                let id = null;
                if (this.questionState !== null) {
                    id = this.questionState.id;
                } else if (this.competitiveQuestionState !== null) {
                    id = this.competitiveQuestionState.id;
                }
                return id;
            },

            needToClear(prevQuestionId) {
                const currentQuestionId = this.getCurrentQuestionId();
                const result = currentQuestionId === prevQuestionId;
                console.log('prevQuestionId', prevQuestionId);
                console.log('currentQuestionId', currentQuestionId);
                console.log('needToClear', result);
                return result;
            },

            listenForEvents() {

                Echo.channel('game.' + this.game.id)
                    .listen('WhoMoves', (e) => {
                        console.log('who moves', e);
                        this.setWhoMoves(e.user_color);
                    })
                    .listen('WinnerFound', (e) => {
                        console.log('winner', e);
                        this.setWinner(e.winner);
                    })
                    .listen('BoxClicked', (e) => {
                        console.log('box clicked', e);
                        this.replaceBox(e);
                    })
                    .listen('ShowCompetitiveBox', (e) => {
                        console.log('box clicked', e);
                        this.replaceBox({x: e.x, y: e.y, color: 'LightGrey'});
                    })
                    .listen('NewQuestion', (e) => {
                        console.log('new question', e);
                        this.setQuestion(e);
                        this.clearResults();
                        this.setAnswered(false);
                    })
                    .listen('AnswersResults', (e) => {
                        console.log('answer results', e);
                        this.setAnswers(e);
                        const questionId = this.getCurrentQuestionId();
                        setTimeout(() => {
                            this.logQuestion();
                            const clear = this.needToClear(questionId);
                            if (clear) {
                                this.clearQuestion();
                                this.clearResults();
                                this.setAnswered(false);
                            }
                        }, 5000);
                    });

                if (this.game.mode === 'base_mode') {
                    Echo.channel('game.' + this.game.id)
                        .listen('BaseCreated', (e) => {
                            console.log('base created', e);
                            this.setBase(e);
                        })
                        .listen('WhoAttack', (e) => {
                            console.log('who attack', e);
                            this.setCompetitiveBox(e.competitive_box);
                        })
                        .listen('CompetitiveAnswerResults', (e) => {
                            console.log('competitive answers', e);
                            this.setCompetitiveAnswers(e);
                            const questionId = this.getCurrentQuestionId();
                            setTimeout(() => {
                                this.logQuestion();
                                const clear = this.needToClear(questionId);
                                if (clear) {
                                    this.clearQuestion();
                                    this.clearCompetitiveQuestion();
                                    this.clearCompetitiveBox();
                                    this.clearResults();
                                    this.setAnswered(false);
                                }
                            }, 5000);
                        })
                        .listen('CorrectAnswers', (e) => {
                            console.log('correct answers', e);
                            this.setCompetitiveCorrectAnswers(e);
                        })
                        .listen('NewExactQuestion', (e) => {
                            console.log('exact question', e);
                            this.setQuestion(e);
                            this.clearResults();
                            this.setAnswered(false);
                        })
                        .listen('UserColorsChanged', (e) => {
                            console.log('user_colors changed', e);
                            this.setUserColors(e.user_colors);
                        });
                }
            }
        }
    }
</script>
