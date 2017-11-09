<template>
    <div>
        <h5 class="text-center">Текущие игры</h5>
        <table class="table table-hover table-bordered">
            <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Название комнаты</th>
                <th scope="col">Кол-во игроков</th>
            </tr>
            </thead>
            <tbody>
            <tr @click="watchGame(game.id)" v-for="(game, i) in current_games" :key="game.id">
                <th scope="row">{{ i + 1}}</th>
                <td>{{ game.title }}</td>
                <td>{{ game.user_colors_count }}</td>
            </tr>
            </tbody>
        </table>

        <h5 class="text-center">Завершенные игры</h5>
        <table class="table table-hover table-bordered">
            <thead class="thead-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Название комнаты</th>
                <th scope="col">Кол-во игроков</th>
                <th scope="col">Победитель</th>
                <th scope="col">Счет</th>

            </tr>
            </thead>
            <tbody>
            <tr @click="watchGame(game.id)" v-for="(game, i) in finished_games" :key="game.id">
                <th scope="row">{{ i + 1}}</th>
                <td>{{ game.title }}</td>
                <td>{{ game.user_colors_count }}</td>
                <td>{{ game.winner.user.name }}</td>
                <td>{{ game.winner.score }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

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
