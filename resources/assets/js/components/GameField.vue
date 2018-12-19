<template>
    <div class="main" v-if="game && field">
        <div class="row no-gutters" v-for="(col, y) in field" :key="y">
            <div class="box" :class="'col-' + game.count_col" :style="{'background-color': box.color}"
                 v-for="(box, x) in col" :key="x"
                 @click="clickBox(box.x, box.y)">
                <div v-if="game.mode === 'base_mode'">
                    <span v-if="box.cost > 0">
                        <{{ box.cost }}>
                    </span>
                    <span v-if="box.base_guards_count > 0">
                        База<{{ box.base_guards_count }}>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .main {
        border: 0.05rem solid;
    }

    .box {
        border: 0.05rem solid;
        height: 10rem;
        background-color: white;
    }
</style>

<script>
    import {mapState} from 'vuex';

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
            clickBox(x, y) {
                console.log(x, y);
                if (!this.player) {
                    this.$notify({
                        text: 'Вы зашли как гость'
                    });
                    return;
                }
                let path = '/box/clicked';

                if (this.game.mode === 'base_mode') {
                    path = '/base/box/clicked';
                }
                axios.post('/games/' + this.game.id + path, {x, y, userColorId: this.player.id})
                    .then((response) => {
                        //console.log(response);
                        if (response.data.error) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });
            }
        }
    }
</script>

