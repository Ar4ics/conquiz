<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Регистрация</div>

                    <div class="card-body">
                        <form @submit.prevent="register" method="post">

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Ник</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" v-model="name" class="form-control" name="name" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Почта</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" v-model="email" class="form-control" name="email" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" v-model="password" class="form-control" name="password" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Повторить</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" v-model="password_confirmation" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Зарегаться
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Register",
        data() {
            return {
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
                error: false
            }
        },

        methods: {
            register() {
                this.$auth.register({
                    data: {name: this.name, email: this.email, password: this.password, password_confirmation: this.password_confirmation},
                    success: function (e) {
                        console.log(e);
                    },
                    error: function (e) {
                        console.log(e);

                    },
                    autoLogin: true,
                    rememberMe: true,
                    redirect: '/',
                });

            }
        }
    }
</script>

<style scoped>

</style>