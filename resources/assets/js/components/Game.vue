<template>
    <div class="container">
        {{ game.title }}
        <div v-for="(user, i) in game.users" :key="i">
            {{ user.name }} - {{ user.game_id ? 'готов' : 'не готов' }}
        </div>
        <p v-if="game.in_progress">Игра началась!</p>
        <button v-else @click="ready">Присоединиться к игре</button>
        <br/>
        <div class="row main">
            <div v-for="(row, x) in rows" :key="x">
                <div class="box" :class="review(x, y)" v-for="(col, y) in cols" :key="y" @click="clickBox(x, y)">
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.main {
    border: 2px solid;
}

.box {
    border: 2px solid;
    height: 150px;
    background-color: whitesmoke;
}
</style>

<script>
export default {
    props: ['gameData', 'user'],

    data () {
        return {
            rows: 3,
            cols: 4,
            game: {
                users: []
            }
        }
    },
    mounted () {
        this.game = this.gameData
        this.listenForClicks()
        //this.listenForGameStart()
    },

    methods: {
        clickBox (x, y) {
            //console.log(x, y);
            //$(`.box`).css('background-color', 'whitesmoke');
            //$(`.box-${x}-${y}`).css('background-color', 'yellow');
            axios.post('/games/' + this.game.id + '/box/clicked', { x, y })
                .then((response) => {

                });

        },

        listenForClicks () {
            Echo.private('game.' + this.game.id)
                .listen('BoxClicked', (e) => {
                    console.log(e);
                    $(`.box-${e.x}-${e.y}`).css('background-color', 'yellow');

                })
                .listen('UserIsReady', (e) => {
                    console.log(e);
                    this.game.in_progress = e.in_progress;
                    this.game.users = e.users;
                    //$(`.box-${e.x}-${e.y}`).css('background-color', 'yellow');

                })
                .listen('GameStarted', (e) => {
                    console.log(e, this.user);
                    //$(`.box-${e.x}-${e.y}`).css('background-color', 'yellow');

                });
        },


        ready () {
            axios.post('/games/' + this.game.id + '/user/ready')
        },
        review (x, y) {
            let n = 12 / this.cols
            return 'col-lg-' + n + ' col-xs-' + n + ' box-' + x + '-' + y
        }
    },

    computed: {

    }
}
</script>
