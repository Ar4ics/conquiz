<template>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">Текущие игры</div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Игра</th>
                        <th scope="col">Название</th>
                        <th scope="col">Игроки</th>
                        <th scope="col">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr @click="watchGame(game.id)" v-for="(game, i) in orderedCurrentGames" :key="game.id">
                        <th scope="row">{{ i + 1}}</th>
                        <td>{{ game.id }}</td>
                        <td>{{ game.title }}</td>
                        <td>
                            <span class="player-name" v-for="(uc, i) in game.user_colors" :key="i.id">
                                {{ uc.user.name }}
                            </span>
                        </td>
                        <td>{{ game.start }} - {{ game.end }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header text-center">Завершенные игры</div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Комната</th>
                        <th scope="col">Победитель</th>
                        <th scope="col">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr @click="watchGame(game.id)" v-for="(game, i) in finished_games" :key="game.id">
                        <th scope="row">{{ i + 1}}</th>
                        <td>{{ game.title }}</td>
                        <td>{{ game.winner.user.name }}</td>
                        <td>{{ game.start }} - {{ game.end }}</td>
                    </tr>
                    </tbody>
                </table>
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
                current_games: [],
                finished_games: []
            }
        },

        computed: {
            orderedCurrentGames: function () {
                return _.orderBy(this.current_games, ['end'], ['desc'])
            }
        },

        mounted() {
            this.current_games = this.initialGames.filter(u => !u.stage3_has_finished);
            this.finished_games = this.initialGames.filter(u => u.stage3_has_finished);

            // Bus.$on('groupCreated', (group) => {
            //     this.groups.push(group);
            // });

            this.listenForNewGames();
        },

        methods: {
            listenForNewGames() {
                Echo.private('games')
                    .listen('GameCreated', (e) => {
                        console.log(e);
                        this.$notify({
                            text: `Игра №${e.game.id} создана`
                        });
                        e.game.user_colors = e.user_colors;
                        this.current_games.push(e.game);
                    });
            },
            watchGame(game) {
                window.location.href = '/games/' + game;
            }

        }
    }
</script>
