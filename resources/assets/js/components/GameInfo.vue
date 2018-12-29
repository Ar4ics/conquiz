<template>
    <div class="card" v-if="game && user_colors">
        <div class="card-body">
            <div class="row no-gutters" v-for="(u, i) in user_colors" :key="i.id">
                <div class="col-10">
                    <div class="row">
                        <div class="col-8">
                            {{ u.user.name }}
                        </div>
                        <div class="col-4 text-center">
                            <div v-if="u.place">{{ u.place }} место</div>
                        </div>
                    </div>
                </div>
                <div class="col-2 player-square" :style="{ backgroundColor: u.color }">{{ u.score }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "GameInfo",

        props: [],

        data() {
            return {}
        },

        computed: {
            ...mapState([
                'game',
                'user_colors'
            ]),
        },

    }
</script>

<style scoped>
    .player-square {
        height: 0.2rem;
    }
</style>