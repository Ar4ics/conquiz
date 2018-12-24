<template>
    <div class="text-center" v-if="game">
        <div v-if="winner">
            <p class="alert alert-success">Игра завершена. Победитель {{ winner.user.name }}</p>
        </div>
        <div v-else>
            <div v-if="who_moves">
                <p class="alert alert-primary">
                    <span v-if="player && player.id === who_moves.id">
                        Ваш ход
                    </span>
                    <span v-else>
                        Ходит {{ who_moves.user.name }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "GameMove",

        computed: {

            ...mapState([
                'who_moves',
                'game',
                'winner',
                'player'
            ]),
        },

    }
</script>

<style scoped>

</style>