<template>
    <div v-if="$auth.ready()" style="padding-top: 3.5rem;">
        <notifications
                :duration="3000"
                animation-name="v-fade-left"
                position="top left">
            <template slot="body" slot-scope="props">
                <div class="custom-template">
                    <div class="custom-template-icon">
                        <i class="icon ion-android-checkmark-circle"></i>
                    </div>
                    <div class="custom-template-content">
                        <div class="custom-template-title">
                            {{props.item.title}}
                            <div class="custom-template-text"
                                 v-html="props.item.text"></div>
                        </div>
                        <div class="custom-template-close"
                             @click="props.close">
                            <i class="icon ion-android-close"></i>
                        </div>
                    </div>
                </div>
            </template>
        </notifications>
        <nav class="navbar navbar-expand-md bg-light navbar-light fixed-top">
            <router-link class="navbar-brand" :to="{name: 'home'}">Конквиз</router-link>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">

                </ul>
                <div v-if="!$auth.check()">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{name: 'login'}">Вход</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{name: 'register'}">Регистрация</router-link>
                        </li>
                    </ul>
                </div>
                <div v-else>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $auth.user().name }} <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="#"
                                   @click="logout">
                                    Выйти
                                </a>
                            </div>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
        <div class="alert alert-danger" v-if="error">
            <a href="#" class="close" @click="setError(null)" aria-label="close">&times;</a>
            Ошибка: {{error}}
        </div>
        <router-view></router-view>
    </div>
</template>
<script>
import {mapMutations, mapState} from "vuex";

    export default {

        computed: {
            ...mapState(['error'])
        },

        methods: {
            ...mapMutations([
                'setError'
            ]),
            logout() {
                this.$auth.logout({
                    makeRequest: true,
                    success() {
                        console.log('success ' + this.context);
                    },
                    error() {
                        console.log('error ' + this.context);
                    },
                    redirect: '/login'
                });
            },

        }

    }
</script>