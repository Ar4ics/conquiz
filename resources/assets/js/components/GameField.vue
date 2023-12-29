<template>
    <div class="card" v-if="game && field">
        <span class="card-header text-center">{{ game.title }}</span>
        <div class="row no-gutters" v-for="(col, y) in field" :key="y">
            <div class="box" :class="'col-' + game.count_col" :style="{backgroundColor: box.color}"
                 v-for="(box, x) in col" :key="x"
                 @click="clickBox(box.x, box.y)">
                <div v-if="game.mode === 'base_mode'">
                    <span v-if="box.user_color_id">
                        <{{ box.cost }}>
                    </span>
                    <br>
                    <span v-if="box.base">
                        {{ box.base.user_name }}<{{ box.base.guards }}>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .box {
        border: 0.01rem solid;
        height: 100px;
    }
</style>

<script>
import {mapMutations, mapState} from 'vuex';

    export default {
        name: "GameField",
        props: [],

        computed: {
            ...mapState([
                'game',
                'field',
                'player'
            ]),
        },

        mounted() {

        },

        methods: {
            ...mapMutations([
                'setError'
            ]),
            clickBox(x, y) {
                console.log(x, y);
                if (!this.player) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                let path = 'box/clicked';

                if (this.game.mode === 'base_mode') {
                    path = 'base/box/clicked';
                }
                this.axios.post(`/games/${this.game.id}/${path}`, {x, y, userColorId: this.player.id})
                    .then((response) => {
                        //console.log(response);
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
            }
        }
    }
</script>

