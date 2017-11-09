<template>
    <div class="card">
        <div class="card-header text-center">Создание игровой комнаты</div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="title">Название игры</label>
                    <input id="title" class="form-control" type="text" v-model="title">
                </div>
                <div class="form-group">
                    <label for="x">Длина поля по x</label>
                    <input id="x" class="form-control" min="1" max="5" type="number" v-model="count_x"/>
                </div>
                <div class="form-group">
                    <label for="y">Длина поля по y</label>
                    <input id="y" class="form-control" min="1" max="5" type="number" v-model="count_y"/>
                </div>
                <div class="form-group">
                    <label for="users">Выберите пользователей...</label>
                    <select class="form-control" v-model="users" multiple
                            id="users">
                        <option v-for="user in initialUsers" :key="user.id" :value="user.id">
                            {{ user.name }}
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
                users: []
            }
        },

        methods: {
            createGroup() {
                axios.post('/games',
                    {title: this.title, users: this.users, count_x: this.count_x, count_y: this.count_y})
                    .then((response) => {
                        this.title = '';
                        this.users = [];
                        //$('.selectpicker').selectpicker('deselectAll');
                        //Bus.$emit('gameCreated', response.data);
                    });
            }
        }
    }
</script>
