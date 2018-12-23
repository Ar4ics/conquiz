<template>
    <div class="container">

        <game-info/>

        <game-users/>

        <chat-messages :game_id="game.id" :title="'Чат'"/>

        <game-move/>

        <game-question/>

        <game-exact-question/>

        <game-results/>

        <game-field/>

    </div>

</template>

<style scoped>

</style>

<script>
    import {mapMutations} from 'vuex';

    export default {
        props: ['game', 'player', 'field', 'whoMoves', 'winner', 'question', 'competitive_box', 'userColors'],

        created() {
            this.setGame(this.game);
            this.setPlayer(this.player);
            this.setField(this.field);
            if (this.question) {
                if (this.question.is_exact_answer) {
                    this.setExactQuestion(this.question);
                } else {
                    this.setQuestion(this.question);
                }
            }
            this.setWhoMoves(this.whoMoves);
            this.setCompetitiveBox(this.competitive_box);
            this.setUserColors(this.userColors);
            this.setWinner(this.winner);
        },

        mounted() {
            this.listenForEvents();
            if (!this.player) {
                this.$notify({
                    text: 'Вы зашли как гость'
                });
            }
        },
        methods: {
            ...mapMutations([
                'setGame',
                'setPlayer',
                'setField',
                'setUserColors',
                'setWinner',
                'setCompetitiveBox',
                'setQuestion',
                'setExactQuestion',
                'setWhoMoves',
                'setOnlineUsers',
                'addOnlineUser',
                'removeOfflineUser',
                'changeBoxColor',
                'clearQuestion',
                'clearExactQuestion',
                'clearResults',
                'setBase',
                'setAnswers',
                'setCompetitiveCorrectAnswers',
                'setCompetitiveAnswers',
                'replaceBox',
                'setCompetitiveBoxColor'
            ]),
            listenForEvents() {

                Echo.join('game_users.' + this.game.id)
                    .here((users) => {
                        console.log('users', users);
                        this.setOnlineUsers(users);
                        this.$notify({
                            text: `В комнате ${users.length} человек`
                        });
                    })
                    .joining((user) => {
                        console.log('joining', user);
                        this.addOnlineUser(user);
                        this.$notify({
                            text: `В комнату зашел ${user.name}`
                        });
                    })
                    .leaving((user) => {
                        console.log('leaving', user);
                        this.removeOfflineUser(user);
                        this.$notify({
                            text: `Из комнаты вышел ${user.name}`
                        });
                    });

                Echo.private('game.' + this.game.id)
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
                    })
                    .listen('AnswersResults', (e) => {
                        console.log('answer results', e);
                        this.setAnswers(e);
                        setTimeout(() => {
                            this.clearQuestion();
                            this.clearResults();
                        }, 5000);
                    });

                if (this.game.mode === 'base_mode') {
                    Echo.private('game.' + this.game.id)
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
                            setTimeout(() => {
                                this.clearQuestion();
                                this.clearExactQuestion();
                                this.clearResults();
                            }, 7000);
                        })
                        .listen('CorrectAnswers', (e) => {
                            console.log('correct answers', e);
                            this.setCompetitiveCorrectAnswers(e);
                            setTimeout(() => {
                                this.clearQuestion();
                                this.clearResults();
                            }, 7000);
                        })
                        .listen('NewExactQuestion', (e) => {
                            console.log('exact question', e);
                            this.setExactQuestion(e);
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
