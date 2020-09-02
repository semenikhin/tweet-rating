import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from '../views/Home';
import Tweet from '../views/Tweet';

Vue.use(VueRouter);

let router = new VueRouter({
    mode: 'history',
    routes: [{
            path: '/home',
            component: Home
        },
        {
            path: '/tweet',
            component: Tweet
        },
        {
            path: '*',
            redirect: '/home'
        }
    ],
});

export default router;