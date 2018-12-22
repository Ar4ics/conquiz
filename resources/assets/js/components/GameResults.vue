<template>
    <div class="card text-center list-group" v-if="results">
        <div class="list-group-item" v-for="(a, i) in results.user_answers" :key="i">
            <span>{{ a.user_name }}</span> ответил
            <span class="badge badge-primary" v-if="results.is_exact">
                {{ a.answer }}
            </span>
            <span class="badge badge-secondary" v-else>
                {{ a.answer + 1 }}
            </span>
            <span class="badge badge-success" v-if="a.is_correct">
                правильно
            </span>
            <span class="badge badge-danger" v-else>
                неправильно
            </span>
            <span class="badge badge-light">{{ a.time }}</span>
        </div>
        <div class="list-group-item list-group-item-light" v-if="results.is_exact">
            Правильный ответ <span class="badge badge-success">{{ results.correct }}</span>
        </div>
        <div class="list-group-item list-group-item-success" v-if="results.winner">
            Выиграл <span class="badge badge-dark">{{ results.winner.user.name }}</span>
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