<template>
    <div class="card">
        <h6 class="card-header text-center">
            <game-users :game_id="game_id"/>
        </h6>
        <div ref="chat" class="chat-content">
            <div v-for="(g, i) in sorted_messages" :key="i" class="list-group list-group-flush">
                <span class="text-center sticky-top">{{ i }}</span>
                <chat-message :chatMessage="m" v-for="m in g" :key="m.i"></chat-message>
            </div>
        </div>
        <div class="card-footer">
            <input v-model="chat_message" type="text" placeholder="Сообщение" class="form-control"
                   @keyup.enter="submitMessage(chat_message)"/>
        </div>
    </div>
</template>

<style>
    .chat-content {
        overflow-y: scroll;
        height: 400px;
    }

    ::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }

</style>

<script>
    import {mapGetters, mapMutations} from 'vuex';

    export default {
        props: ['game_id', 'messages'],
        data() {
            return {
                chat_message: '',
            }
        },
        mounted() {

            this.setGameMessages(this.messages);

            Echo.private('game.' + this.game_id)
                .listen('GameMessageCreated', (e) => {
                    console.log('GameMessageCreated', e);
                    this.addGameMessage(e);
                });


        },

        computed: {

            ...mapGetters([
                'sorted_messages',
            ]),
        },

        updated() {
            this.$nextTick(() => this.scrollToEnd());
        },

        methods: {

            ...mapMutations([
                'setGameMessages',
                'addGameMessage'
            ]),

            submitMessage(message) {
                this.chat_message = '';
                console.log(message);
                axios.post('/games/' + this.game_id + '/message', {message})
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
