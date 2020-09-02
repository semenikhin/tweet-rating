import TweetAPI from '../api/tweet';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        rootTweet: null,
        replies: []
    },
    getters: {
        isLoading(state) {
            return state.isLoading;
        },
        hasError(state) {
            return state.error !== null;
        },
        error(state) {
            return state.error;
        },
        hasRootTweet(state) {
            return state.rootTweet !== null;
        },
        rootTweet(state) {
            return state.rootTweet;
        },
        hasReplies(state) {
            return state.replies.length > 0;
        },
        replies(state) {
            return state.replies;
        },
    },
    mutations: {
        ['FETCHING_TWEET'](state) {
            state.isLoading = true;
            state.error = null;
            state.rootTweet = null;
        },
        ['FETCHING_TWEET_SUCCESS'](state, data) {
            state.isLoading = false;
            state.error = null;
            state.rootTweet = data.root;
            state.replies = data.all_replies;
        },
        ['FETCHING_TWEET_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.rootTweet = null;
        },
    },
    actions: {
        getTweet({
            commit
        }, data) {
            commit('FETCHING_TWEET');

            return TweetAPI.getTweet(data.tweetID, data.sortBy)
                .then(res => commit('FETCHING_TWEET_SUCCESS', res.data))
                .catch(err => commit('FETCHING_TWEET_ERROR', err));
        },
    },
}