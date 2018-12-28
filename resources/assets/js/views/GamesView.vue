<template>
    <div class="container" v-if="loaded">
        <div class="row no-gutters">
            <div class="col-12 col-md-6">
                <chat-messages :game_id="0" :messages="messages"></chat-messages>
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
            load() {
                this.axios.get('/games')
                    .then((response) => {
                        console.log(response.data);
                        this.messages = response.data.messages;
                        this.users = response.data.users;
                        this.games = response.data.games;
                        this.loaded = true;
                    });
            },

        }

    }
</script>