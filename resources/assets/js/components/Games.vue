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
            <tr @click="watchGame(game.id)" v-for="(game, i) in games" :key="game.id">
                <th scope="row">{{ i + 1}}</th>
                <td>{{ game.title }}</td>
                <td>{{ game.user_colors_count }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['initialGames', 'user'],

    data () {
        return {
            games: []
        }
    },

    mounted () {
        this.games = this.initialGames;
        // Bus.$on('groupCreated', (group) => {
        //     this.groups.push(group);
        // });

        this.listenForNewGroups();
    },

    methods: {
        listenForNewGroups () {
            Echo.private('games')
                .listen('GameCreated', (e) => {
                    console.log(e);
                    this.games.push(e.game);
                });
        },
        watchGame (game) {
            window.location.href = '/games/' + game;
        }

    }
}
</script>
