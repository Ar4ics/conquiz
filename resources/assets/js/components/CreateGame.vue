<template>
    <div class="panel panel-default">
        <div class="panel-heading">Создание комнаты</div>
        <div class="panel-body">
            <form>
                <div class="form-group">
                    <input class="form-control" type="text" v-model="title" placeholder="Имя комнаты">
                </div>
                <div class="form-group">
                    <select class="selectpicker" title="Выберите пользователей..." v-model="users" multiple id="friends">
                        <option v-for="user in initialUsers" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>
            </form>
        </div>
        <div class="panel-footer text-center">
            <button type="submit" @click.prevent="createGroup" class="btn btn-primary">Создать</button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['initialUsers'],

    data () {
        return {
            title: '',
            users: []
        }
    },

    methods: {
        createGroup () {
            axios.post('/games', { title: this.title, users: this.users })
                .then((response) => {
                    this.title = '';
                    this.users = [];
                    $('.selectpicker').selectpicker('deselectAll');
                    //Bus.$emit('gameCreated', response.data);
                });
        }
    }
}
</script>
