<template>
    <span>
        <span>Онлайн: </span>
        <span v-for="(u, i) in online_users" :key="i.id">
            {{ u.name }}
        </span>
    </span>
</template>

<script>
    import {mapMutations, mapState} from 'vuex';

    export default {
        name: "GameUsers",
        props: ['game_id'],
        computed: {
            ...mapState([
                'online_users'
            ]),
        },

        mounted() {
            this.listen();
        },

        beforeDestroy() {
            Echo.leave('users.' + this.game_id);
        },

        methods: {
            ...mapMutations([
                'setOnlineUsers',
                'addOnlineUser',
                'removeOfflineUser',
            ]),

            listen() {
                Echo.join('users.' + this.game_id)
                    .here((users) => {
                        console.log('users', users);
                        this.setOnlineUsers(users);
                        this.$notify({
                            text: `Здесь ${users.length} человек`
                        });
                    })
                    .joining((user) => {
                        console.log('joining', user);
                        this.addOnlineUser(user);
                        this.$notify({
                            text: `Зашел ${user.name}`
                        });
                    })
                    .leaving((user) => {
                        console.log('leaving', user);
                        this.removeOfflineUser(user);
                        this.$notify({
                            text: `Вышел ${user.name}`
                        });
                    });
            }

        },

    }
</script>

<style scoped>

</style>