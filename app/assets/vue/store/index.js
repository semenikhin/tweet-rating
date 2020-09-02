import Vue from 'vue';
import Vuex from 'vuex';
import TweetModule from './tweet';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        tweet: TweetModule,
    },
});