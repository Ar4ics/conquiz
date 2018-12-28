<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Вход</div>

                    <div class="card-body">
                        <form @submit.prevent="login" method="post">
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Почта</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" v-model="email" class="form-control" name="email"
                                           required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" v-model="password" class="form-control"
                                           name="password" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-6 offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Войти
                                    </button>
                                    <a @click.prevent="loginGoogle" class="btn">
                                        <strong>Войти с Google</strong>
                                    </a>
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
        name: "Login",

        data() {
            return {
                email: null,
                password: null,
                error: false
            }
        },

        created() {
            if (this.$route.query.code) {
                this.$auth.oauth2({
                    code: true,
                    provider: 'google',
                    data: this.$route.query,
                    success: function (e) {
                        console.log(e);
                    },
                    error: function (e) {
                        console.log(e);
                    },
                });
            }
        },

        methods: {
            login() {
                this.$auth.login({
                    data: {email: this.email, password: this.password},
                    success: function (e) {
                        console.log(e);
                    },
                    error: function (e) {
                        console.log(e);

                    },
                    rememberMe: true,
                    redirect: '/',
                    fetchUser: true,
                });

            },

            loginGoogle() {
                this.$auth.oauth2({
                    provider: 'google',
                    params: {
                    },
                    rememberMe: true
                });
            }
        }
    }
</script>

<style scoped>

</style>