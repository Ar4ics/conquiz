<template>
    <div class="card">
        <div class="card-header text-center">Создание игровой комнаты</div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="title">Название игры</label>
                    <input id="title" class="form-control" type="text" v-model="title" required>
                </div>
                <div class="form-group">
                    <label for="x">Длина поля по x</label>
                    <input id="x" class="form-control" min="2" max="4" type="number" v-model="count_x"/>
                </div>
                <div class="form-group">
                    <label for="y">Длина поля по y</label>
                    <input id="y" class="form-control" min="2" max="4" type="number" v-model="count_y"/>
                </div>
                <div class="form-group">
                    <label for="users">Выберите пользователей...</label>
                    <select class="form-control" v-model="users" multiple
                            id="users">
                        <option v-for="user in online_sorted" :key="user.id" :value="user.id">
                            {{ user.name }} - {{ user.status }}
                        </option>
                    </select>
                </div>
                <div class="form-group text-center">
                    <button type="submit" @click.prevent="createGroup" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['initialUsers'],

        data() {
            return {
                title: '',
                count_x: 2,
                count_y: 2,
                users: [],
                online_users: this.initialUsers
            }
        },

        mounted() {
            Echo.join('users')
                .here((users) => {
                    console.log('users', users);
                    users.forEach(u => {
                       let us = this.online_users.find(o => o.id === u.id);
                       if (us) {
                           us.status = 'online';
                       }
                    });

                    this.$notify({
                        text: `В зале ${users.length} человек`
                    });
                })
                .joining((user) => {
                    console.log('joining', user);
                    let us = this.online_users.find(o => o.id === user.id);
                    if (us) {
                        us.status = 'online';
                    }
                    this.$notify({
                        text: `В зал зашел ${user.name}`
                    });
                })
                .leaving((user) => {
                    console.log('leaving', user);
                    let us = this.online_users.find(o => o.id === user.id);
                    if (us) {
                        us.status = 'offline';
                    }
                    this.$notify({
                        text: `Из зала вышел ${user.name}`
                    });
                });
        },

        computed: {
            online_sorted: function () {
                return this.online_users.sort(function (a, b) {
                    if (a.status > b.status) {
                        return -1;
                    }
                    if (a.status < b.status) {
                        return 1;
                    }
                    return 0;
                });
            }
        },

        methods: {

            createGroup() {
                if (this.users.length < 1 || this.users.length > 2) {
                    this.$notify({
                        text: 'Выберите одного или двух игроков'
                    });
                    return;
                }
                if (this.title.trim() === '') {
                    this.$notify({
                        text: 'Введите название игры'
                    });
                    return;
                }
                axios.post('/games',
                    {title: this.title, users: this.users, count_x: this.count_x, count_y: this.count_y})
                    .then((response) => {
                        this.title = '';
                        this.users = [];

                        this.$notify({
                            text: 'Игра создана'
                        });
                        //$('.selectpicker').selectpicker('deselectAll');
                        //Bus.$emit('gameCreated', response.data);
                    });
            }
        }
    }
</script>
