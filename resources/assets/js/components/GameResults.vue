<template>
    <div class="text-center" v-if="results">
        <div v-for="(a, i) in results.user_answers" :key="i">
            {{ a.user_name }} дал {{ a.is_correct ? 'правильный' : 'неправильный' }}
            {{ a.answer }} ответ, время {{ a.time }} с.
        </div>
        <div v-if="exact_question">
            Правильный ответ: {{ results.correct }}
        </div>
        <div v-if="results.winner">
            Побеждает  {{ results.winner.user.name }}
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "GameResults",

        computed: {
            ...mapState([
                'results',
                'exact_question'
            ]),
        },
    }
</script>

<style scoped>

</style>