<template>
    <div class="card make-game">
        <div class="card-header text-center">Создание игры</div>
        <div class="card-body">
            <form @submit.prevent="createGame">
                <div class="form-group">
                    <label for="title">Название игры</label>
                    <input class="form-control" id="title" required type="text" v-model="title">
                </div>
                <div class="form-group">
                    <label for="x">Длина поля по x</label>
                    <input class="form-control" id="x" max="4" min="2" type="number" v-model="count_x"/>
                </div>
                <div class="form-group">
                    <label for="y">Длина поля по y</label>
                    <input class="form-control" id="y" max="4" min="2" type="number" v-model="count_y"/>
                </div>
                <div class="form-group">
                    <label>Игровой режим</label><br>
                    <label>
                        <input disabled type="radio" value="classic" v-model="mode">Классический
                    </label>

                    <label>
                        <input type="radio" value="base_mode" v-model="mode">Захват базы
                    </label>
                </div>
                <div class="form-group">
                    <label>Выберите одного или двух игроков</label>
                    <v-select v-model="users" value="id" label="name" multiple :options="initialUsers"/>
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-primary" type="submit">Создать</button>
                </div>
            </form>
        </div>
    </div>
</template>
<style scoped>
    .make-game {
        height: 550px;
    }
</style>
<script>

    export default {
        props: ['initialUsers'],

        data() {
            return {
                title: '',
                count_x: 2,
                count_y: 2,
                mode: 'base_mode',
                users: []
            }
        },

        computed: {},

        mounted() {

        },

        methods: {

            createGame() {
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
                if (this.count_x < 2 || this.count_x > 12 || this.count_y < 2 || this.count_y > 12) {
                    this.$notify({
                        text: 'Неверные размеры поля'
                    });
                    return;
                }

                this.axios.post('/games',
                    {
                        title: this.title,
                        users: this.users.map(u => u.id),
                        mode: this.mode,
                        count_x: this.count_x,
                        count_y: this.count_y
                    })
                    .then((response) => {
                        if (response.data.hasOwnProperty('error')) {
                            this.$notify({
                                text: response.data.error
                            });
                        }
                    });
                this.title = '';
                this.users = [];
            }
        }
    }
</script>
