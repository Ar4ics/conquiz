<template>
    <div>
        <div v-for="game in games" :key="game.id">
            <h4>{{ game.title }}</h4>
            <p>Игроков: {{ game.user_colors_count }}</p>
            <button @click="watchGame(game.id)">Посмотреть игру</button>
        </div>
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
