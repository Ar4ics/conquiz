<template>
    <game v-if="loaded"
          :game="game"
          :player="player"
          :field="field"
          :who-moves="who_moves"
          :question="question"
          :competitive-box="competitive_box"
          :user-colors="user_colors"
          :winner="winner"
          :messages="messages">
    </game>
</template>

<script>
    export default {
        props: [],

        data() {
            return {
                game: null,
                player: null,
                field: null,
                who_moves: null,
                question: null,
                competitive_box: null,
                user_colors: null,
                winner: null,
                messages: null,
                loaded: false
            }
        },

        created() {
            this.load();
        },

        methods: {
            load() {
                this.axios.get(`/games/${this.$route.params.id}`)
                    .then((response) => {
                        console.log(response.data);
                        this.game = response.data.game;
                        this.player = response.data.player;
                        this.field = response.data.field;
                        this.who_moves = response.data.who_moves;
                        this.question = response.data.question;
                        this.competitive_box = response.data.competitive_box;
                        this.user_colors = response.data.user_colors;
                        this.winner = response.data.winner;
                        this.messages = response.data.messages;
                        document.title = this.game.title;
                        this.loaded = true;
                    });
            },

        }

    }
</script>

<style scoped>

</style>