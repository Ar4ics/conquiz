import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Login from './views/Login'
import Register from './views/Register'
import NotFound from './views/NotFound';
import GamesView from './views/GamesView';
import GameView from "./views/GameView";

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            redirect: '/games'
        },
        {
            path: '/games',
            name: 'games',
            component: GamesView,
            meta: {
                auth: true,
                title: 'Игры'
            }
        },
        {
            path: '/games/:id',
            name: 'game',
            component: GameView,
            meta: {
                auth: true,
                title: 'Игра'
            }
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
                auth: false,
                title: 'Авторизация'
            }
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
            meta: {
                auth: false,
                title: 'Регистрация'
            }
        },
        {
            path: '/404',
            component: NotFound,
            meta: {
                title: '404 Не найдено'
            }
        },
        {path: '*', redirect: '/404'},
    ],
});
router.beforeEach((to, from, next) => {
    document.title = to.meta.title;
    next();
});

export default router;