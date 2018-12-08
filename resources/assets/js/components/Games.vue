<template>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">Текущие игры</div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Комната</th>
                        <th scope="col">Игроки</th>
                        <th scope="col">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr @click="watchGame(game.id)" v-for="(game, i) in current_games" :key="game.id">
                        <th scope="row">{{ i + 1}}</th>
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

        mounted() {
            this.current_games = this.initialGames.filter(u => !u.stage3_has_finished);
            this.finished_games = this.initialGames.filter(u => u.stage3_has_finished);

            // Bus.$on('groupCreated', (group) => {
            //     this.groups.push(group);
            // });

            this.listenForNewGroups();
        },

        methods: {
            listenForNewGroups() {
                Echo.private('games')
                    .listen('GameCreated', (e) => {
                        console.log(e);
                        this.current_games.push(e);
                    });
            },
            watchGame(game) {
                window.location.href = '/games/' + game;
            }

        }
    }
</script>
