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
                emoji = emoji.replaceAll('kappa', '<img alt="kappa" src="/img/kappa.png"/>');
                emoji = emoji.replaceAll('win', '<img alt="win" src="/img/win.png"/>');
                emoji = emoji.replaceAll('keepo', '<img alt="keepo" src="/img/keepo.png"/>');
                emoji = emoji.replaceAll('ross', '<img alt="ross" src="/img/ross.png"/>');
                emoji = emoji.replaceAll('cheat', '<img alt="cheat" src="/img/cheat.png"/>');
                emoji = emoji.replaceAll('de', '<img alt="de" src="/img/de.png"/>');
                emoji = emoji.replaceAll('mini_k', '<img alt="mini_k" src="/img/mini_k.png"/>');
                emoji = emoji.replaceAll('sheep', '<img alt="sheep" src="/img/sheep.png"/>');
                emoji = emoji.replaceAll('hil', '<img alt="hil" src="/img/hil.png"/>');
                emoji = emoji.replaceAll('salt', '<img alt="salt" src="/img/salt.png"/>');
                return emoji;
            }
        }
    }
</script>
