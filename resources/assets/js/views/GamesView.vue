<template>
    <div class="container" v-if="loaded">
        <div class="row no-gutters">
            <div class="col-12 col-md-6">
                <chat-messages :game_id="0" :initial-messages="messages"></chat-messages>
            </div>
            <div class="col-12 col-md-6">
                <create-game :initial-users="users"></create-game>
            </div>
        </div>

        <div>
            <games :initial-games="games" :user="$auth.user()"></games>
        </div>
    </div>
</template>

<style scoped>

</style>

<script>
    export default {

        data() {
            return {
                messages: null,
                users: null,
                games: null,
                loaded: false
            }
        },

        created() {
            this.load();
        },

        methods: {
            async load() {
                const response = await this.axios.get('/games');
                this.users = response.data.users;
                this.games = response.data.games;

                const response2 = await this.axios.get('/games/0/message');
                this.messages = response2.data;
                this.loaded = true;
            },

        }

    }
</script>