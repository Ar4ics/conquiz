<template>
    <div v-if="game && is_question_exists">
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
                                <div class="text-center">
                                    <div v-if="competitive_question">
                                        <p>{{ competitive_question.title }}</p>
                                        <input :disabled="disabledIf()"
                                               v-model.number="exact_answer" type="number" placeholder="Ответ"
                                               class="form-control text-center"
                                               @keyup.enter="answer(exact_answer)"/>
                                    </div>
                                    <div v-if="question" class="row">
                                        <p class="col-12 text-center">{{ question.title }}</p>
                                        <button class="col-12"
                                                :disabled="disabledIf()"
                                                :class="getClass(i)"
                                                v-for="(a, i) in question.answers"
                                                :key="i"
                                                @click="answer(i)">
                                            {{ a }}
                                        </button>
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
                exact_answer: '',
            }
        },

        computed: {
            ...mapState([
                'question',
                'competitive_question',
                'player',
                'results',
                'game',
                'competitive_box',
                'is_answered'
            ]),


            ...mapGetters([
                'competition',
                'is_question_exists',
            ]),
        },

        mounted() {
        },

        methods: {

            disabledIf() {
                return !this.player || this.is_answered ||
                    (this.competitive_box &&
                        this.competitive_box.competitors.length > 0 &&
                        !this.competitive_box.competitors.includes(this.player.id));
            },

            ...mapMutations([
                'setAnswered',
                'clearQuestion',
                'clearCompetitiveQuestion',
                'setError',
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
                this.clearCompetitiveQuestion();
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
                this.setAnswered(true);

                const userColorId = this.player.id;
                const questionId = this.question ? this.question.id : this.competitive_question.id;

                let path = 'user/answered';

                if (this.game.mode === 'base_mode') {
                    path = 'base/user/answered';
                }

                this.axios.post(`/games/${this.game.id}/${path}`, {userAnswer, userColorId, questionId})
                    .then((response) => {
                        if (response.data.hasOwnProperty('error')) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    })
                    .catch(error => {
                        console.log(error.response.data.message);
                        this.setError(error.response.data.message);
                    });
            },
        }
    }
</script>

<style scoped>

</style>