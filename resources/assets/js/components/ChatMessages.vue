<template>
    <div class="card">
        <h5 class="card-header text-center">{{ title }}</h5>
        <div ref="chat" class="card-body chat-content">
            <div v-for="(g, i) in grouped" :key="i">
                <h5 class="card-title text-center">{{ i }}</h5>
                <div v-for="m in g" :key="m.i">
                    <chat-message :chatMessage="m"></chat-message>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <input v-model="chat_message" type="text" placeholder="Сообщение" class="form-control"
                   @keyup.enter="submitMessage"/>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title', 'game_id'],
        data() {
            return {
                chat_message: '',
                messages: []
            }
        },
        mounted() {
            this.listenForEvents();
            this.loadMessages();
        },

        updated() {
            this.$nextTick(() => this.scrollToEnd());
        },

        computed: {
            grouped: function () {
                return _.groupBy(this.messages, 'date');
            }
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
                axios.post('/games/' + this.game_id + '/message', {message: this.chat_message})
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
                this.$refs.chat.scrollTop = this.$refs.chat.scrollHeight;
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