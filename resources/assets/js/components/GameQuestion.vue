<template>
    <div class="card text-center" v-if="question && game">
        <div class="card-body">
            <p>{{ question.title }}</p>
            <div class="a" v-if="question.answers">
                <button v-bind:id="'a-' + i"
                        :class="getClass(i)"
                        v-for="(a, i) in question.answers"
                        v-bind:key="i"
                        v-on:click="answer(i)">
                    {{ a }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "GameQuestion",
        props: [],

        data() {
            return {}
        },

        computed: {
            ...mapState([
                'question',
                'player',
                'results',
                'game'
            ]),
        },

        mounted() {
        },

        methods: {

            getClass(i) {
                if (this.results) {
                    if (this.results.correct === i) {
                        return 'btn btn-success';
                    }
                }
                return 'btn btn-light';
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