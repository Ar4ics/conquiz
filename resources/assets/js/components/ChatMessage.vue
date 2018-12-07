<template>
    <div class="row no-gutters">
        <div class="col-6 col-md-10">
            <span class="chat-name">{{ chatMessage.user.name }}</span>
            <span v-html="messageEmoji"></span>
        </div>
        <div class="col-6 col-md-2 text-right">
            <small>{{ chatMessage.date }}</small>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['chatMessage'],
        data() {
            return {
                message: this.chatMessage.message
            };
        },
        computed: {
            messageEmoji: function () {
                let emoji = this.message;
                emoji = emoji.replace('капа', '<img alt="kappa" src="/img/kappa.png"/>');
                emoji = emoji.replace('вин', '<img alt="win" src="/img/win.png"/>');
                emoji = emoji.replace('кеепо', '<img alt="win" src="/img/keepo.png"/>');
                emoji = emoji.replace('росс', '<img alt="win" src="/img/ross.png"/>');
                emoji = emoji.replace('чит', '<img alt="win" src="/img/cheat.png"/>');
                emoji = emoji.replace('де', '<img alt="win" src="/img/de.png"/>');
                emoji = emoji.replace('хил', '<img alt="win" src="/img/hil.png"/>');
                return emoji;
            }
        }
    }
</script>

<style scoped>

    .chat-name {
        padding: 1px;
        border-style: groove;
        border-width: 1px;
    }

</style>