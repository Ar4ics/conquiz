<template>
    <div class="list-group-item flex-column">
        <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-1 message-name">{{ chatMessage.user.name }}</h6>
            <small>{{ chatMessage.time }}</small>
        </div>
        <span v-html="messageEmoji"></span>
    </div>
</template>


<style scoped>
    .message-name {
        padding: 1px;
        border-style: groove;
        border-width: 1px;
    }
</style>

<script>
    export default {
        props: ['chatMessage'],

        data() {
            return {
                message: this.chatMessage.message
            };
        },

        methods: {},

        computed: {
            messageEmoji: function () {
                let emoji = this.message;
                emoji = emoji.replace('kappa', '<img alt="kappa" src="/img/kappa.png"/>');
                emoji = emoji.replace('win', '<img alt="win" src="/img/win.png"/>');
                emoji = emoji.replace('keepo', '<img alt="win" src="/img/keepo.png"/>');
                emoji = emoji.replace('ross', '<img alt="win" src="/img/ross.png"/>');
                emoji = emoji.replace('cheat', '<img alt="win" src="/img/cheat.png"/>');
                emoji = emoji.replace('hil', '<img alt="win" src="/img/hil.png"/>');
                emoji = emoji.replace('salt', '<img alt="win" src="/img/salt.png"/>');
                return emoji;
            }
        }
    }
</script>
