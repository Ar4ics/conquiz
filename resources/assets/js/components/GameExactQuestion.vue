<template>
    <div>
        <div class="card text-center" v-if="exact_question && game">
            <h6 class="card-header">
                Вопрос с точным ответом
            </h6>
            <div class="card-body">
                <p>{{ exact_question.title }}</p>
                <div>
                    <input v-model="exact_answer" type="text" placeholder="Ответ" class="form-control text-center"
                           @keyup.enter="answer(exact_answer)"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "GameExactQuestion",
        props: [],

        data() {
            return {
                exact_answer: ''
            }
        },

        computed: {
            ...mapState([
                'exact_question',
                'player',
                'results',
                'game'
            ])
        },

        mounted() {
        },

        methods: {

            answer(userAnswer) {
                console.log('answer', userAnswer);
                if (!this.player) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                const userColorId = this.player.id;
                const questionId = this.exact_question.id;

                let path  = '/base/user/answered';

                axios.post('/games/' + this.game.id + path, {userAnswer, userColorId, questionId})
                    .then((response) => {
                        if (response.data.hasOwnProperty('error')) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });

                this.exact_answer = '';
            },
        }
    }
</script>

<style scoped>

</style>