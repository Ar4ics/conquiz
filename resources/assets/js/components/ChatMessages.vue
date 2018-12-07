<template>
    <div class="chat-content">
        <div v-for="(u, i) in messages" :key="i.id">
            <chat-message :chatMessage="u"></chat-message>
        </div>
        <div style="padding-top: 10px">
            <input v-model="chat_message" type="text" placeholder="Сообщение" class="form-control"
                   @keyup.enter="submitMessage"/>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['game_id'],
        data() {
            return {
                chat_message: '',
                messages: [],
            }
        },
        mounted() {
            this.listenForEvents();
            this.loadMessages();
        },
        updated() {
            // whenever data changes and the component re-renders, this is called.
            this.$nextTick(() => this.scrollToEnd());
        },
        methods: {

            loadMessages() {
                axios.get('/games/' + this.game_id + '/message')
                    .then((response) => {
                        console.log(response);
                        if (response.data.error) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                        this.messages = response.data;
                    });
            },

            submitMessage() {
                console.log(this.chat_message);
                axios.post('/games/' + this.game_id + '/message', { message: this.chat_message })
                    .then((response) => {
                        console.log(response);
                        if (response.data.error) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });
                this.chat_message = '';
            },

            listenForEvents() {
                Echo.private('game.' + this.game_id)
                    .listen('GameMessageCreated', (e) => {
                        console.log('GameMessageCreated', e);
                        this.messages.push(e);
                    });
            },

            scrollToEnd: function () {
                // scroll to the start of the last message
                this.$el.scrollTop = this.$el.lastElementChild.offsetTop;
            }
        }
    }
</script>

<style scoped>
    .chat-content {
        overflow-y: scroll;
        max-height: 200px;
    }
    ::-webkit-scrollbar {
        width: 0px;
        background: transparent; /* make scrollbar transparent */
    }
</style>