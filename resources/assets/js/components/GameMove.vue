<template>
    <div class="text-center" v-if="game">
        <h6 class="card-header" v-if="competition">
            {{competition}}
        </h6>
        <div v-if="!question_asked">
            <div>
                <div v-if="winner" class="text-center">
                    <p class="alert alert-success">Игра завершена. Победитель {{ winner.user.name }}</p>
                </div>
                <div v-else>
                    <div v-if="who_moves" class="text-center">
                        <p class="alert alert-primary">Ждем хода игрока {{ who_moves.user.name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';

    export default {
        name: "GameMove",

        computed: {

            ...mapState([
                'who_moves',
                'game',
                'winner'
            ]),

            ...mapGetters([
                'competition',
                'question_asked',
            ]),
        },

    }
</script>

<style scoped>

</style>