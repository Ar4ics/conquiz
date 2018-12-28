<template>
    <div class="card chat-header">
        <div class="card-header text-center">
            <game-users :game_id="game_id"/>
        </div>
        <div ref="chat" class="chat-content">
            <div v-for="(g, i) in messages" :key="i" class="list-group list-group-flush">
                <span class="text-center sticky-top">{{ g.date }}</span>
                <chat-message :chatMessage="m" v-for="m in g.messages" :key="m.i"></chat-message>
            </div>
        </div>
        <div class="card-footer mt-auto">
            <input v-model="chat_message" type="text" placeholder="Сообщение" class="form-control"
                   @keyup.enter="submitMessage(chat_message)"/>
        </div>
    </div>
</template>

<style>
    .chat-header {
        height: 550px;
    }

    .chat-content {
        overflow-y: scroll;
    }

    ::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }

</style>

<script>
    import {mapState, mapMutations} from 'vuex';

    export default {
        props: ['game_id', 'initialMessages'],
        data() {
            return {
                chat_message: '',
            }
        },

        created() {
            this.setGameMessages(this.initialMessages);
        },

        mounted() {
            this.scrollToEnd();
            Echo.private('game.' + this.game_id)
                .listen('GameMessageCreated', (e) => {
                    console.log('GameMessageCreated', e);
                    this.addGameMessage(e);
                });
        },

        updated() {
            this.scrollToEnd();
        },

        beforeDestroy() {
            Echo.leave('game.' + this.game_id);
        },

        computed: {

            ...mapState([
                'messages',
            ]),
        },



        methods: {

            ...mapMutations([
                'setGameMessages',
                'addGameMessage'
            ]),

            submitMessage(message) {
                this.chat_message = '';
                console.log(message);
                this.axios.post(`/games/${this.game_id}/message`, {message})
                    .then((response) => {
                        console.log(response);
                        if (response.data.error) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });
            },

            scrollToEnd: function () {
                // scroll to the start of the last message
                this.$refs.chat.scrollTop = this.$refs.chat.scrollHeight;
            }
        }
    }
</script>
