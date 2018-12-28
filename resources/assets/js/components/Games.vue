<template>
    <div class="row no-gutters text-center">
        <div class="card col-12 col-md-6">
            <div class="card-header">Текущие игры</div>
            <div class="card-body">
                <div class="table-responsive">

                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">№</th>
                        <th scope="col">Название</th>
                        <th scope="col">Игроки</th>
                        <th scope="col">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr @click="watchGame(game)" v-for="(game, i) in orderedCurrentGames" :key="game.id">
                        <th scope="row">{{ i + 1}}</th>
                        <td>{{ game.id }}</td>
                        <td>{{ game.title }}</td>
                        <td>
                            <span class="player-name" v-for="(uc, i) in game.user_colors" :key="i.id">
                                {{ uc.user.name }}
                            </span>
                        </td>
                        <td>{{ game.date }}</td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="card col-12 col-md-6">
            <div class="card-header text-center">Завершенные игры</div>
            <div class="card-body">
                <div class="table-responsive">

                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">№</th>
                        <th scope="col">Победитель</th>
                        <th scope="col">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr @click="watchGame(game)" v-for="(game, i) in finishedGames" :key="game.id">
                        <th scope="row">{{ i + 1}}</th>
                        <td>{{ game.id }}</td>
                        <td>{{ game.winner.user.name }}</td>
                        <td>{{ game.date }}</td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .player-name {
        padding: 1px;
        border-style: groove;
        border-width: 1px;
    }
</style>

<script>
    export default {
        props: ['initialGames', 'user'],

        data() {
            return {
                games: this.initialGames
            }
        },

        computed: {
            orderedCurrentGames: function () {
                return _.orderBy(this.games.filter(g => !g.winner_user_color_id), ['end'], ['desc'])
            },
            finishedGames: function () {
                return this.games.filter(g => g.winner_user_color_id)
            }
        },

        mounted() {
            this.listenForNewGames();
        },

        beforeDestroy() {
            Echo.leave('games');
        },

        methods: {
            listenForNewGames() {
                Echo.private('games')
                    .listen('GameCreated', (e) => {
                        console.log(e);
                        this.$notify({
                            text: `Игра №${e.game.id} создана`
                        });
                        this.games.push(e.game);
                    });
            },
            watchGame(game) {
                this.$router.push({name: 'game', params: {id: game.id}});
            }

        }
    }
</script>
