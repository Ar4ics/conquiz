<template>
    <div v-if="game && question_asked">
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <span class="modal-title">{{ competition }}</span>
                                <button type="button" class="close" aria-label="Close" @click="closeModal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center" v-if="question && game">
                                    <div>
                                        <p>{{ question.title }}</p>
                                        <div class="a" v-if="question.answers">
                                            <button
                                                    :disabled="!player"
                                                    v-bind:id="'a-' + i"
                                                    :class="getClass(i)"
                                                    v-for="(a, i) in question.answers"
                                                    v-bind:key="i"
                                                    v-on:click="answer(i)">
                                                {{ a }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center"
                                     v-if="exact_question && game && (!results || results.is_exact)">
                                    <div>
                                        <p>{{ exact_question.title }}</p>
                                        <div>
                                            <input :disabled="!player"
                                                   v-model.number="exact_answer" type="number" placeholder="Ответ"
                                                   class="form-control text-center"
                                                   @keyup.enter="answer(exact_answer)"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="results" class="modal-footer justify-content-center">
                                <game-results/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    import {mapGetters, mapMutations, mapState} from 'vuex';

    export default {
        name: "GameQuestion",
        props: [],

        data() {
            return {
                exact_answer: ''
            }
        },

        computed: {
            ...mapState([
                'question',
                'exact_question',
                'player',
                'results',
                'game'
            ]),


            ...mapGetters([
                'competition',
                'question_asked',
            ]),
        },

        mounted() {
        },

        methods: {

            ...mapMutations([
                'clearQuestion',
                'clearExactQuestion',
            ]),

            getClass(i) {
                if (this.results) {
                    if (this.results.correct === i) {
                        return 'btn btn-success';
                    }
                }
                return 'btn btn-light';
            },

            closeModal() {
                this.clearQuestion();
                this.clearExactQuestion();
            },

            answer(userAnswer) {
                console.log('answer', userAnswer);
                if (!this.player) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                this.exact_answer = '';
                const userColorId = this.player.id;

                const questionId = this.question ? this.question.id : this.exact_question.id;

                let path = '/user/answered';

                if (this.game.mode === 'base_mode') {
                    path = '/base/user/answered';
                }

                axios.post('/games/' + this.game.id + path, {userAnswer, userColorId, questionId})
                    .then((response) => {
                        if (response.data.hasOwnProperty('error')) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });
            },
        }
    }
</script>

<style scoped>

</style>